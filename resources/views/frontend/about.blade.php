@extends('layouts.app')
@section('content')
    {{-- Hero --}}
    <section style="padding:8rem 0 4rem;position:relative;overflow:hidden;">
        <div class="orb" style="width:500px;height:500px;background:rgba(139,92,246,0.1);top:-100px;right:-100px;"></div>
        <div class="container">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;">
                <div class="reveal-left">
                    <div class="section-tag">About Me</div>
                    <h1 class="font-display"
                        style="font-size:clamp(2.5rem,5vw,4rem);font-weight:900;color:var(--text-primary);line-height:1.15;margin-bottom:1.25rem;">
                        Crafting Digital<br><span class="grad-text">Experiences</span>
                    </h1>
                    <p style="color:var(--text-secondary);font-size:1rem;line-height:1.8;margin-bottom:1.5rem;">
                        {{ $admin?->bio ?: 'Passionate full stack developer with a love for clean code, thoughtful design, and building products that make a difference.' }}
                    </p>
                    <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                        <a href="{{ route('contact') }}" class="btn-anime">Let's Talk</a>
                        @if ($admin?->resume_url)
                            <a href="{{ $admin->resume_url }}" target="_blank" class="btn-outline">Download CV ↓</a>
                        @endif
                    </div>
                </div>
                <div class="reveal-right" style="text-align:center;">
                    <div style="width:280px;height:280px;border-radius:50%;margin:0 auto;position:relative;">
                        @if ($admin?->avatar)
                            <img src="{{ $admin->avatar }}" alt="{{ $admin->name }}"
                                style="width:100%;height:100%;object-fit:cover;border-radius:50%;border:3px solid var(--border-color);">
                        @else
                            <div
                                style="width:100%;height:100%;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#06b6d4);display:flex;align-items:center;justify-content:center;">
                                <span class="font-display"
                                    style="font-size:6rem;font-weight:900;color:white;">{{ substr(config('portfolio.site_name'), 0, 1) }}</span>
                            </div>
                        @endif
                        <div
                            style="position:absolute;inset:-8px;border-radius:50%;border:1px solid var(--border-color);animation:spin 20s linear infinite;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Timeline --}}
    <section class="section-pad" style="background:var(--bg-secondary);position:relative;">
        <div class="container">
            <div style="text-align:center;margin-bottom:4rem;">
                <div class="section-tag reveal" style="justify-content:center;">My Journey</div>
                <h2 class="font-display reveal delay-1"
                    style="font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:800;color:var(--text-primary);">Experience &
                    Education</h2>
            </div>
            <div style="max-width:700px;margin:0 auto;position:relative;">
                {{-- Timeline line --}}
                <div style="position:absolute;left:24px;top:0;bottom:0;width:1px;background:var(--border-color);"></div>
                @foreach ($timeline as $i => $item)
                    <div class="reveal delay-{{ $i + 1 }}"
                        style="display:flex;gap:2rem;margin-bottom:2.5rem;position:relative;">
                        <div
                            style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,var(--accent-1),var(--accent-2));display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.7rem;font-weight:700;color:white;z-index:1;">
                            {{ substr($item['year'], 2) }}
                        </div>
                        <div class="anime-card" style="flex:1;padding:1.25rem;">
                            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem;flex-wrap:wrap;">
                                <h3 style="color:var(--text-primary);font-size:1rem;font-weight:700;">{{ $item['title'] }}
                                </h3>
                                <span class="tech-badge">{{ $item['year'] }}</span>
                            </div>
                            <p style="color:var(--accent-1);font-size:0.85rem;font-weight:500;margin-bottom:0.5rem;">
                                {{ $item['place'] }}</p>
                            <p style="color:var(--text-secondary);font-size:0.875rem;line-height:1.6;">{{ $item['desc'] }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Skills --}}
    <section class="section-pad">
        <div class="container">
            <div style="text-align:center;margin-bottom:4rem;">
                <div class="section-tag reveal" style="justify-content:center;">Tech Stack</div>
                <h2 class="font-display reveal delay-1"
                    style="font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:800;color:var(--text-primary);">Skills &
                    Technologies</h2>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1.5rem;">
                @foreach ($skills as $category => $items)
                    <div class="anime-card reveal" style="padding:1.5rem;">
                        <h3
                            style="color:var(--text-accent);font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:1rem;">
                            {{ $category }}</h3>
                        <div style="display:flex;flex-wrap:wrap;gap:0.5rem;">
                            @foreach ($items as $skill)
                                <span class="tech-badge">{{ $skill }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Skills section — full replacement --}}
    <section class="py-24 lg:py-32">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <div class="section-tag justify-center">Tech Stack</div>
                <h2 class="font-display text-4xl lg:text-5xl font-black text-txt-primary reveal delay-1">
                    Skills & <span class="grad-text">Proficiency</span>
                </h2>
                <p class="text-txt-secondary mt-4 max-w-md mx-auto reveal delay-2">
                    Hover the radar points to see exact proficiency levels. Filter by category below.
                </p>
            </div>

            <x-frontend.skills-radar :skills="$skills" :grouped="$grouped" />
        </div>
    </section>

    <style>
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection
