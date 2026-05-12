@extends('layouts.app')
@section('content')
    <div style="min-height:80vh;display:flex;align-items:center;justify-content:center;text-align:center;padding:2rem;">
        <div>
            <div class="grad-text font-display" style="font-size:8rem;font-weight:900;line-height:1;">404</div>
            <h1 class="font-display" style="font-size:2rem;font-weight:800;color:var(--text-primary);margin-bottom:1rem;">Page
                Not Found</h1>
            <p style="color:var(--text-secondary);margin-bottom:2rem;">迷子になりました · This page doesn't exist.</p>
            <a href="{{ route('home') }}" class="btn-anime">← Back to Home</a>
        </div>
    </div>
@endsection
