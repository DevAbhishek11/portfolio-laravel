@extends('layouts.admin')
@section('title', 'Blog Posts')

@section('content')
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Content Management</h1>
            <p class="text-slate-400 text-sm mt-1">Manage, edit, and monitor your blog performance.</p>
        </div>
        <a href="{{ route('admin.blogs.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-indigo-600/20 transition-all transform active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Create New Post
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-slate-800/50 border border-slate-700 rounded-2xl p-4 mb-6 backdrop-blur-sm">
        <form method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full bg-slate-900/50 border border-slate-600 text-white text-sm rounded-xl pl-10 pr-4 py-2 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all"
                    placeholder="Search by title or category...">
            </div>

            <div class="w-full md:w-48">
                <select name="status" onchange="this.form.submit()"
                    class="w-full bg-slate-900/50 border border-slate-600 text-white text-sm rounded-xl px-4 py-2 outline-none focus:border-indigo-500 appearance-none cursor-pointer">
                    <option value="">All Statuses</option>
                    @foreach (['published', 'draft', 'archived'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto ml-auto">
                <button type="submit"
                    class="flex-1 md:flex-none px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-bold rounded-xl transition-all">
                    Apply Filter
                </button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.blogs.index') }}"
                        class="px-4 py-2 bg-rose-500/10 text-rose-500 hover:bg-rose-500/20 text-sm font-bold rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Posts Table -->
    <div class="bg-slate-800/50 border border-slate-700 rounded-2xl overflow-hidden backdrop-blur-sm shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/40">
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Post Title</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Category</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Engagement</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($blogs as $blog)
                        <tr class="hover:bg-white/5 transition-all group">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span
                                            class="text-sm font-bold text-white group-hover:text-indigo-400 transition-colors">
                                            {{ Str::limit($blog->title, 45) }}
                                        </span>
                                        @if ($blog->is_featured)
                                            <span
                                                class="px-2 py-0.5 bg-amber-500/10 text-amber-500 border border-amber-500/20 rounded text-[9px] font-black uppercase tracking-tighter">
                                                Featured
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $blog->read_time }} min read
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs font-medium text-slate-400 bg-slate-900/50 px-2 py-1 rounded-lg border border-slate-700">
                                    {{ $blog->category ?: 'General' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusMap = [
                                        'published' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                        'draft' => 'bg-slate-700/50 text-slate-400 border-slate-600/30',
                                        'archived' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                    ];
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-md border text-[10px] font-bold uppercase tracking-tight {{ $statusMap[$blog->status] ?? $statusMap['draft'] }}">
                                    {{ $blog->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-slate-300">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span class="text-sm font-semibold">{{ number_format($blog->view_count) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-slate-500 whitespace-nowrap">
                                    {{ $blog->published_at ? $blog->published_at->format('M d, Y') : '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0">
                                    <a href="{{ route('admin.blogs.show', $blog->id) }}"
                                        class="p-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg transition-colors"
                                        title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.blogs.edit', $blog->id) }}"
                                        class="p-2 bg-slate-700 hover:bg-indigo-600 text-white rounded-lg transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20.172 4.172a2 2 0 010 2.828L13.414 13l-4 1 1-4 6.758-6.758z" />
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete('del-blog-{{ $blog->id }}')"
                                        class="p-2 bg-slate-700 hover:bg-rose-600 text-white rounded-lg transition-colors"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <form id="del-blog-{{ $blog->id }}" method="POST"
                                    action="{{ route('admin.blogs.destroy', $blog->id) }}" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-24 text-center">
                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="p-4 bg-slate-700/30 rounded-full text-slate-500">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-white font-bold text-lg">No posts found</p>
                                        <p class="text-slate-500 text-sm">Start sharing your thoughts with the world.</p>
                                    </div>
                                    <a href="{{ route('admin.blogs.create') }}"
                                        class="text-indigo-400 hover:text-indigo-300 font-bold text-sm">Create your first
                                        post →</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($blogs->hasPages())
            <div class="px-6 py-4 bg-slate-900/40 border-t border-slate-700">
                {{ $blogs->links() }}
            </div>
        @endif
    </div>
@endsection
