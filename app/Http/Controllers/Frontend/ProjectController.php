<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::published()->with('techStacks');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $projects   = $query->orderBy('sort_order')->orderByDesc('created_at')->paginate(9);
        $categories = ['all', 'frontend', 'backend', 'fullstack'];
        $active     = $request->get('category', 'all');

        return view('frontend.projects.index', compact('projects', 'categories', 'active'));
    }

    public function show(string $slug)
    {
        $project = Project::published()
            ->with(['images', 'techStacks'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment view count
        $project->increment('view_count');

        $related = Project::published()
            ->where('category', $project->category)
            ->where('id', '!=', $project->id)
            ->with('techStacks')
            ->limit(3)
            ->get();

        $prev = Project::published()->where('id', '<', $project->id)->orderByDesc('id')->first();
        $next = Project::published()->where('id', '>', $project->id)->orderBy('id')->first();

        return view('frontend.projects.show', compact('project', 'related', 'prev', 'next'));
    }
}
