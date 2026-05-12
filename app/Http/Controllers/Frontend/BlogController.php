<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::published()->with('user');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('excerpt', 'like', "%{$s}%");
            });
        }
        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        $featured = Blog::published()->featured()->latest('published_at')->first();
        $blogs    = $query->orderByDesc('published_at')->paginate(9)->withQueryString();

        $categories = Blog::published()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category');

        return view('frontend.blogs.index', compact('blogs', 'featured', 'categories'));
    }

    public function show(string $slug)
    {
        $blog = Blog::published()
            ->with(['user', 'approvedComments.replies'])
            ->where('slug', $slug)
            ->firstOrFail();

        $blog->increment('view_count');

        $related = Blog::published()
            ->where('id', '!=', $blog->id)
            ->where(function ($q) use ($blog) {
                $q->where('category', $blog->category)
                  ->orWhereJsonOverlaps('tags', $blog->tags ?? []);
            })
            ->limit(3)
            ->get();

        $prev = Blog::published()->where('id', '<', $blog->id)->orderByDesc('id')->first();
        $next = Blog::published()->where('id', '>', $blog->id)->orderBy('id')->first();

        return view('frontend.blogs.show', compact('blog', 'related', 'prev', 'next'));
    }

    public function comment(Request $request, string $slug)
    {
        $blog = Blog::published()->where('slug', $slug)->firstOrFail();

        // Rate limit: 3 comments per hour per IP
        $key = 'comment:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->with('error', 'Too many comments. Please wait before trying again.');
        }
        RateLimiter::hit($key, 3600);

        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|max:255',
            'comment'   => 'required|string|min:5|max:2000',
            'parent_id' => 'nullable|integer|exists:blog_comments,id',
            'honeypot'  => 'max:0', // spam trap — must be empty
        ]);

        BlogComment::create([
            'blog_id'    => $blog->id,
            'parent_id'  => $data['parent_id'] ?? null,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'comment'    => $data['comment'],
            'ip_address' => $request->ip(),
            'is_approved'=> false,
        ]);

        return back()->with('success', 'Comment submitted and awaiting moderation. Thank you!');
    }
}