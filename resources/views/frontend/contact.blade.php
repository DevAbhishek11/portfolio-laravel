@extends('layouts.app')
@section('content')
    <section style="padding:8rem 0 5rem;position:relative;overflow:hidden;">
        <div class="orb" style="width:500px;height:500px;background:rgba(139,92,246,0.1);top:-100px;right:-100px;"></div>
        <div class="orb" style="width:400px;height:400px;background:rgba(6,182,212,0.07);bottom:-50px;left:-80px;"></div>

        <div class="container" style="position:relative;z-index:1;">
            <div style="text-align:center;margin-bottom:4rem;">
                <div class="section-tag reveal" style="justify-content:center;">Contact</div>
                <h1 class="font-display reveal delay-1"
                    style="font-size:clamp(2.5rem,5vw,4rem);font-weight:900;color:var(--text-primary);">
                    Let's Work <span class="grad-text">Together</span>
                </h1>
                <p class="reveal delay-2"
                    style="color:var(--text-secondary);font-size:1rem;max-width:480px;margin:1rem auto 0;line-height:1.7;">
                    Have a project in mind, or just want to say hi? Drop me a message and I'll get back to you as soon as
                    possible.
                </p>
                <p style="color:rgba(139,92,246,0.4);font-size:0.8rem;letter-spacing:0.15em;margin-top:0.75rem;">
                    一緒に素晴らしいものを作りましょう</p>
            </div>

            <div
                style="display:grid;grid-template-columns:1fr 380px;gap:3rem;align-items:start;max-width:1000px;margin:0 auto;">

                {{-- Contact Form --}}
                <div class="glass reveal-left" style="padding:2.5rem;">
                    <form method="POST" action="{{ route('contact.store') }}" id="contactForm">
                        @csrf
                        {{-- Honeypot --}}
                        <div style="display:none;"><input type="text" name="honeypot" tabindex="-1" autocomplete="off">
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                            <div>
                                <label
                                    style="display:block;color:var(--text-secondary);font-size:0.82rem;font-weight:500;margin-bottom:0.4rem;">Full
                                    Name *</label>
                                <input type="text" name="name"
                                    class="anime-input @error('name') input-error @enderror" value="{{ old('name') }}"
                                    placeholder="John Doe" required>
                                @error('name')
                                    <p class="error-msg">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label
                                    style="display:block;color:var(--text-secondary);font-size:0.82rem;font-weight:500;margin-bottom:0.4rem;">Email
                                    Address *</label>
                                <input type="email" name="email"
                                    class="anime-input @error('email') input-error @enderror" value="{{ old('email') }}"
                                    placeholder="john@example.com" required>
                                @error('email')
                                    <p class="error-msg">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
                            <div>
                                <label
                                    style="display:block;color:var(--text-secondary);font-size:0.82rem;font-weight:500;margin-bottom:0.4rem;">Phone</label>
                                <input type="text" name="phone"
                                    class="anime-input @error('phone') input-error @enderror" value="{{ old('phone') }}"
                                    placeholder="+1 234 567 890">
                                @error('phone')
                                    <p class="error-msg">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label
                                    style="display:block;color:var(--text-secondary);font-size:0.82rem;font-weight:500;margin-bottom:0.4rem;">Subject
                                    *</label>
                                <input type="text" name="subject"
                                    class="anime-input @error('subject') input-error @enderror" value="{{ old('subject') }}"
                                    placeholder="Project inquiry" required>
                                @error('subject')
                                    <p class="error-msg">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div style="margin-bottom:1.5rem;">
                            <label
                                style="display:block;color:var(--text-secondary);font-size:0.82rem;font-weight:500;margin-bottom:0.4rem;">
                                Message * <span id="char-count" style="float:right;color:var(--text-secondary);">0 /
                                    5000</span>
                            </label>
                            <textarea name="message" id="message-area" class="anime-input @error('message') input-error @enderror" rows="7"
                                placeholder="Tell me about your project, timeline, and budget…" required minlength="20" maxlength="5000"
                                oninput="document.getElementById('char-count').textContent = this.value.length + ' / 5000'">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="error-msg">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" id="submit-btn" class="btn-anime"
                            style="width:100%;justify-content:center;font-size:1rem;padding:1rem;">
                            <span id="btn-text">Send Message ✉</span>
                            <span id="btn-loading" style="display:none;">Sending…</span>
                        </button>
                        <p style="color:var(--text-secondary);font-size:0.78rem;text-align:center;margin-top:0.75rem;">
                            I'll reply within 24 hours.
                        </p>
                    </form>
                </div>

                {{-- Contact Info --}}
                <div class="reveal-right" style="display:flex;flex-direction:column;gap:1.25rem;">

                    @foreach ([['📧', 'Email', config('portfolio.site_email'), 'mailto:' . config('portfolio.site_email')], ['📍', 'Location', config('portfolio.site_location'), null], ['📱', 'Phone', config('portfolio.site_phone'), 'tel:' . config('portfolio.site_phone')]] as [$icon, $label, $value, $href])
                        @if ($value)
                            <div class="anime-card" style="padding:1.25rem;display:flex;align-items:center;gap:1rem;">
                                <div
                                    style="width:44px;height:44px;border-radius:0.75rem;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;font-size:1.2rem;flex-shrink:0;">
                                    {{ $icon }}
                                </div>
                                <div>
                                    <p style="color:var(--text-secondary);font-size:0.75rem;margin-bottom:0.15rem;">
                                        {{ $label }}</p>
                                    @if ($href)
                                        <a href="{{ $href }}"
                                            style="color:var(--text-primary);font-size:0.9rem;font-weight:500;text-decoration:none;">{{ $value }}</a>
                                    @else
                                        <p style="color:var(--text-primary);font-size:0.9rem;font-weight:500;">
                                            {{ $value }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach

                    {{-- Social links --}}
                    <div class="anime-card" style="padding:1.25rem;">
                        <p
                            style="color:var(--text-secondary);font-size:0.75rem;margin-bottom:0.875rem;text-transform:uppercase;letter-spacing:0.08em;">
                            Find me online</p>
                        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
                            @if (config('portfolio.social.github'))
                                <a href="{{ config('portfolio.social.github') }}" target="_blank" class="social-icon"
                                    style="width:auto;padding:0 1rem;border-radius:0.5rem;font-size:0.82rem;">GitHub</a>
                            @endif
                            @if (config('portfolio.social.linkedin'))
                                <a href="{{ config('portfolio.social.linkedin') }}" target="_blank" class="social-icon"
                                    style="width:auto;padding:0 1rem;border-radius:0.5rem;font-size:0.82rem;">LinkedIn</a>
                            @endif
                            @if (config('portfolio.social.twitter'))
                                <a href="{{ config('portfolio.social.twitter') }}" target="_blank" class="social-icon"
                                    style="width:auto;padding:0 1rem;border-radius:0.5rem;font-size:0.82rem;">Twitter</a>
                            @endif
                        </div>
                    </div>

                    {{-- Availability --}}
                    <div class="anime-card" style="padding:1.25rem;border-color:rgba(34,197,94,0.3);">
                        <div style="display:flex;align-items:center;gap:0.625rem;">
                            <div
                                style="width:10px;height:10px;border-radius:50%;background:#22c55e;animation:pulse 2s infinite;">
                            </div>
                            <p style="color:#4ade80;font-size:0.875rem;font-weight:600;">Available for new projects</p>
                        </div>
                        <p style="color:var(--text-secondary);font-size:0.8rem;margin-top:0.375rem;">Typical response time:
                            within 24 hours</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.getElementById('contactForm').addEventListener('submit', function() {
                document.getElementById('btn-text').style.display = 'none';
                document.getElementById('btn-loading').style.display = 'inline';
                document.getElementById('submit-btn').disabled = true;
            });
        </script>
    @endpush

    <style>
        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.5;
                transform: scale(1.3);
            }
        }
    </style>
@endsection
