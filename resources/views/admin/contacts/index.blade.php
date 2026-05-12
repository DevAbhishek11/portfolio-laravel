@extends('layouts.admin')
@section('title', 'Contact Queries')

@section('content')
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Contact Queries</h1>
            @if ($unreadCount > 0)
                <div class="flex items-center gap-2 mt-1">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    </span>
                    <p class="text-sm font-medium text-rose-400">{{ $unreadCount }} unread messages</p>
                </div>
            @else
                <p class="text-slate-400 text-sm mt-1">Your inbox is up to date.</p>
            @endif
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-slate-800/50 border border-slate-700 rounded-2xl p-4 mb-6 backdrop-blur-sm">
        <form method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <!-- Search -->
            <div class="relative w-full md:w-72">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full bg-slate-900/50 border border-slate-600 text-white text-sm rounded-xl pl-10 pr-4 py-2 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all"
                    placeholder="Search name, email...">
            </div>

            <!-- Status Dropdown -->
            <div class="w-full md:w-44">
                <select name="status" onchange="this.form.submit()"
                    class="w-full bg-slate-900/50 border border-slate-600 text-white text-sm rounded-xl px-4 py-2 outline-none focus:border-indigo-500 appearance-none">
                    <option value="">All Statuses</option>
                    @foreach (['unread', 'read', 'replied', 'archived'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2 w-full md:w-auto ml-auto">
                <button type="submit"
                    class="flex-1 md:flex-none px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold rounded-xl transition-all">
                    Filter
                </button>
                @if (request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.contacts.index') }}"
                        class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white text-sm font-bold rounded-xl transition-all text-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-slate-800/50 border border-slate-700 rounded-2xl overflow-hidden backdrop-blur-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/40">
                        <th class="px-6 py-4 w-12">
                            <input type="checkbox" id="selectAll"
                                class="w-4 h-4 rounded border-slate-600 bg-slate-900 text-indigo-600 focus:ring-indigo-500/50 focus:ring-offset-slate-800">
                        </th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Sender</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Subject</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest text-right">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-white/5 transition-all group">
                            <td class="px-6 py-4">
                                <input type="checkbox"
                                    class="row-check w-4 h-4 rounded border-slate-600 bg-slate-900 text-indigo-600 focus:ring-indigo-500/50">
                            </td>
                            <td class="px-6 py-4 relative">
                                <!-- Unread Indicator Strip -->
                                @if ($contact->status === 'unread')
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-rose-500"></div>
                                @endif

                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold {{ $contact->status === 'unread' ? 'text-white' : 'text-slate-300' }}">
                                        {{ $contact->name }}
                                    </span>
                                    <span class="text-xs text-slate-500">{{ $contact->email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-400 truncate max-w-[200px] md:max-w-xs">
                                    {{ Str::limit($contact->subject, 50) }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'unread' => 'bg-rose-500/10 text-rose-500 border-rose-500/20',
                                        'read' => 'bg-slate-700/30 text-slate-400 border-slate-600/30',
                                        'replied' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20',
                                        'archived' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 rounded-md border text-[10px] font-bold uppercase tracking-tight {{ $statusClasses[$contact->status] ?? $statusClasses['read'] }}">
                                    {{ $contact->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-xs text-slate-500 whitespace-nowrap">{{ $contact->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.contacts.show', $contact->id) }}"
                                        class="p-2 bg-slate-700 hover:bg-indigo-600 text-white rounded-lg transition-colors"
                                        title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <button onclick="confirmDelete('del-contact-{{ $contact->id }}')"
                                        class="p-2 bg-slate-700 hover:bg-rose-600 text-white rounded-lg transition-colors"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                <form id="del-contact-{{ $contact->id }}" method="POST"
                                    action="{{ route('admin.contacts.destroy', $contact->id) }}" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="p-4 bg-slate-700/30 rounded-full">
                                        <svg class="w-10 h-10 text-slate-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-slate-500 font-medium">Your inbox is empty.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($contacts->hasPages())
            <div class="px-6 py-4 bg-slate-900/40 border-t border-slate-700">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Improved Select All logic
            const selectAll = document.getElementById('selectAll');
            const rowChecks = document.querySelectorAll('.row-check');

            selectAll.addEventListener('change', function() {
                rowChecks.forEach(cb => {
                    cb.checked = this.checked;
                    updateRowStyle(cb);
                });
            });

            rowChecks.forEach(cb => {
                cb.addEventListener('change', () => updateRowStyle(cb));
            });

            function updateRowStyle(cb) {
                const tr = cb.closest('tr');
                if (cb.checked) {
                    tr.classList.add('bg-indigo-600/5');
                } else {
                    tr.classList.remove('bg-indigo-600/5');
                }
            }
        </script>
    @endpush
@endsection
