@extends('layouts.dashboard')

@section('pageTitle', 'Enquiries')

@section('content')
@php
    $filterQuery = array_filter([
        'search' => $filters['search'] !== '' ? $filters['search'] : null,
        'status' => $filters['status'] !== 'all' ? $filters['status'] : null,
        'sort' => $filters['sort'] !== 'newest' ? $filters['sort'] : null,
        'event' => $filters['event'] !== 'all' ? $filters['event'] : null,
        'attachments' => $filters['attachments'] ? 1 : null,
    ], fn ($v) => $v !== null);
    $hasFilters = $filterQuery !== [];
@endphp
<div x-data="{ more: false }">
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold text-slate-900">Enquiries & Training Requests</h1>
        <p class="text-sm text-slate-500 mt-1">
            {{ $enquiries->count() }} {{ $hasFilters ? 'matching' : 'total' }} enquiries{{ $hasFilters ? ' (filtered)' : ' from visitors' }}.
        </p>
    </div>

    @if(session('success'))<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif

    @if($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-sm text-red-700">
        <p class="font-semibold mb-1">Please fix the following:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- Toolbar: search, sort, filter, more --}}
    <div class="mb-4 rounded-2xl border border-slate-200 bg-white shadow-sm p-4">
        <form method="GET" action="{{ route('manage.enquiries.index') }}" x-ref="filters" class="flex flex-col gap-3">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                {{-- Search --}}
                <div class="relative flex-1 min-w-0">
                    <svg class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" /></svg>
                    <input type="text" name="search" value="{{ $filters['search'] }}" placeholder="Search name, contact, company, event or message..."
                           class="block w-full rounded-xl border border-slate-300 bg-white pl-9 pr-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                </div>

                {{-- Status --}}
                <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-gold-500 focus:outline-none">
                    <option value="all" @selected($filters['status'] === 'all')>All statuses</option>
                    <option value="pending" @selected($filters['status'] === 'pending')>Pending</option>
                    <option value="replied" @selected($filters['status'] === 'replied')>Replied</option>
                </select>

                {{-- Sort --}}
                <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-gold-500 focus:outline-none">
                    <option value="newest" @selected($filters['sort'] === 'newest')>Newest first</option>
                    <option value="oldest" @selected($filters['sort'] === 'oldest')>Oldest first</option>
                    <option value="name_az" @selected($filters['sort'] === 'name_az')>Name A–Z</option>
                    <option value="name_za" @selected($filters['sort'] === 'name_za')>Name Z–A</option>
                </select>

                <button type="submit" class="rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">Search</button>

                @if($hasFilters)
                <a href="{{ route('manage.enquiries.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors whitespace-nowrap">Clear</a>
                @endif

                {{-- More filters --}}
                <button type="button" @@click="more = !more"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></svg>
                    More
                    <svg class="h-4 w-4 transition-transform" :class="more && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
            </div>

            {{-- More options --}}
            <div x-show="more" x-cloak class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-100">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Event</label>
                    <select name="event" onchange="this.form.submit()" class="block w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-gold-500 focus:outline-none">
                        <option value="all" @selected($filters['event'] === 'all')>All events</option>
                        @foreach($eventOptions as $eventId => $eventName)
                        <option value="{{ $eventId }}" @selected((string) $eventId === $filters['event'])>{{ $eventName }}</option>
                        @endforeach
                    </select>
                </div>
                <label class="flex items-center gap-2 sm:mt-6 text-sm text-slate-700 cursor-pointer">
                    <input type="checkbox" name="attachments" value="1" @checked($filters['attachments']) onchange="this.form.submit()" class="h-4 w-4 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
                    Only enquiries with attachments
                </label>
            </div>
        </form>
    </div>

    @if($enquiries->isEmpty())
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm py-16 text-center">
        <p class="text-sm font-semibold text-slate-600">{{ $hasFilters ? 'No enquiries match your filters.' : 'No enquiries yet.' }}</p>
        @if($hasFilters)
        <a href="{{ route('manage.enquiries.index') }}" class="mt-3 inline-block text-sm font-semibold text-gold-600 hover:text-gold-700">Clear filters</a>
        @endif
    </div>
    @else
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-[22rem_1fr] lg:h-[calc(100vh-14rem)]">

            {{-- Enquiry list --}}
            <div class="border-b border-slate-200 lg:border-b-0 lg:border-r flex flex-col min-h-0">
                <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $hasFilters ? 'Results' : 'All Enquiries' }}</p>
                    <span class="text-xs font-semibold text-slate-400">{{ $enquiries->count() }}</span>
                </div>
                <div class="flex-1 overflow-y-auto divide-y divide-slate-100">
                    @foreach($enquiries as $e)
                    @php $active = $selected && $selected->id === $e->id; @endphp
                    <a href="{{ route('manage.enquiries.index', array_merge($filterQuery, ['enquiry' => $e->id])) }}"
                       class="block px-5 py-4 transition-colors {{ $active ? 'bg-gold-50 border-l-4 border-gold-500' : 'border-l-4 border-transparent hover:bg-slate-50' }}">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $e->name }}</p>
                            @if($e->replied_at)
                            <span class="shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-emerald-100 text-emerald-700">Replied</span>
                            @else
                            <span class="shrink-0 inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold bg-amber-100 text-amber-700">Pending</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-1 truncate">{{ $e->contact }}</p>
                        <p class="text-xs text-slate-400 mt-0.5 truncate">{{ $e->event->name ?? 'General Enquiry' }}</p>
                        @if($e->attachments->isNotEmpty())
                        <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 mt-1">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                            {{ $e->attachments->count() }}
                        </span>
                        @endif
                        @if($e->message)<p class="text-xs text-slate-500 mt-1.5 line-clamp-1">{{ $e->message }}</p>@endif
                        <p class="text-[10px] text-slate-400 mt-1.5">{{ $e->created_at->format('M d, Y H:i') }}</p>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Enquiry detail --}}
            <div class="flex flex-col min-h-0">
                <div class="flex-1 overflow-y-auto">
                    <div class="p-6 border-b border-slate-100 flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">{{ $selected->name }}</h2>
                            <p class="text-sm text-slate-500 mt-1">Submitted {{ $selected->created_at->format('M d, Y \a\t H:i') }}</p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($selected->replied_at)
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-emerald-100 text-emerald-700">Replied</span>
                            @else
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-amber-100 text-amber-700">Pending</span>
                            @endif
                            <form action="{{ route('manage.enquiries.destroy', $selected->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this enquiry?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button>
                            </form>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Contact</label>
                                <p class="text-sm text-slate-900">{{ $selected->contact }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Company</label>
                                <p class="text-sm text-slate-900">{{ $selected->company ?? '—' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Event</label>
                                <p class="text-sm text-slate-900">{{ $selected->event->name ?? 'General' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Replied At</label>
                                <p class="text-sm text-slate-900">{{ $selected->replied_at ? $selected->replied_at->format('M d, Y \a\t H:i') : 'Not yet replied' }}</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Message</label>
                            <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap">{{ $selected->message }}</div>
                        </div>

                        @if($selected->reply_message)
                        <div class="pt-4 border-t border-slate-100">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Your Reply</label>
                            <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap">{{ $selected->reply_message }}</div>
                        </div>
                        @endif

                        @if($selected->attachments->isNotEmpty())
                        <div class="pt-4 border-t border-slate-100">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Attachments</label>
                            <div class="space-y-2">
                                @foreach($selected->attachments as $attachment)
                                <div class="flex items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-2.5">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <svg class="h-5 w-5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-slate-800 truncate">{{ $attachment->original_name }}</p>
                                            <p class="text-xs text-slate-400">{{ $attachment->humanSize() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if($attachment->isPreviewable())
                                        <a href="{{ route('enquiries.attachment.preview', $attachment->id) }}" target="_blank" class="rounded-lg px-2.5 py-1 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors">Preview</a>
                                        @endif
                                        <a href="{{ route('enquiries.attachment.download', $attachment->id) }}" class="rounded-lg px-2.5 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">Download</a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Reply form --}}
                <div class="border-t border-slate-200 bg-slate-50/70 p-6">
                    <h3 class="text-sm font-bold text-slate-900 mb-3">Send Reply</h3>
                    <form action="{{ route('manage.enquiries.reply', $selected->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <textarea name="reply_message" rows="3" required placeholder="Type your response to this enquiry..." class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Send to Email</label>
                                <input type="email" name="reply_email" value="{{ $selected->contact }}" placeholder="Email address to send reply"
                                       class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                                <p class="text-xs text-slate-500 mt-1">Auto-filled from contact field. Edit if needed.</p>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Attachments</label>
                                <input type="file" name="attachments[]" multiple accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,.doc,.docx,.xls,.xlsx,.csv,.txt,.zip"
                                       class="block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-gold-500 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gold-600 cursor-pointer">
                                <p class="text-xs text-slate-500 mt-1">Up to 5 files (PDF, images, Office documents or ZIP), max 10 MB each.</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center justify-end">
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
