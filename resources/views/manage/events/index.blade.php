@extends('layouts.dashboard')

@section('pageTitle', 'Manage Events')

@section('content')
<div>
    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Manage Events</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $events->count() }} total events.</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- View toggle --}}
            <div class="flex items-center rounded-lg border border-slate-200 bg-white p-0.5">
                <a href="{{ route('manage.events.index', ['view' => 'list']) }}"
                   class="flex items-center justify-center h-8 w-8 rounded-md transition-all duration-150 {{ $viewMode === 'list' ? 'bg-gold-500 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </a>
                <a href="{{ route('manage.events.index', ['view' => 'grid']) }}"
                   class="flex items-center justify-center h-8 w-8 rounded-md transition-all duration-150 {{ $viewMode === 'grid' ? 'bg-gold-500 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </a>
            </div>
            <a href="{{ route('manage.events.create') }}"
                class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Event
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success') }}
    </div>
    @endif

    @if($errors->any() && !session('success'))
    <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700">
        <ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    @php
    $statusColors = ['published' => 'bg-emerald-100 text-emerald-700', 'draft' => 'bg-slate-100 text-slate-600', 'archived' => 'bg-amber-100 text-amber-700'];
    @endphp

    <div class="{{ $viewMode === 'grid' ? 'grid gap-4 md:grid-cols-2' : 'grid gap-3' }}">
    @forelse($events as $event)
        @if($viewMode === 'grid')
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition-all">
            <div class="p-5 sm:p-6">
                <div class="flex items-start gap-4 sm:gap-5">
                    <div class="w-16 sm:w-20 shrink-0">
                        <img src="{{ $event->cover_url }}" alt="" class="w-full aspect-square rounded-xl object-cover bg-slate-100">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <span class="inline-flex items-center rounded-full bg-gold-100 px-2.5 py-0.5 text-[10px] font-semibold text-gold-700">{{ ucfirst($event->event_type) }}</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-semibold {{ $statusColors[$event->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($event->status) }}</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-snug">{{ $event->name }}</h3>
                        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                            <span class="flex items-center gap-1"><svg class="h-3.5 w-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> {{ $event->start_date->format('M d, Y') }}@if($event->start_date->format('Y-m-d') !== $event->end_date->format('Y-m-d')) - {{ $event->end_date->format('M d, Y') }}@endif</span>
                            <span class="flex items-center gap-1"><svg class="h-3.5 w-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg> {{ $event->venue->name ?? 'Online' }}</span>
                        </div>
                        <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1.5 text-xs font-medium">
                            <span class="inline-flex items-center gap-1.5 text-slate-600" title="Tickets sold"><svg class="h-4 w-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg> <span>{{ $event->ticket_types_sum_sold_count ?? 0 }}/{{ $event->ticket_types_sum_quantity ?? 0 }} sold</span></span>
                            <span class="inline-flex items-center gap-1.5 text-slate-600" title="Registered attendees"><svg class="h-4 w-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> <span>{{ $event->attendees_count }} registered</span></span>
                            <span class="inline-flex items-center gap-1.5 text-emerald-600" title="Checked in"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span>{{ $event->checkins_count }} checked in</span></span>
                            <span class="inline-flex items-center gap-1.5 text-blue-600" title="Revenue"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span>${{ number_format($event->revenue, 0) }}</span></span>
                        </div>
                        <div class="mt-3 flex items-center gap-2 pt-3 border-t border-slate-100">
                            <a href="{{ route('manage.events.show', $event->slug) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-50 transition-colors">View</a>
                            <a href="{{ route('manage.events.edit', $event->slug) }}" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors">Edit</a>
                            <form action="{{ route('manage.events.destroy', $event->slug) }}" method="POST" class="inline" onsubmit="return confirm('Delete event \'{{ $event->name }}\'? This cannot be undone.');">@csrf @method('DELETE')<button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-md transition-all">
            <div class="flex items-center gap-3 sm:gap-5 p-3 sm:p-5">
                <div class="w-10 sm:w-14 shrink-0">
                    <img src="{{ $event->cover_url }}" alt="" class="w-full aspect-square rounded-lg sm:rounded-xl object-cover bg-slate-100">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5 flex-wrap">
                        <span class="inline-flex items-center rounded-full bg-gold-100 px-2 py-0.5 text-[10px] font-semibold text-gold-700">{{ ucfirst($event->event_type) }}</span>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $statusColors[$event->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($event->status) }}</span>
                    </div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900 leading-snug truncate">{{ $event->name }}</h3>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-0.5 text-[11px] sm:text-xs text-slate-500">
                        <span class="flex items-center gap-1"><svg class="h-3 w-3 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg> {{ $event->start_date->format('M d, Y') }}@if($event->start_date->format('Y-m-d') !== $event->end_date->format('Y-m-d')) - {{ $event->end_date->format('M d, Y') }}@endif</span>
                        <span class="hidden sm:flex items-center gap-1"><svg class="h-3 w-3 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg> {{ $event->venue->name ?? 'Online' }}</span>
                    </div>
                    <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] font-medium">
                        <span class="inline-flex items-center gap-1 text-slate-600" title="Tickets sold"><svg class="h-3.5 w-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg> <span>{{ $event->ticket_types_sum_sold_count ?? 0 }}/{{ $event->ticket_types_sum_quantity ?? 0 }}</span></span>
                        <span class="inline-flex items-center gap-1 text-slate-600" title="Registered attendees"><svg class="h-3.5 w-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg> <span>{{ $event->attendees_count }} registered</span></span>
                        <span class="inline-flex items-center gap-1 text-slate-600" title="Checked in"><svg class="h-3.5 w-3.5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span>{{ $event->checkins_count }} checked in</span></span>
                        <span class="inline-flex items-center gap-1 text-blue-600" title="Total revenue"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> <span>${{ number_format($event->revenue, 0) }}</span></span>
                    </div>
                </div>
                <div class="flex items-center gap-1 sm:gap-2 shrink-0">
                    <a href="{{ route('manage.events.show', $event->slug) }}" class="rounded-lg px-2 sm:px-3 py-1.5 text-[11px] sm:text-xs font-semibold text-blue-600 hover:bg-blue-50 transition-colors">View</a>
                    <a href="{{ route('manage.events.edit', $event->slug) }}" class="hidden sm:inline-flex rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors">Edit</a>
                    <form action="{{ route('manage.events.destroy', $event->slug) }}" method="POST" class="inline" onsubmit="return confirm('Delete event \'{{ $event->name }}\'? This cannot be undone.');">@csrf @method('DELETE')<button type="submit" class="rounded-lg px-2 sm:px-3 py-1.5 text-[11px] sm:text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button></form>
                </div>
            </div>
        </div>
        @endif
    @empty
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="py-12 text-center text-sm text-slate-500">No events found.</div>
    </div>
    @endforelse
    </div>

</div>
@endsection