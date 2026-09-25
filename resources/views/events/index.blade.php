@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-gold-50 via-white to-gold-100 py-10 sm:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex items-center rounded-full bg-gold-400/10 px-3 sm:px-4 py-1 text-xs sm:text-sm font-semibold text-gold-600 ring-1 ring-inset ring-gold-400/30 mb-3 sm:mb-4">
            Discover Opportunities
        </span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-slate-900">
            All Events &amp; Trainings
        </h1>
        <p class="mt-3 sm:mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto">
            Browse our full lineup of professional development opportunities.
        </p>
    </div>
</div>

<div class="mx-auto max-w-7xl px-0 sm:px-6 lg:px-8 py-6 sm:py-8">
    {{-- Status filter --}}
    <div class="flex flex-wrap items-center justify-center gap-2 px-4 sm:px-0 mb-6">
        @foreach(['active' => 'Active', 'past' => 'Past', 'all' => 'All Events'] as $val => $label)
            <a href="{{ route('events', ['status' => $val, 'type' => request('type'), 'sort' => request('sort'), 'view' => $viewMode]) }}"
               class="inline-flex items-center rounded-full px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold transition-all duration-200 {{ request('status', 'active') === $val ? 'bg-gold-500 text-white shadow-md shadow-gold-500/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- Filter by type & sort --}}
    <div class="sm:flex sm:flex-wrap sm:items-center sm:justify-between gap-4 mb-8">
        <div class="overflow-x-auto flex-nowrap flex items-center gap-2 px-4 sm:px-0 pb-2 sm:pb-0 -mb-2 sm:mb-0" style="-ms-overflow-style: none; scrollbar-width: none;">
            <a href="{{ route('events', ['status' => request('status', 'active'), 'sort' => request('sort'), 'view' => $viewMode]) }}"
               class="shrink-0 inline-flex items-center rounded-full px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold transition-all duration-200 {{ !request('type') ? 'bg-gold-500 text-white shadow-md shadow-gold-500/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                All Types
            </a>
            @foreach($eventTypes as $type)
                <a href="{{ route('events', ['type' => $type->slug, 'status' => request('status', 'active'), 'sort' => request('sort'), 'view' => $viewMode]) }}"
                   class="shrink-0 inline-flex items-center rounded-full px-3 sm:px-4 py-1.5 sm:py-2 text-xs sm:text-sm font-semibold transition-all duration-200 {{ request('type') === $type->slug ? 'bg-gold-500 text-white shadow-md shadow-gold-500/25' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $type->name }}
                </a>
            @endforeach
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-2 px-4 sm:px-0 mt-3 sm:mt-0">
            {{-- View toggle --}}
            <div class="flex items-center rounded-lg border border-slate-200 bg-white p-0.5 mr-2">
                <a href="{{ route('events', ['view' => 'grid', 'status' => request('status', 'active'), 'type' => request('type'), 'sort' => request('sort')]) }}"
                   class="flex items-center justify-center h-8 w-8 rounded-md transition-all duration-150 {{ $viewMode === 'grid' ? 'bg-gold-500 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </a>
                <a href="{{ route('events', ['view' => 'list', 'status' => request('status', 'active'), 'type' => request('type'), 'sort' => request('sort')]) }}"
                   class="flex items-center justify-center h-8 w-8 rounded-md transition-all duration-150 {{ $viewMode === 'list' ? 'bg-gold-500 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600' }}">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </a>
            </div>
            <label for="sort" class="text-xs sm:text-sm text-slate-500 font-medium shrink-0">Sort by:</label>
            <select id="sort" onchange="window.location.href=this.value"
                    class="flex-1 sm:flex-none rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs sm:text-sm text-slate-700 focus:border-gold-400 focus:ring-2 focus:ring-gold-500/20 outline-none">
                <option value="{{ route('events', ['sort' => 'date_asc', 'type' => request('type'), 'status' => request('status', 'active'), 'view' => $viewMode]) }}" {{ request('sort', 'date_asc') === 'date_asc' ? 'selected' : '' }}>Date: Soonest First</option>
                <option value="{{ route('events', ['sort' => 'date_desc', 'type' => request('type'), 'status' => request('status', 'active'), 'view' => $viewMode]) }}" {{ request('sort') === 'date_desc' ? 'selected' : '' }}>Date: Latest First</option>
                <option value="{{ route('events', ['sort' => 'name_asc', 'type' => request('type'), 'status' => request('status', 'active'), 'view' => $viewMode]) }}" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                <option value="{{ route('events', ['sort' => 'name_desc', 'type' => request('type'), 'status' => request('status', 'active'), 'view' => $viewMode]) }}" {{ request('sort') === 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
            </select>
        </div>
    </div>

    @if($events->count() > 0)
        @if($viewMode === 'grid')
        <div class="grid gap-4 sm:gap-8 sm:grid-cols-2 lg:grid-cols-3 px-4 sm:px-0">
        @else
        <div class="flex flex-col gap-4 sm:gap-6 px-4 sm:px-0">
        @endif
            @foreach($events as $event)
                @if($viewMode === 'grid')
                <a href="{{ route('events.show', $event->slug) }}" class="group block rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-4 sm:p-6 shadow-sm hover:shadow-lg hover:border-gold-300 transition-all duration-200">
                    <div class="aspect-video rounded-lg sm:rounded-xl mb-3 sm:mb-4 overflow-hidden border border-slate-100 bg-slate-100">
                        <img src="{{ $event->cover_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                    </div>
                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                        <span class="inline-flex items-center rounded-full bg-gold-100 px-2.5 sm:px-3 py-0.5 sm:py-1 text-[10px] sm:text-xs font-semibold text-gold-700">
                            {{ ucfirst($event->event_type) }}
                        </span>
                        <span class="text-[10px] sm:text-xs text-slate-400">
                            {{ $event->start_date->format('M d, Y') }}
                        </span>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-gold-600 transition-colors mb-1.5 sm:mb-2">
                        {{ $event->name }}
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 line-clamp-2 mb-3 sm:mb-4 leading-relaxed">
                        {{ Str::limit(strip_tags($event->description), 140) }}
                    </p>
                    <div class="flex flex-col sm:flex-row sm:items-center text-[11px] sm:text-xs text-slate-500 gap-1.5 sm:gap-4">
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-gold-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $event->start_date->format('M d') }} - {{ $event->end_date->format('M d, Y') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-gold-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $event->venue->name ?? 'Online' }}
                        </span>
                    </div>
                </a>
                @else
                <a href="{{ route('events.show', $event->slug) }}" class="group flex gap-4 sm:gap-6 rounded-xl sm:rounded-2xl border border-slate-200 bg-white p-3 sm:p-5 shadow-sm hover:shadow-lg hover:border-gold-300 transition-all duration-200">
                    <div class="w-24 sm:w-40 shrink-0 rounded-lg sm:rounded-xl overflow-hidden border border-slate-100 bg-slate-100">
                        <img src="{{ $event->cover_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                            <span class="inline-flex items-center rounded-full bg-gold-100 px-2.5 py-0.5 text-[10px] font-semibold text-gold-700">
                                {{ ucfirst($event->event_type) }}
                            </span>
                            <span class="text-[10px] text-slate-400">
                                {{ $event->start_date->format('M d, Y') }}
                            </span>
                        </div>
                        <h3 class="text-sm sm:text-lg font-bold text-slate-900 group-hover:text-gold-600 transition-colors mb-1 leading-snug">
                            {{ $event->name }}
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 line-clamp-1 sm:line-clamp-2 mb-2 leading-relaxed">
                            {{ Str::limit(strip_tags($event->description), 140) }}
                        </p>
                        <div class="flex flex-wrap items-center text-[11px] sm:text-xs text-slate-500 gap-2 sm:gap-4">
                            <span class="flex items-center gap-1">
                                <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5 text-gold-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $event->start_date->format('M d') }} - {{ $event->end_date->format('M d, Y') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="h-3 w-3 sm:h-3.5 sm:w-3.5 text-gold-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $event->venue->name ?? 'Online' }}
                            </span>
                        </div>
                    </div>
                </a>
                @endif
            @endforeach
        </div>
    @else
        <div class="text-center py-12 sm:py-20 px-4 sm:px-0">
            <svg class="h-12 w-12 sm:h-16 sm:w-16 mx-auto text-slate-300 mb-3 sm:mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="text-lg sm:text-xl font-semibold text-slate-500 mb-1 sm:mb-2">No events found</h3>
            <p class="text-sm sm:text-base text-slate-400">Try selecting a different training type.</p>
        </div>
    @endif
</div>
@endsection