@extends('layouts.app')
@section('content')

    {{-- ── HERO ─────────────────────────────────────────────────────────────────── --}}
    <section style="min-height:100vh;display:flex;align-items:center;position:relative;overflow:hidden;padding-top:5rem;">
        {{-- Orbs --}}
        <div class="orb" style="width:600px;height:600px;background:rgba(139,92,246,0.12);top:-100px;right:-100px;"></div>
        <div class="orb" style="width:400px;height:400px;background:rgba(6,182,212,0.08);bottom:-50px;left:-50px;"></div>

        <div class="container" style="position:relative;z-index:1;">
            <div style="max-width:700px;">
                <div class="section-tag reveal" data-anime="fade-up">
                    <span>Full Stack Developer</span>
                </div>

                <h1 class="hero-title font-display" data-split="chars"
                    style="font-size:clamp(3rem,7vw,5.5rem);font-weight:900;line-height:1.1;margin-bottom:1.5rem;">
                    Hi, I'm <span class="grad-text">{{ config('portfolio.site_name') }}</span>
                </h1>

                <p class="reveal delay-2"
                    style="font-size:1.15rem;color:var(--text-secondary);max-width:520px;margin-bottom:2.5rem;line-height:1.8;"
                    data-anime="fade-up" data-delay="200">
                    I craft beautiful, performant web experiences — from pixel-perfect frontends to bulletproof backends.
                    <span style="color:var(--text-accent);">{{ $projectCount }}+ projects</span> shipped.
                </p>

                <div style="display:flex;gap:1rem;flex-wrap:wrap;" class="reveal delay-3" data-anime="fade-up"
                    data-delay="300">
                    <a href="{{ route('projects.index') }}" class="btn-anime">
                        View My Work
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                    <a href="{{ route('contact') }}" class="btn-outline">Get In Touch</a>
                </div>

                {{-- Social links --}}
                <div style="display:flex;gap:0.75rem;margin-top:2.5rem;" class="reveal delay-4">
                    @if (config('portfolio.social.github'))
                        <a href="{{ config('portfolio.social.github') }}" target="_blank" class="social-icon">GH</a>
                    @endif
                    @if (config('portfolio.social.linkedin'))
                        <a href="{{ config('portfolio.social.linkedin') }}" target="_blank" class="social-icon">in</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div style="position:absolute;bottom:2rem;left:50%;transform:translateX(-50%);text-align:center;z-index:1;">
            <p style="color:var(--text-secondary);font-size:0.7rem;letter-spacing:0.15em;margin-bottom:0.5rem;">スクロール ·
                SCROLL</p>
            <div
                style="width:1px;height:50px;background:linear-gradient(to bottom,var(--accent-1),transparent);margin:0 auto;animation:scrollPulse 2s ease-in-out infinite;">
            </div>
        </div>
    </section>

    {{-- ── FEATURED PROJECTS ────────────────────────────────────────────────────── --}}
    <section class="stagger-grid" class="section-pad" style="position:relative;">
        <div class="container">
            <div style="text-align:center;margin-bottom:4rem;">
                <div class="section-tag reveal" style="justify-content:center;">Selected Work</div>
                <h2 class="font-display reveal delay-1"
                    style="font-size:clamp(2rem,4vw,3rem);font-weight:800;color:var(--text-primary);">
                    Featured Projects
                </h2>
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.75rem;">
                @forelse($featuredProjects as $i => $project)
                    <article class="anime-card reveal delay-{{ (int) $i + 1 }}" style="position:relative;">
                        {{-- Thumbnail --}}
                        <div style="height:220px;overflow:hidden;border-radius:0.875rem 0.875rem 0 0;position:relative;">
                            <img src="{{ asset('/' .  $project->thumbnail ) }}" alt="{{ $project->title }}"
                                style="width:100%;height:100%;object-fit:cover;transition:transform 0.5s ease;">
                            <div
                                style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 40%,rgba(10,10,15,0.9));">
                            </div>
                            <span class="badge-{{ $project->category }}"
                                style="position:absolute;top:0.75rem;left:0.75rem;padding:3px 12px;border-radius:9999px;font-size:0.72rem;font-weight:600;background:rgba(0,0,0,0.5);backdrop-filter:blur(8px);">
                                {{ ucfirst($project->category) }}
                            </span>
                            @if ($project->is_featured)
                                <span style="position:absolute;top:0.75rem;right:0.75rem;font-size:0.8rem;">⭐</span>
                            @endif
                        </div>

                        <div style="padding:1.5rem;position:relative;z-index:1;">
                            <h3
                                style="color:var(--text-primary);font-size:1.1rem;font-weight:700;margin-bottom:0.5rem;font-family:'Playfair Display',serif;">
                                {{ $project->title }}
                            </h3>
                            <p style="color:var(--text-secondary);font-size:0.875rem;line-height:1.6;margin-bottom:1rem;">
                                {{ Str::limit($project->short_description, 100) }}
                            </p>

                            {{-- Tech stack --}}
                            <div style="display:flex;flex-wrap:wrap;gap:0.4rem;margin-bottom:1.25rem;">
                                @foreach ($project->techStacks->take(4) as $tech)
                                    <span class="tech-badge">{{ $tech->name }}</span>
                                @endforeach
                                @if ($project->techStacks->count() > 4)
                                    <span class="tech-badge">+{{ $project->techStacks->count() - 4 }}</span>
                                @endif
                            </div>

                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <a href="{{ route('projects.show', $project->slug) }}" class="btn-anime"
                                    style="padding:0.5rem 1.25rem;font-size:0.85rem;">
                                    View Project →
                                </a>
                                <div style="display:flex;gap:0.5rem;">
                                    @if ($project->github_url)
                                        <a href="{{ $project->github_url }}" target="_blank" class="btn-outline"
                                            style="padding:0.5rem 0.75rem;font-size:0.8rem;">GH</a>
                                    @endif
                                    @if ($project->live_url)
                                        <a href="{{ $project->live_url }}" target="_blank" class="btn-outline"
                                            style="padding:0.5rem 0.75rem;font-size:0.8rem;">↗</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <p style="color:var(--text-secondary);text-align:center;grid-column:1/-1;padding:4rem 0;">No featured
                        projects yet.</p>
                @endforelse
            </div>

            <div style="text-align:center;margin:3rem 0;" class="reveal">
                <a href="{{ route('projects.index') }}" class="btn-outline">View All Projects →</a>
            </div>
        </div>
    </section>

    {{-- ── SERVICES PREVIEW ─────────────────────────────────────────────────────── --}}
    <section data-section-burst class="section-pad"
        style="background:var(--bg-secondary);position:relative;overflow:hidden;">
        <div class="orb"
            style="width:500px;height:500px;background:rgba(6,182,212,0.06);top:50%;left:-100px;transform:translateY(-50%);">
        </div>
        <div class="container" style="position:relative;z-index:1;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;" class="md:grid-cols-2">
                <div>
                    <div class="section-tag reveal">What I Do</div>
                    <h2 class="font-display reveal delay-1"
                        style="font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:800;color:var(--text-primary);margin-bottom:1rem;">
                        Full Stack<br><span class="grad-text">Development</span>
                    </h2>
                    <p class="reveal delay-2" style="color:var(--text-secondary);line-height:1.8;margin-bottom:1.5rem;">
                        From concept to deployment, I build complete digital products that solve real problems. Frontend
                        finesse meets backend power.
                    </p>
                    <a href="{{ route('services') }}" class="btn-anime reveal delay-3" style="display:inline-flex;">
                        See All Services →
                    </a>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    @foreach ([['Frontend', 'React · Next.js · Tailwind', '#06b6d4'], ['Backend', 'Laravel · Node.js · APIs', '#8b5cf6'], ['Mobile', 'React Native · Expo', '#f43f5e'], ['Database', 'MySQL · MongoDB · Redis', '#eab308']] as $i => [$title, $sub, $color])
                        <div class="anime-card reveal delay-{{ $i + 1 }}" style="padding:1.25rem;">
                            <div
                                style="width:36px;height:36px;border-radius:0.5rem;background:{{ $color }}20;display:flex;align-items:center;justify-content:center;margin-bottom:0.75rem;">
                                <div style="width:14px;height:14px;border-radius:3px;background:{{ $color }};">
                                </div>
                            </div>
                            <h3 style="color:var(--text-primary);font-size:0.95rem;font-weight:600;margin-bottom:0.25rem;">
                                {{ $title }}</h3>
                            <p style="color:var(--text-secondary);font-size:0.78rem;">{{ $sub }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── LATEST BLOGS ─────────────────────────────────────────────────────────── --}}
    @if ($latestBlogs->count())
        <section class="section-pad">
            <div class="container">
                <div
                    style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3rem;flex-wrap:wrap;gap:1rem;">
                    <div>
                        <div class="section-tag reveal">Latest Writing</div>
                        <h2 class="font-display reveal delay-1"
                            style="font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:800;color:var(--text-primary);">
                            From the Blog
                        </h2>
                    </div>
                    <a href="{{ route('blogs.index') }}" class="btn-outline reveal">Read All →</a>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.75rem;">
                    @foreach ($latestBlogs as $i => $blog)
                        <article class="anime-card reveal delay-{{ $i + 1 }}">
                            @if ($blog->featured_image)
                                <div style="height:180px;overflow:hidden;border-radius:0.875rem 0.875rem 0 0;">
                                    <img src="{{ asset('/' .  $blog->featured_image ) }}" alt="{{ $blog->title }}"
                                        style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;">
                                </div>
                            @endif
                            <div style="padding:1.5rem;">
                                @if ($blog->category)
                                    <span class="tech-badge"
                                        style="margin-bottom:0.75rem;display:inline-block;">{{ $blog->category }}</span>
                                @endif
                                <h3
                                    style="color:var(--text-primary);font-size:1rem;font-weight:700;margin-bottom:0.5rem;font-family:'Playfair Display',serif;line-height:1.4;">
                                    {{ Str::limit($blog->title, 60) }}
                                </h3>
                                <p
                                    style="color:var(--text-secondary);font-size:0.85rem;line-height:1.6;margin-bottom:1rem;">
                                    {{ Str::limit($blog->excerpt ?: strip_tags($blog->content), 100) }}
                                </p>
                                <div style="display:flex;align-items:center;justify-content:space-between;">
                                    <span style="color:var(--text-secondary);font-size:0.78rem;">
                                        {{ $blog->published_at?->format('M d, Y') }} · {{ $blog->read_time }}
                                    </span>
                                    <a href="{{ route('blogs.show', $blog->slug) }}"
                                        style="color:var(--accent-1);font-size:0.85rem;text-decoration:none;font-weight:500;">Read
                                        →</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- ── CONTACT CTA ──────────────────────────────────────────────────────────── --}}
    <section style="padding:5rem 0;position:relative;overflow:hidden;background:var(--bg-secondary);">
        <div class="orb"
            style="width:700px;height:700px;background:rgba(139,92,246,0.1);top:50%;left:50%;transform:translate(-50%,-50%);">
        </div>
        <div class="container" style="text-align:center;position:relative;z-index:1;">
            <p style="color:rgba(139,92,246,0.5);font-size:0.8rem;letter-spacing:0.2em;margin-bottom:1rem;">一緒に作りましょう</p>
            <h2 class="font-display reveal"
                style="font-size:clamp(2rem,5vw,3.5rem);font-weight:900;color:var(--text-primary);margin-bottom:1rem;">
                Let's Create Something<br><span class="grad-text">Amazing Together</span>
            </h2>
            <p class="reveal delay-1"
                style="color:var(--text-secondary);font-size:1rem;max-width:500px;margin:0 auto 2rem;">
                Have a project in mind? I'd love to hear about it. Let's talk and make it real.
            </p>
            <a href="{{ route('contact') }}" class="btn-anime reveal delay-2"
                style="font-size:1.05rem;padding:1rem 2.5rem;">
                Get In Touch ✉
            </a>
        </div>
    </section>

    <style>
        @keyframes scrollPulse {

            0%,
            100% {
                opacity: 0.3;
                transform: scaleY(0.5);
                transform-origin: top;
            }

            50% {
                opacity: 1;
                transform: scaleY(1);
            }
        }
    </style>
@endsection
