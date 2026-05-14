/* ═══════════════════════════════════════════════════════════════════════════
   ANIME-CHARACTER.JS
   Compatible with Three.js r128 — no CapsuleGeometry, no Object.assign
   on read-only mesh properties.
   ═══════════════════════════════════════════════════════════════════════════ */

const AnimeCharacter = (() => {
  const MODEL_URL = "/assets/models/character.glb";
  const USE_FALLBACK = true; // set false when you place a real .glb

  let mixer = null;
  let model = null;
  let headBone = null;
  let neckBone = null;
  let leftEyeBone = null;
  let rightEyeBone = null;
  let leftArmBone = null;
  let rightArmBone = null;

  let blinkTimer = 0;
  let blinkState = "open";
  let blinkNext = 3;
  let breathPhase = 0;
  let waving = false;
  let waveCooldown = 0;
  let wavePhase = 0;
  let pulseScale = 1;
  let keyTiltX = 0;
  let keyTiltY = 0;

  let targetHeadX = 0,
    targetHeadY = 0;
  let currentHeadX = 0,
    currentHeadY = 0;

  const LERP = 0.06;
  const lerp = (a, b, t) => a + (b - a) * t;

  let proceduralParts = null;

  // ── Capsule substitute (r128 compatible) ──────────────────────────────
  // Builds a rounded-cylinder shape from a cylinder + two hemisphere caps
  function makeCapsule(radiusTop, radiusBottom, height, mat) {
    const group = new THREE.Group();

    // Cylinder body
    const cyl = new THREE.Mesh(
      new THREE.CylinderGeometry(radiusTop, radiusBottom, height, 10),
      mat,
    );
    group.add(cyl);

    // Top cap (hemisphere)
    const capTop = new THREE.Mesh(
      new THREE.SphereGeometry(
        radiusTop,
        10,
        6,
        0,
        Math.PI * 2,
        0,
        Math.PI / 2,
      ),
      mat,
    );
    capTop.position.y = height / 2;
    group.add(capTop);

    // Bottom cap (hemisphere, flipped)
    const capBot = new THREE.Mesh(
      new THREE.SphereGeometry(
        radiusBottom,
        10,
        6,
        0,
        Math.PI * 2,
        Math.PI / 2,
        Math.PI / 2,
      ),
      mat,
    );
    capBot.position.y = -height / 2;
    group.add(capBot);

    return group;
  }

  // ── Safe mesh factory ─────────────────────────────────────────────────
  function mkMesh(geo, mat) {
    return new THREE.Mesh(geo, mat);
  }

  function placed(mesh, x, y, z) {
    mesh.position.set(x, y, z);
    return mesh;
  }

  function rotated(mesh, rx, ry, rz) {
    mesh.rotation.set(rx, ry, rz);
    return mesh;
  }

  // ── Procedural character ──────────────────────────────────────────────
  function buildProcedural(scene) {
    const C = {
      skin: 0xffd5b8,
      hair: 0x1a0a2e,
      eye: 0x8b5cf6,
      cloth1: 0x8b5cf6,
      cloth2: 0x06b6d4,
      accent: 0xf43f5e,
      gold: 0xffd700,
    };
    const cel = (c) => new THREE.MeshToonMaterial({ color: c });
    const basic = (c) => new THREE.MeshBasicMaterial({ color: c });

    const root = new THREE.Group();
    root.name = "animeChar";

    // ── Torso ─────────────────────────────────────────────────────────
    root.add(
      placed(
        mkMesh(new THREE.CylinderGeometry(0.38, 0.32, 0.9, 12), cel(C.cloth1)),
        0,
        0,
        0,
      ),
    );

    // Collar
    const collar = mkMesh(
      new THREE.TorusGeometry(0.36, 0.05, 8, 24),
      cel(C.cloth2),
    );
    collar.rotation.x = Math.PI / 2;
    placed(collar, 0, 0.42, 0);
    root.add(collar);

    // ── Hips ──────────────────────────────────────────────────────────
    root.add(
      placed(
        mkMesh(new THREE.CylinderGeometry(0.35, 0.4, 0.4, 12), cel(C.cloth1)),
        0,
        -0.65,
        0,
      ),
    );

    // Belt
    root.add(
      placed(
        mkMesh(new THREE.CylinderGeometry(0.37, 0.37, 0.1, 16), cel(C.accent)),
        0,
        -0.5,
        0,
      ),
    );

    // Buckle
    root.add(
      placed(
        mkMesh(
          new THREE.BoxGeometry(0.14, 0.1, 0.06),
          new THREE.MeshStandardMaterial({
            color: C.gold,
            metalness: 0.9,
            roughness: 0.1,
          }),
        ),
        0,
        -0.5,
        0.38,
      ),
    );

    // ── Legs ──────────────────────────────────────────────────────────
    [-0.2, 0.2].forEach((x) => {
      // Upper leg
      root.add(
        placed(
          mkMesh(
            new THREE.CylinderGeometry(0.14, 0.12, 0.85, 10),
            cel(C.cloth2),
          ),
          x,
          -1.08,
          0,
        ),
      );

      // Boot — use a rounded group instead of CapsuleGeometry
      const boot = makeCapsule(0.13, 0.12, 0.3, cel(C.accent));
      boot.position.set(x, -1.58, 0.06);
      boot.rotation.x = -0.2;
      root.add(boot);
    });

    // ── Arms ──────────────────────────────────────────────────────────
    const arms = [];
    [
      [-0.55, 1],
      [0.55, -1],
    ].forEach(([x, side]) => {
      const shoulder = new THREE.Group();
      shoulder.position.set(x, 0.25, 0);

      const upper = mkMesh(
        new THREE.CylinderGeometry(0.1, 0.09, 0.75, 8),
        cel(C.cloth1),
      );
      upper.position.y = -0.3;
      upper.rotation.z = side * 0.25;
      shoulder.add(upper);

      const elbow = new THREE.Group();
      elbow.position.y = -0.75;
      shoulder.add(elbow);

      const fore = mkMesh(
        new THREE.CylinderGeometry(0.09, 0.08, 0.65, 8),
        cel(C.skin),
      );
      fore.position.y = -0.3;
      fore.rotation.z = side * 0.15;
      elbow.add(fore);

      const hand = mkMesh(new THREE.SphereGeometry(0.1, 8, 6), cel(C.skin));
      hand.position.set(side * 0.05, -0.65, 0);
      elbow.add(hand);

      root.add(shoulder);
      arms.push({ shoulder, elbow });
    });

    // ── Neck ──────────────────────────────────────────────────────────
    root.add(
      placed(
        mkMesh(new THREE.CylinderGeometry(0.14, 0.16, 0.25, 10), cel(C.skin)),
        0,
        0.54,
        0,
      ),
    );

    // ── Head ──────────────────────────────────────────────────────────
    const head = new THREE.Group();
    head.position.set(0, 1.05, 0);
    root.add(head);

    // Skull
    const skull = mkMesh(new THREE.SphereGeometry(0.42, 16, 12), cel(C.skin));
    skull.scale.set(1, 1.08, 0.95);
    head.add(skull);

    // Jaw
    const jaw = mkMesh(new THREE.ConeGeometry(0.28, 0.32, 12), cel(C.skin));
    jaw.position.y = -0.33;
    jaw.rotation.x = Math.PI;
    head.add(jaw);

    // ── Eyes ──────────────────────────────────────────────────────────
    const eyes = [];
    [-0.17, 0.17].forEach((x) => {
      const eg = new THREE.Group();
      eg.position.set(x, 0.06, 0.36);
      head.add(eg);

      // Sclera
      const sclera = mkMesh(
        new THREE.SphereGeometry(0.1, 12, 10),
        cel(0xffffff),
      );
      sclera.scale.set(1, 1.1, 0.6);
      eg.add(sclera);

      // Iris
      const iris = mkMesh(new THREE.CircleGeometry(0.065, 20), cel(C.eye));
      iris.position.z = 0.06;
      eg.add(iris);

      // Pupil
      const pupil = mkMesh(
        new THREE.CircleGeometry(0.032, 16),
        basic(0x000000),
      );
      pupil.position.z = 0.065;
      eg.add(pupil);

      // Highlight
      const hl = mkMesh(new THREE.CircleGeometry(0.016, 8), basic(0xffffff));
      hl.position.set(0.02, 0.025, 0.07);
      eg.add(hl);

      // Upper lid line
      const lid = mkMesh(
        new THREE.CylinderGeometry(0.1, 0.1, 0.015, 12, 1, false, 0, Math.PI),
        basic(C.hair),
      );
      lid.position.set(0, 0.08, 0.04);
      lid.rotation.x = Math.PI / 2;
      eg.add(lid);

      eyes.push(eg);
    });

    // ── Eyebrows ──────────────────────────────────────────────────────
    [-0.17, 0.17].forEach((x) => {
      const brow = mkMesh(
        new THREE.BoxGeometry(0.14, 0.018, 0.015),
        basic(C.hair),
      );
      brow.position.set(x, 0.21, 0.37);
      brow.rotation.z = x < 0 ? 0.12 : -0.12;
      head.add(brow);
    });

    // ── Mouth ─────────────────────────────────────────────────────────
    const mouth = mkMesh(
      new THREE.TorusGeometry(0.07, 0.012, 6, 12, Math.PI),
      basic(0xc06060),
    );
    mouth.position.set(0, -0.15, 0.38);
    mouth.rotation.z = Math.PI;
    head.add(mouth);

    // ── Hair ──────────────────────────────────────────────────────────
    const hairCap = mkMesh(
      new THREE.SphereGeometry(0.44, 14, 10, 0, Math.PI * 2, 0, Math.PI * 0.55),
      cel(C.hair),
    );
    hairCap.position.y = 0.12;
    head.add(hairCap);

    // Side hair strands
    [
      { p: [-0.38, -0.1, 0.1], r: [0.3, 0, 0.5] },
      { p: [0.38, -0.1, 0.1], r: [0.3, 0, -0.5] },
      { p: [-0.3, -0.15, -0.2], r: [0.1, 0.3, 0.4] },
      { p: [0.3, -0.15, -0.2], r: [0.1, -0.3, -0.4] },
    ].forEach(({ p, r }) => {
      const s = mkMesh(new THREE.ConeGeometry(0.07, 0.55, 6), cel(C.hair));
      s.position.set(...p);
      s.rotation.set(...r);
      head.add(s);
    });

    // Front spikes
    [
      [-0.15, 0.45, 0.22, -0.4 + 0 * 0.1, 0, -0.15 * -1.5],
      [0, 0.52, 0.18, -0.4 + 1 * 0.1, 0, 0],
      [0.15, 0.45, 0.22, -0.4 + 2 * 0.1, 0, 0.15 * -1.5],
    ].forEach(([x, y, z, rx, ry, rz]) => {
      const spike = mkMesh(new THREE.ConeGeometry(0.055, 0.4, 5), cel(C.hair));
      spike.position.set(x, y, z);
      spike.rotation.set(rx, ry, rz);
      head.add(spike);
    });

    // Headband
    const band = mkMesh(
      new THREE.TorusGeometry(0.44, 0.03, 8, 28),
      cel(C.cloth2),
    );
    band.position.y = 0.18;
    band.rotation.x = Math.PI / 2;
    head.add(band);

    // Earrings
    [-0.43, 0.43].forEach((x) => {
      const ring = mkMesh(
        new THREE.TorusGeometry(0.05, 0.012, 6, 12),
        new THREE.MeshStandardMaterial({
          color: C.gold,
          metalness: 0.9,
          roughness: 0.1,
        }),
      );
      ring.position.set(x, -0.1, 0);
      ring.rotation.y = Math.PI / 2;
      head.add(ring);
    });

    // ── Chest emblem ──────────────────────────────────────────────────
    const emblem = mkMesh(
      new THREE.CircleGeometry(0.1, 6),
      new THREE.MeshStandardMaterial({
        color: C.accent,
        emissive: new THREE.Color(0xf43f5e),
        emissiveIntensity: 0.4,
      }),
    );
    emblem.position.set(0, 0.1, 0.4);
    root.add(emblem);

    // ── Aura rings ────────────────────────────────────────────────────
    const auraMat = new THREE.MeshBasicMaterial({
      color: C.eye,
      transparent: true,
      opacity: 0.3,
      side: THREE.DoubleSide,
    });
    const aura = mkMesh(new THREE.RingGeometry(0.6, 0.65, 32), auraMat);
    aura.position.set(0, -0.3, 0);
    aura.rotation.x = Math.PI / 2;
    root.add(aura);

    const aura2Mat = new THREE.MeshBasicMaterial({
      color: C.eye,
      transparent: true,
      opacity: 0.12,
      side: THREE.DoubleSide,
    });
    const aura2 = mkMesh(new THREE.RingGeometry(0.9, 0.94, 32), aura2Mat);
    aura2.position.set(0, -0.3, 0);
    aura2.rotation.x = Math.PI / 2;
    root.add(aura2);

    // ── Final position ─────────────────────────────────────────────────
    root.position.set(1.8, -0.5, 0);
    root.scale.setScalar(0.9);
    scene.add(root);

    return { root, head, eyes, arms, aura, aura2 };
  }

  // ── GLTF loader (lazy) ─────────────────────────────────────────────────
  function loadGLTF(url) {
    return new Promise((resolve, reject) => {
      const doLoad = () => {
        const loader = new THREE.GLTFLoader();
        loader.load(
          url,
          resolve,
          (xhr) => {
            if (xhr.total)
              console.info(
                `[3D] ${Math.round((xhr.loaded / xhr.total) * 100)}%`,
              );
          },
          reject,
        );
      };

      if (THREE.GLTFLoader) {
        doLoad();
        return;
      }

      const script = document.createElement("script");
      script.src =
        "https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js";
      script.onload = doLoad;
      script.onerror = () => reject(new Error("GLTFLoader CDN failed"));
      document.head.appendChild(script);
    });
  }

  // ── Find bone by name fragments ────────────────────────────────────────
  function findBone(root, ...names) {
    let found = null;
    root.traverse((node) => {
      if (found) return;
      const n = node.name.toLowerCase();
      if (names.some((name) => n.includes(name.toLowerCase()))) found = node;
    });
    return found;
  }

  // ── Module init ────────────────────────────────────────────────────────
  async function init(sceneState) {
    const { scene, mobile } = sceneState;
    if (!scene) return;

    let useGLTF = !USE_FALLBACK;

    if (useGLTF) {
      try {
        const check = await fetch(MODEL_URL, { method: "HEAD" });
        if (!check.ok)
          throw new Error(`Model not found (${check.status}): ${MODEL_URL}`);

        const gltf = await loadGLTF(MODEL_URL);
        model = gltf.scene;
        model.position.set(1.8, -1.2, 0);
        model.scale.setScalar(mobile ? 1.2 : 1.6);
        model.rotation.y = -0.3;

        model.traverse((node) => {
          if (node.isMesh) {
            node.castShadow = true;
            node.receiveShadow = true;
          }
        });

        scene.add(model);

        if (gltf.animations?.length) {
          mixer = new THREE.AnimationMixer(model);
          gltf.animations.forEach((clip) => {
            mixer.clipAction(clip).setLoop(THREE.LoopRepeat).play();
          });
        }

        headBone = findBone(model, "Head", "head", "J_Bip_C_Head");
        neckBone = findBone(model, "Neck", "neck", "J_Bip_C_Neck");
        leftEyeBone = findBone(
          model,
          "LeftEye",
          "left_eye",
          "eye_L",
          "J_Adj_L_FaceEye",
        );
        rightEyeBone = findBone(
          model,
          "RightEye",
          "right_eye",
          "eye_R",
          "J_Adj_R_FaceEye",
        );
        leftArmBone = findBone(
          model,
          "LeftUpperArm",
          "left_arm",
          "J_Bip_L_UpperArm",
        );
        rightArmBone = findBone(
          model,
          "RightUpperArm",
          "right_arm",
          "J_Bip_R_UpperArm",
        );

        console.info("[3D] GLTF loaded. Bones:", {
          head: !!headBone,
          neck: !!neckBone,
          eyes: !!(leftEyeBone && rightEyeBone),
          arms: !!(leftArmBone && rightArmBone),
        });
      } catch (err) {
        console.warn("[3D] GLTF failed → procedural fallback:", err.message);
        useGLTF = false;
      }
    }

    if (!useGLTF) {
      proceduralParts = buildProcedural(scene);
    }
  }

  // ── Per-frame update ────────────────────────────────────────────────────
  function update(elapsed, delta, sceneState) {
    const { mouse, scroll } = sceneState;

    if (mixer) mixer.update(delta);

    // GLTF bone update
    if (model) {
      targetHeadX = lerp(targetHeadX, -mouse.ny * 0.3, 0.08);
      targetHeadY = lerp(targetHeadY, mouse.nx * 0.4, 0.08);
      currentHeadX = lerp(currentHeadX, targetHeadX + keyTiltX, LERP);
      currentHeadY = lerp(currentHeadY, targetHeadY + keyTiltY, LERP);

      if (headBone) {
        headBone.rotation.x = lerp(headBone.rotation.x, currentHeadX, 0.1);
        headBone.rotation.y = lerp(headBone.rotation.y, currentHeadY, 0.1);
      }
      if (neckBone) {
        neckBone.rotation.x = lerp(
          neckBone.rotation.x,
          currentHeadX * 0.4,
          0.08,
        );
        neckBone.rotation.y = lerp(
          neckBone.rotation.y,
          currentHeadY * 0.3,
          0.08,
        );
      }
      if (leftEyeBone) {
        leftEyeBone.rotation.y = lerp(
          leftEyeBone.rotation.y,
          mouse.nx * 0.15,
          0.1,
        );
        leftEyeBone.rotation.x = lerp(
          leftEyeBone.rotation.x,
          -mouse.ny * 0.1,
          0.1,
        );
      }
      if (rightEyeBone) {
        rightEyeBone.rotation.y = lerp(
          rightEyeBone.rotation.y,
          mouse.nx * 0.15,
          0.1,
        );
        rightEyeBone.rotation.x = lerp(
          rightEyeBone.rotation.x,
          -mouse.ny * 0.1,
          0.1,
        );
      }

      breathPhase += delta * 0.9;
      model.position.y = -1.2 + Math.sin(breathPhase) * 0.015;

      const scrollNorm = Math.min(scroll.y / 2000, 1);
      model.rotation.x = lerp(model.rotation.x, scrollNorm * -0.1, 0.05);

      waveCooldown = Math.max(0, waveCooldown - delta);
      if (waving && rightArmBone) {
        wavePhase += delta * 5;
        rightArmBone.rotation.z = lerp(
          rightArmBone.rotation.z,
          -1.2 + Math.sin(wavePhase) * 0.6,
          0.15,
        );
        if (wavePhase > Math.PI * 4) {
          waving = false;
          wavePhase = 0;
          rightArmBone.rotation.z = 0;
        }
      }
      if (!waving && rightArmBone) {
        rightArmBone.rotation.z = lerp(
          rightArmBone.rotation.z,
          Math.sin(elapsed * 0.7) * 0.04,
          0.03,
        );
      }
      if (leftArmBone) {
        leftArmBone.rotation.z = lerp(
          leftArmBone.rotation.z,
          Math.sin(elapsed * 0.7 + 1) * 0.04,
          0.03,
        );
      }

      pulseScale = lerp(pulseScale, 1, 0.08);
      model.scale.setScalar(lerp(model.scale.x, 1.6 * pulseScale, 0.1));
    }

    if (proceduralParts) _updateProcedural(elapsed, delta, sceneState);

    keyTiltX = lerp(keyTiltX, 0, 0.04);
    keyTiltY = lerp(keyTiltY, 0, 0.04);
  }

  function _updateProcedural(elapsed, delta, sceneState) {
    const p = proceduralParts;
    if (!p) return;
    const { mouse } = sceneState;

    breathPhase += delta * 0.9;
    p.root.position.y = -0.5 + Math.sin(breathPhase) * 0.018;

    targetHeadX = lerp(targetHeadX, -mouse.ny * 0.35, 0.08);
    targetHeadY = lerp(targetHeadY, mouse.nx * 0.45, 0.08);
    currentHeadX = lerp(currentHeadX, targetHeadX + keyTiltX, LERP);
    currentHeadY = lerp(currentHeadY, targetHeadY + keyTiltY, LERP);

    p.head.rotation.x = currentHeadX;
    p.head.rotation.y = currentHeadY;

    p.eyes.forEach((eye) => {
      eye.rotation.y = lerp(eye.rotation.y, mouse.nx * 0.12, 0.08);
      eye.rotation.x = lerp(eye.rotation.x, -mouse.ny * 0.1, 0.08);
    });

    // Blink cycle
    blinkTimer += delta;
    if (blinkState === "open" && blinkTimer > blinkNext) {
      blinkState = "closing";
      blinkTimer = 0;
      blinkNext = 2.5 + Math.random() * 4;
    }
    if (blinkState === "closing") {
      p.eyes.forEach((e) => {
        e.scale.y = lerp(e.scale.y, 0.05, 0.25);
      });
      if (blinkTimer > 0.12) {
        blinkState = "opening";
        blinkTimer = 0;
      }
    }
    if (blinkState === "opening") {
      p.eyes.forEach((e) => {
        e.scale.y = lerp(e.scale.y, 1, 0.2);
      });
      if (blinkTimer > 0.15) blinkState = "open";
    }

    // Arm animation
    waveCooldown = Math.max(0, waveCooldown - delta);
    if (waving) {
      wavePhase += delta * 6;
      if (p.arms[1]) {
        p.arms[1].shoulder.rotation.z = -0.5 + Math.sin(wavePhase) * 0.8;
      }
      if (wavePhase > Math.PI * 4) {
        waving = false;
        wavePhase = 0;
        if (p.arms[1]) p.arms[1].shoulder.rotation.z = -0.25;
      }
    } else {
      p.arms?.forEach((arm, i) => {
        if (arm?.shoulder) {
          arm.shoulder.rotation.z = lerp(
            arm.shoulder.rotation.z,
            (i === 0 ? 0.3 : -0.3) + Math.sin(elapsed * 0.7 + i) * 0.04,
            0.05,
          );
        }
      });
    }

    // Aura pulse
    if (p.aura) {
      p.aura.material.opacity = 0.2 + Math.sin(elapsed * 1.5) * 0.08;
      p.aura.rotation.z += delta * 0.3;
    }
    if (p.aura2) {
      p.aura2.material.opacity = 0.08 + Math.sin(elapsed * 1.2 + 1) * 0.04;
      p.aura2.rotation.z -= delta * 0.18;
    }

    pulseScale = lerp(pulseScale, 1, 0.08);
    p.root.scale.setScalar(0.9 * pulseScale);
  }

  // ── Event handlers ─────────────────────────────────────────────────────
  function onKeyTilt(key) {
    const amt = 0.35;
    if (key === "ArrowLeft") keyTiltY = lerp(keyTiltY, -amt, 0.5);
    if (key === "ArrowRight") keyTiltY = lerp(keyTiltY, amt, 0.5);
    if (key === "ArrowUp") keyTiltX = lerp(keyTiltX, -amt * 0.6, 0.5);
    if (key === "ArrowDown") keyTiltX = lerp(keyTiltX, amt * 0.6, 0.5);
  }

  function onPulse() {
    pulseScale = 1.15;
    const root = model || proceduralParts?.root;
    if (!root) return;
    let t = 0;
    const startY = root.position.y;
    const jump = () => {
      t += 0.06;
      root.position.y = startY + Math.sin(t * Math.PI) * 0.7;
      if (t < 1) requestAnimationFrame(jump);
      else root.position.y = startY;
    };
    requestAnimationFrame(jump);
  }

  function triggerWave() {
    if (waving || waveCooldown > 0) return;
    waving = true;
    waveCooldown = 4;
  }

  return { init, update, onKeyTilt, onPulse, triggerWave };
})();
