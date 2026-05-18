@extends('layouts.app')
@section('content')
    <section style="padding:8rem 0 4rem;">
        <div class="container">
            <div style="text-align:center;margin-bottom:4rem;">
                <div class="section-tag reveal" style="justify-content:center;">Services</div>
                <h1 class="font-display reveal delay-1"
                    style="font-size:clamp(2.5rem,5vw,4rem);font-weight:900;color:var(--text-primary);">
                    What I Can <span class="grad-text">Do For You</span>
                </h1>
                <p class="reveal delay-2"
                    style="color:var(--text-secondary);font-size:1rem;max-width:520px;margin:1rem auto 0;">
                    Full-spectrum digital solutions — from pixel-perfect UIs to scalable backend systems.
                </p>
            </div>

            <div
                style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.75rem;margin-bottom:5rem;">
                @foreach ($services as $i => $service)
                    <div class="anime-card reveal delay-{{ ($i % 3) + 1 }}" style="padding:2rem;">
                        <div
                            style="width:52px;height:52px;border-radius:0.875rem;background:{{ $service['color'] }}20;border:1px solid {{ $service['color'] }}40;display:flex;align-items:center;justify-content:center;margin-bottom:1.25rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none"
                                viewBox="0 0 24 24" stroke="{{ $service['color'] }}" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $service['icon'] }}" />
                            </svg>
                        </div>
                        <h3 class="font-display"
                            style="color:var(--text-primary);font-size:1.15rem;font-weight:700;margin-bottom:0.75rem;">
                            {{ $service['title'] }}</h3>
                        <p style="color:var(--text-secondary);font-size:0.875rem;line-height:1.7;margin-bottom:1rem;">
                            {{ $service['desc'] }}</p>
                        <div style="display:flex;flex-wrap:wrap;gap:0.35rem;">
                            @foreach ($service['stack'] as $tech)
                                <span class="tech-badge">{{ $tech }}</span>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Process --}}
            <div style="margin-bottom:5rem;">
                <div style="text-align:center;margin-bottom:3rem;">
                    <div class="section-tag reveal" style="justify-content:center;">How I Work</div>
                    <h2 class="font-display reveal delay-1"
                        style="font-size:clamp(1.8rem,3.5vw,2.8rem);font-weight:800;color:var(--text-primary);">My Process
                    </h2>
                </div>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.5rem;">
                    @foreach ($process as $i => $step)
                        <div class="anime-card reveal delay-{{ ($i % 4) + 1 }}" style="padding:1.5rem;text-align:center;">
                            <div class="grad-text font-display"
                                style="font-size:2.5rem;font-weight:900;margin-bottom:0.75rem;">{{ $step['step'] }}</div>
                            <h3 style="color:var(--text-primary);font-size:0.95rem;font-weight:700;margin-bottom:0.5rem;">
                                {{ $step['title'] }}</h3>
                            <p style="color:var(--text-secondary);font-size:0.82rem;line-height:1.6;">{{ $step['desc'] }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Stats --}}
            <div
                style="background:var(--bg-secondary);border-radius:1.5rem;border:1px solid var(--border-color);padding:3rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:2rem;text-align:center;">
                @foreach ($stats as $i => $stat)
                    <div class="reveal delay-{{ $i + 1 }}">
                        <div class="grad-text font-display" style="font-size:2.5rem;font-weight:900;" data-counter="{{ $stat['value'] }}" data-suffix="+">{{ $stat['value'] }}
                        </div>
                        <p style="color:var(--text-secondary);font-size:0.875rem;margin-top:0.25rem;">{{ $stat['label'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            <div style="text-align:center;margin-top:3.5rem;">
                <a href="{{ route('contact') }}" class="btn-anime" style="font-size:1.05rem;padding:1rem 2.5rem;">
                    Start a Project →
                </a>
            </div>
        </div>
    </section>
@endsection
