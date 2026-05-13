@extends('layouts.app')
@section('content')

    <section style="padding:8rem 0 4rem;">
        <div class="container">
            <div style="text-align:center;margin-bottom:3rem;">
                <div class="section-tag reveal" style="justify-content:center;">Portfolio</div>
                <h1 class="font-display reveal delay-1"
                    style="font-size:clamp(2.5rem,5vw,4rem);font-weight:900;color:var(--text-primary);">
                    My <span class="grad-text">Projects</span>
                </h1>
            </div>

            {{-- Category Filter --}}
            <div style="display:flex;justify-content:center;gap:0.75rem;flex-wrap:wrap;margin-bottom:3rem;"
                class="reveal delay-2">
                @foreach ($categories as $cat)
                    <a href="{{ $cat === 'all' ? route('projects.index') : route('projects.index', ['category' => $cat]) }}"
                        style="padding:0.5rem 1.25rem;border-radius:9999px;font-size:0.85rem;font-weight:500;text-decoration:none;transition:all 0.2s;
                      border:1px solid {{ $active === $cat || ($cat === 'all' && $active === 'all') ? 'var(--accent-1)' : 'var(--border-color)' }};
                      background:{{ $active === $cat || ($cat === 'all' && $active === 'all') ? 'rgba(139,92,246,0.2)' : 'transparent' }};
                      color:{{ $active === $cat || ($cat === 'all' && $active === 'all') ? '#a78bfa' : 'var(--text-secondary)' }};">
                        {{ ucfirst($cat) }}
                    </a>
                @endforeach
            </div>

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.75rem;">
                @forelse($projects as $i => $project)
                    <article class="anime-card reveal delay-{{ ($i % 3) + 1 }}">
                        <div style="height:200px;overflow:hidden;border-radius:0.875rem 0.875rem 0 0;position:relative;">
                            <img src="{{ $project->getFirstMediaUrl('thumbnail', 'card') }}"
                                srcset="{{ $project->getThumbnailSrcset() }}"
                                sizes="(max-width:640px) 100vw, (max-width:1024px) 50vw, 33vw" alt="{{ $project->title }}"
                                loading="lazy" decoding="async"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <div
                                style="position:absolute;inset:0;background:linear-gradient(to bottom,transparent 50%,rgba(10,10,15,0.85));">
                            </div>
                            <span class="badge-{{ $project->category }}"
                                style="position:absolute;top:0.75rem;left:0.75rem;padding:3px 10px;border-radius:9999px;font-size:0.7rem;font-weight:600;backdrop-filter:blur(8px);background:rgba(0,0,0,0.5);">
                                {{ ucfirst($project->category) }}
                            </span>
                            @if ($project->is_featured)
                                <span style="position:absolute;top:0.75rem;right:0.75rem;">⭐</span>
                            @endif
                        </div>
                        <div style="padding:1.5rem;">
                            <h2
                                style="color:var(--text-primary);font-size:1.05rem;font-weight:700;margin-bottom:0.4rem;font-family:'Playfair Display',serif;">
                                {{ $project->title }}
                            </h2>
                            <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.6;margin-bottom:1rem;">
                                {{ Str::limit($project->short_description, 90) }}
                            </p>
                            <div style="display:flex;flex-wrap:wrap;gap:0.35rem;margin-bottom:1.25rem;">
                                @foreach ($project->techStacks->take(4) as $tech)
                                    <span class="tech-badge">{{ $tech->name }}</span>
                                @endforeach
                            </div>
                            <div style="display:flex;align-items:center;gap:0.75rem;">
                                <a href="{{ route('projects.show', $project->slug) }}" class="btn-anime"
                                    style="padding:0.5rem 1rem;font-size:0.82rem;flex:1;justify-content:center;">View →</a>
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
                    </article>
                @empty
                    <div style="grid-column:1/-1;text-align:center;padding:5rem 0;color:var(--text-secondary);">
                        No projects found. <a href="{{ route('projects.index') }}" style="color:var(--accent-1);">Clear
                            filters →</a>
                    </div>
                @endforelse
            </div>

            @if ($projects->hasPages())
                <div style="display:flex;justify-content:center;gap:0.5rem;margin-top:3rem;flex-wrap:wrap;">
                    {{ $projects->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
