@extends('layouts.app')
@section('content')

    <section style="padding:8rem 0 4rem;">
        <div class="container">
            <div style="text-align:center;margin-bottom:3rem;">
                <div class="section-tag reveal" style="justify-content:center;">Articles</div>
                <h1 class="font-display reveal delay-1"
                    style="font-size:clamp(2.5rem,5vw,4rem);font-weight:900;color:var(--text-primary);">
                    The <span class="grad-text">Blog</span>
                </h1>
            </div>

            {{-- Search + Filters --}}
            <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;justify-content:center;margin-bottom:3rem;"
                class="reveal delay-2">
                <input type="text" name="search" value="{{ request('search') }}" class="anime-input"
                    style="max-width:300px;" placeholder="Search articles…">
                @foreach ($categories as $cat)
                    <a href="{{ route('blogs.index', ['category' => $cat]) }}"
                        style="padding:0.5rem 1rem;border-radius:9999px;font-size:0.82rem;text-decoration:none;transition:all 0.2s;
                      border:1px solid {{ request('category') === $cat ? 'var(--accent-1)' : 'var(--border-color)' }};
                      background:{{ request('category') === $cat ? 'rgba(139,92,246,0.2)' : 'transparent' }};
                      color:{{ request('category') === $cat ? '#a78bfa' : 'var(--text-secondary)' }};">
                        {{ $cat }}
                    </a>
                @endforeach
                <button type="submit" class="btn-anime" style="padding:0.5rem 1.25rem;font-size:0.85rem;">Search</button>
                @if (request()->hasAny(['search', 'category', 'tag']))
                    <a href="{{ route('blogs.index') }}" class="btn-outline"
                        style="padding:0.5rem 1rem;font-size:0.85rem;">Clear</a>
                @endif
            </form>

            {{-- Featured Post --}}
            @if ($featured && !request()->hasAny(['search', 'category', 'tag']))
                <div class="anime-card reveal"
                    style="display:grid;grid-template-columns:1fr 1fr;gap:0;margin-bottom:2.5rem;overflow:hidden;">
                    @if ($featured->featured_image)
                        <img src="{{ $featured->featured_image }}" alt="{{ $featured->title }}"
                            style="width:100%;height:300px;object-fit:cover;">
                    @endif
                    <div style="padding:2.5rem;display:flex;flex-direction:column;justify-content:center;">
                        <span class="tech-badge" style="width:fit-content;margin-bottom:1rem;">⭐ Featured</span>
                        <h2 class="font-display"
                            style="color:var(--text-primary);font-size:1.5rem;font-weight:800;line-height:1.3;margin-bottom:0.75rem;">
                            {{ $featured->title }}</h2>
                        <p style="color:var(--text-secondary);font-size:0.9rem;line-height:1.6;margin-bottom:1.5rem;">
                            {{ Str::limit($featured->excerpt ?: strip_tags($featured->content), 150) }}
                        </p>
                        <div
                            style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
                            <span
                                style="color:var(--text-secondary);font-size:0.8rem;">{{ $featured->published_at?->format('M d, Y') }}
                                · {{ $featured->read_time }}</span>
                            <a href="{{ route('blogs.show', $featured->slug) }}" class="btn-anime"
                                style="padding:0.625rem 1.25rem;font-size:0.875rem;">Read Article →</a>
                        </div>
                    </div>
                </div>
            @endif

            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.75rem;">
                @forelse($blogs as $i => $blog)
                    <article class="anime-card reveal delay-{{ ($i % 3) + 1 }}">
                        @if ($blog->featured_image)
                            <div style="height:190px;overflow:hidden;border-radius:0.875rem 0.875rem 0 0;">
                                <img src="{{ $blog->featured_image }}" alt="{{ $blog->title }}"
                                    style="width:100%;height:100%;object-fit:cover;transition:transform 0.4s;"
                                    onmouseover="this.style.transform='scale(1.05)'"
                                    onmouseout="this.style.transform='scale(1)'">
                            </div>
                        @endif
                        <div style="padding:1.5rem;">
                            <div style="display:flex;gap:0.5rem;flex-wrap:wrap;margin-bottom:0.75rem;">
                                @if ($blog->category)
                                    <span class="tech-badge">{{ $blog->category }}</span>
                                @endif
                                @foreach (array_slice($blog->tags ?? [], 0, 2) as $tag)
                                    <span class="tech-badge" style="opacity:0.7;">#{{ $tag }}</span>
                                @endforeach
                            </div>
                            <h2 class="font-display"
                                style="color:var(--text-primary);font-size:1.05rem;font-weight:700;line-height:1.4;margin-bottom:0.6rem;">
                                {{ Str::limit($blog->title, 65) }}
                            </h2>
                            <p style="color:var(--text-secondary);font-size:0.85rem;line-height:1.6;margin-bottom:1rem;">
                                {{ Str::limit($blog->excerpt ?: strip_tags($blog->content), 100) }}
                            </p>
                            <div style="display:flex;align-items:center;justify-content:space-between;">
                                <span style="color:var(--text-secondary);font-size:0.78rem;">
                                    {{ $blog->published_at?->format('M d, Y') }} · {{ $blog->read_time }}
                                </span>
                                <a href="{{ route('blogs.show', $blog->slug) }}"
                                    style="color:var(--accent-1);text-decoration:none;font-size:0.85rem;font-weight:500;">Read
                                    →</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div style="grid-column:1/-1;text-align:center;padding:5rem 0;color:var(--text-secondary);">
                        No blog posts found.
                    </div>
                @endforelse
            </div>

            @if ($blogs->hasPages())
                <div style="display:flex;justify-content:center;margin-top:3rem;">
                    {{ $blogs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </section>
@endsection
