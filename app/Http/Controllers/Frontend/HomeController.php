<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Blog;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProjects = Project::published()
            ->featured()
            ->with('techStacks')
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        $latestBlogs = Blog::published()
            ->latest('published_at')
            ->limit(3)
            ->get();

        $projectCount = Project::published()->count();
        $blogCount    = Blog::published()->count();

        return view('frontend.home', compact(
            'featuredProjects',
            'latestBlogs',
            'projectCount',
            'blogCount'
        ));
    }
}


// This is updated part of home controller and this broken or not working in some parts when loads on home page...

// namespace App\Http\Controllers\Frontend;

// use App\Http\Controllers\Controller;
// use App\Models\Project;
// use App\Models\Blog;
// use Illuminate\Support\Facades\Cache;

// class HomeController extends Controller
// {
//     public function index()
//     {
//         $featuredProjects = Cache::remember('home.featured_projects', 300, fn() => Project::published()->featured()->with('techStacks')->orderBy('sort_order')->limit(4)->get());
//         $latestBlogs = Cache::remember('home.latest_blogs', 300, fn() => Blog::published()->latest('published_at')->limit(3)->get());
//         $stats = Cache::remember('home.stats', 1800, fn() => ['projects' => Project::published()->count(), 'blogs' => Blog::published()->count(),]);
//         return view('frontend.home', ['featuredProjects' => $featuredProjects, 'latestBlogs' => $latestBlogs, 'projectCount' => $stats['projects'], 'blogCount' => $stats['blogs'],]);
//     }
// }
