@extends('layouts.app')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
{{-- Cover Header --}}
<div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 80%, #334155 100%)">
    <div class="absolute inset-0 bg-cover bg-center select-none" style="background-image: url('{{ $event->cover_url }}');"></div>
    @if($event->cover_image)
    <div class="absolute inset-x-0 top-[20%] bottom-0 bg-gradient-to-t from-black/95 via-black/80 to-transparent"></div>
    @endif
    <div class="relative mx-auto max-w-7xl px-4 py-24 sm:px-6 lg:px-8">
        <div class="max-w-4xl">
            <div class="rounded-[32px] border border-white/10 bg-slate-950/70 p-8 shadow-2xl shadow-black/40 backdrop-blur-xl">
                <div class="flex items-center gap-3 mb-4">
                    <span class="inline-flex items-center rounded-full bg-gold-500/20 px-3.5 py-1.5 text-xs font-bold text-gold-300 backdrop-blur-sm border border-gold-500/30">{{ ucfirst($event->event_type) }}</span>
                    <div class="ml-auto" x-data="saveButton({{ $isSaved ? 'true' : 'false' }})" data-save-url="{{ route('customer.saved-events.toggle') }}" data-event-id="{{ $event->id }}">
                        <button @click="toggle()" :disabled="loading"
                            class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-bold backdrop-blur-sm border transition-all duration-200 cursor-pointer"
                            :class="saved ? 'bg-red-500/20 text-red-300 border-red-500/40 hover:bg-red-500/30' : 'bg-white/10 text-white/70 border-white/20 hover:bg-white/20 hover:text-white'">
                            <svg class="h-3.5 w-3.5 transition-transform duration-300" :class="saved ? 'scale-110' : ''" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <span x-text="saved ? 'Saved' : 'Save'"></span>
                        </button>
                    </div>
                </div>
                <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl leading-tight">{{ $event->name }}</h1>
                <div class="mt-8 flex flex-wrap items-center gap-6">
                    <div class="flex items-center gap-2 text-sm text-slate-300 bg-white/10 rounded-full px-4 py-2 backdrop-blur-sm border border-white/10"><svg class="h-4 w-4 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg><span class="font-semibold">{{ $event->start_date->format('M d, Y') }}</span><span class="text-slate-500">–</span><span class="font-semibold">{{ $event->end_date->format('M d, Y') }}</span></div>
                    <div class="flex items-center gap-2 text-sm text-slate-300 bg-white/10 rounded-full px-4 py-2 backdrop-blur-sm border border-white/10"><svg class="h-4 w-4 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>{{ $event->venue->name ?? 'Online Event' }}</div>
                    <div class="flex items-center gap-2 text-sm text-slate-300 bg-white/10 rounded-full px-4 py-2 backdrop-blur-sm border border-white/10"><svg class="h-4 w-4 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>{{ $event->timezone }}</div>
                </div>
                @if($event->ticketTypes->isNotEmpty())
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <a href="{{ route('checkout.show', $event->slug) }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-8 py-4 text-base font-bold text-white shadow-xl shadow-gold-500/40 hover:from-gold-600 hover:to-gold-700 transition-all duration-200 hover:scale-105 transform cursor-pointer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        Get Tickets
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
</div>

<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="rounded-[32px] border border-slate-200 bg-white/90 p-8 shadow-2xl shadow-slate-900/10 backdrop-blur-xl">
        <div class="grid gap-8 lg:grid-cols-[1.5fr_auto] lg:items-start">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-gold-600">Event Details</p>
                <h2 class="mt-4 text-3xl font-extrabold text-slate-900 sm:text-4xl">What to expect at {{ $event->name }}</h2>
                @if($event->description)
                <div class="prose prose-slate mt-5 max-w-3xl prose-headings:font-bold prose-headings:text-slate-900 prose-p:text-slate-600 prose-p:leading-8 prose-li:text-slate-600 prose-a:text-gold-600 prose-img:max-w-full prose-img:rounded-xl">{!! $event->description_html !!}</div>
                @endif

                @php
                $focusAreas = $event->topics->pluck('name')->filter();
                $highlights = $event->ticketTypes->flatMap(fn($ticket) => $ticket->highlights ?? [])->filter();
                @endphp

                @if($focusAreas->isNotEmpty() || $highlights->isNotEmpty())
                <div class="mt-8 grid gap-4 sm:grid-cols-2">
                    @if($focusAreas->isNotEmpty())
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Focus areas</p>
                        <ul class="mt-4 space-y-3 text-sm text-slate-700">
                            @foreach($focusAreas as $area)
                            <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-gold-500"></span>{{ $area }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($highlights->isNotEmpty())
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Why attend</p>
                        <ul class="mt-4 space-y-3 text-sm text-slate-700">
                            @foreach($highlights as $highlight)
                            <li class="flex gap-3"><span class="mt-1 h-2.5 w-2.5 rounded-full bg-gold-500"></span>{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <div class="space-y-4">
                <div class="rounded-[28px] bg-slate-950 p-6 text-white shadow-xl shadow-slate-900/20">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Quick facts</p>
                    <div class="mt-5 space-y-4 text-sm">
                        <div class="rounded-3xl bg-white/5 p-4">
                            <p class="text-[10px] uppercase tracking-[0.35em] text-slate-400">When</p>
                            <p class="mt-2 font-semibold text-white">{{ $event->start_date->format('M d, Y') }} – {{ $event->end_date->format('M d, Y') }}</p>
                            <p class="text-slate-400">{{ $event->timezone }}</p>
                        </div>
                        <div class="rounded-3xl bg-white/5 p-4">
                            <p class="text-[10px] uppercase tracking-[0.35em] text-slate-400">Where</p>
                            <p class="mt-2 font-semibold text-white">{{ $event->venue->name ?? 'Online' }}</p>
                            @if($event->venue?->address)<p class="text-slate-400">{{ $event->venue->address }}</p>@endif
                        </div>
                        <div class="rounded-3xl bg-white/5 p-4">
                            <p class="text-[10px] uppercase tracking-[0.35em] text-slate-400">Type</p>
                            <p class="mt-2 font-semibold text-white">{{ ucfirst($event->event_type) }}</p>
                        </div>
                        @if($event->start_date->isFuture())
                        <div class="rounded-3xl bg-amber-500/10 border border-amber-500/20 p-4" x-data="countdown('{{ $event->start_date->toIso8601String() }}')" x-init="start()">
                            <p class="text-[10px] uppercase tracking-[0.35em] text-amber-300 font-semibold">Event starts in</p>
                            <div class="mt-3 grid grid-cols-4 gap-1.5 text-center">
                                <div class="rounded-lg bg-amber-500/10 p-1.5">
                                    <span class="block text-sm font-extrabold text-amber-300" x-text="days"></span>
                                    <span class="text-[9px] font-semibold text-amber-400/70 uppercase tracking-wide">Days</span>
                                </div>
                                <div class="rounded-lg bg-amber-500/10 p-1.5">
                                    <span class="block text-sm font-extrabold text-amber-300" x-text="hours"></span>
                                    <span class="text-[9px] font-semibold text-amber-400/70 uppercase tracking-wide">Hrs</span>
                                </div>
                                <div class="rounded-lg bg-amber-500/10 p-1.5">
                                    <span class="block text-sm font-extrabold text-amber-300" x-text="mins"></span>
                                    <span class="text-[9px] font-semibold text-amber-400/70 uppercase tracking-wide">Min</span>
                                </div>
                                <div class="rounded-lg bg-amber-500/10 p-1.5">
                                    <span class="block text-sm font-extrabold text-amber-300" x-text="secs"></span>
                                    <span class="text-[9px] font-semibold text-amber-400/70 uppercase tracking-wide">Sec</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    <div class="grid gap-8 lg:grid-cols-[1fr_340px] lg:items-start">

        {{-- Main Content --}}
        <div x-data="{ tab: 'program' }">
            {{-- Tab Navigation --}}
            <div class="border-b border-slate-200 mb-8">
                <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">
                    <button @click="tab = 'program'" :class="tab === 'program' ? 'border-gold-500 text-gold-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="border-b-2 py-4 px-1 text-sm font-semibold whitespace-nowrap">Program & Speakers</button>
                    <button @click="tab = 'sessions'" :class="tab === 'sessions' ? 'border-gold-500 text-gold-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="border-b-2 py-4 px-1 text-sm font-semibold whitespace-nowrap">Schedule</button>
                    <button @click="tab = 'sponsors'" :class="tab === 'sponsors' ? 'border-gold-500 text-gold-600' : 'border-transparent text-slate-500 hover:border-slate-300 hover:text-slate-700'" class="border-b-2 py-4 px-1 text-sm font-semibold whitespace-nowrap">Sponsors</button>
                </nav>
            </div>

            {{-- Tab: Program & Speakers --}}
            <div x-show="tab === 'program'" class="space-y-10">

                {{-- Training Topics --}}
                @if($event->topics->isNotEmpty())
                <div>
                    <div class="flex items-center gap-3 mb-5 pb-3 border-b border-slate-100">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg></div>
                        <h2 class="text-lg font-bold text-slate-900">Training Topics</h2>
                        <span class="text-xs text-slate-400 font-semibold bg-slate-100 rounded-full px-2.5 py-0.5">{{ $event->topics->count() }}</span>
                    </div>
                    <div class="space-y-3">
                        @foreach($event->topics as $index => $topic)
                        <div class="relative rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all hover:border-blue-200">
                            <div class="flex items-start gap-4">
                                <div class="h-8 w-8 rounded-lg bg-blue-50 flex items-center justify-center text-sm font-bold text-blue-600 shrink-0">{{ $index + 1 }}</div>
                                <div>
                                    <h3 class="font-bold text-slate-900">{{ $topic->name }}</h3>
                                    @if($topic->description)<p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $topic->description }}</p>@endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Keynote Speakers --}}
                @if($event->speakers->where('pivot.featured', true)->isNotEmpty())
                <div>
                    <div class="flex items-center gap-3 mb-5 pb-3 border-b border-slate-100">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center text-white shadow-md"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg></div>
                        <h2 class="text-lg font-bold text-slate-900">Keynote Speakers</h2>
                        <span class="text-xs text-slate-400 font-semibold bg-slate-100 rounded-full px-2.5 py-0.5">{{ $event->speakers->where('pivot.featured', true)->count() }}</span>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        @foreach($event->speakers->where('pivot.featured') as $speaker)
                        <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-lg transition-all hover:border-purple-200 hover:-translate-y-0.5">
                            <div class="flex items-start gap-4">
                                <img src="{{ $speaker->photo_url }}" alt="{{ $speaker->name }}" class="h-14 w-14 rounded-full object-cover shrink-0 ring-2 ring-purple-100 bg-slate-100">
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-slate-900">{{ $speaker->name }}</h3>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $speaker->job_title ?? 'Speaker' }}@if($speaker->company) · {{ $speaker->company }}@endif</p>
                                    @if($speaker->pivot->speak_time)<div class="mt-2 inline-flex items-center gap-1 rounded-full bg-gold-50 px-2.5 py-1 text-[10px] font-bold text-gold-700 border border-gold-200"><svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg> {{ date('h:i A', strtotime($speaker->pivot->speak_time)) }}</div>@endif
                                </div>
                            </div>
                            @if($speaker->bio)<p class="text-sm text-slate-600 mt-3 pt-3 border-t border-slate-100 leading-relaxed">{{ $speaker->bio }}</p>@endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- All Speakers --}}
                @if($event->speakers->where('pivot.featured', false)->isNotEmpty())
                <div>
                    <div class="flex items-center gap-3 mb-5 pb-3 border-b border-slate-100">
                        <div class="h-8 w-8 rounded-lg bg-gradient-to-br from-teal-500 to-teal-600 flex items-center justify-center text-white shadow-md"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg></div>
                        <h2 class="text-lg font-bold text-slate-900">All Speakers</h2>
                        <span class="text-xs text-slate-400 font-semibold bg-slate-100 rounded-full px-2.5 py-0.5">{{ $event->speakers->where('pivot.featured', false)->count() }}</span>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        @foreach($event->speakers->where('pivot.featured', false) as $speaker)
                        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm hover:shadow-md transition-all hover:border-teal-200 flex items-start gap-3">
                            <img src="{{ $speaker->photo_url }}" alt="{{ $speaker->name }}" class="h-10 w-10 rounded-full object-cover shrink-0 bg-slate-100">
                            <div>
                                <h3 class="font-bold text-slate-900 text-sm">{{ $speaker->name }}</h3>
                                <p class="text-xs text-slate-500">{{ $speaker->job_title }}@if($speaker->company) · {{ $speaker->company }}@endif</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($event->speakers->where('pivot.featured', true)->isEmpty() && $event->speakers->where('pivot.featured', false)->isEmpty() && $event->topics->isEmpty())
                <p class="text-sm text-slate-500 text-center py-8">No program content added yet.</p>
                @endif
            </div>

            {{-- Tab: Schedule --}}
            <div x-show="tab === 'sessions'" class="space-y-4" style="display: none;">
                @if($event->sessions->isNotEmpty())
                @foreach($event->sessions as $session)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <span class="inline-flex items-center rounded-full bg-gold-100 px-3 py-1 text-xs font-bold text-gold-700">{{ $session->sessionType->name ?? 'Presentation' }}</span>
                        <span class="flex items-center gap-1 text-xs text-slate-500 bg-slate-50 rounded-lg px-2.5 py-1 shrink-0"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg> {{ $session->start_time->format('h:i A') }} – {{ $session->end_time->format('h:i A') }}</span>
                    </div>
                    <h3 class="font-bold text-slate-900">{{ $session->title }}</h3>
                    @if($session->description)<p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $session->description }}</p>@endif
                    <div class="flex flex-wrap items-center justify-between gap-3 mt-3 pt-3 border-t border-slate-100">
                        <div class="flex items-center gap-3 flex-wrap">
                            @foreach($session->speakers as $sp)
                            <div class="flex items-center gap-1.5 text-xs">
                                <div class="h-6 w-6 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-[8px] font-bold text-white shadow-sm">{{ strtoupper(substr($sp->name, 0, 1)) }}</div><span class="font-semibold text-slate-700">{{ $sp->name }}</span>
                            </div>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-2">
                            @if($session->room)<span class="text-[10px] text-slate-400 bg-slate-50 rounded-lg px-2 py-1"><svg class="h-3 w-3 inline mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg> {{ $session->room }}</span>@endif
                            @if($session->track)<span class="track-badge text-[10px] font-bold rounded-lg px-2 py-1 border" data-track-color="{{ $session->track->color }}">{{ $session->track->name }}</span>@endif
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <p class="text-sm text-slate-500 text-center py-8">No scheduled sessions published yet.</p>
                @endif
            </div>

            {{-- Tab: Sponsors --}}
            <div x-show="tab === 'sponsors'" class="grid grid-cols-2 gap-4 sm:grid-cols-3" style="display: none;">
                @if($event->sponsors->isNotEmpty())
                @foreach($event->sponsors as $sponsor)
                <div class="rounded-xl border border-slate-200 bg-white p-6 flex flex-col items-center justify-center text-center shadow-sm hover:shadow-lg transition-all hover:border-gold-200 hover:-translate-y-0.5">
                    @if($sponsor->image_url)<img src="{{ $sponsor->image_url }}" alt="{{ $sponsor->name }}" class="h-16 w-16 rounded-xl object-cover mb-3 bg-slate-50 ring-2 ring-slate-100">@else<div class="h-16 w-16 rounded-xl bg-gradient-to-br from-gold-100 to-gold-200 flex items-center justify-center mb-3"><span class="font-bold text-gold-600 text-lg">{{ strtoupper(substr($sponsor->name, 0, 1)) }}</span></div>@endif
                    <span class="text-sm font-bold text-slate-900">{{ $sponsor->name }}</span>
                    <span class="mt-1.5 inline-flex items-center rounded-full bg-gold-100 px-3 py-1 text-[10px] font-bold text-gold-700 capitalize tracking-wide">{{ $sponsor->level }}</span>
                </div>
                @endforeach
                @else
                <p class="text-sm text-slate-500 text-center py-8 col-span-3">No sponsors added yet.</p>
                @endif
            </div>

        </div>

        {{-- Ticket Sidebar --}}
        @if($event->ticketTypes->isNotEmpty())
        @php
            $hasAvailable = $event->ticketTypes->contains(fn($tt) => ($tt->quantity - $tt->sold_count) > 0);
            $cheapest = $event->ticketTypes->where(fn($tt) => ($tt->quantity - $tt->sold_count) > 0)->sortBy(fn($tt) => $tt->payablePrice())->first();
            $totalSold = $event->ticketTypes->sum('sold_count');
            $totalCapacity = $event->ticketTypes->sum('quantity');
            $totalRemaining = max(0, $totalCapacity - $totalSold);
            $fillPercent = $totalCapacity > 0 ? min(100, round(($totalSold / $totalCapacity) * 100)) : 0;
        @endphp
        <div class="lg:sticky lg:top-24 space-y-4" x-data="{ displayCurrency: 'EUR' }">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                <div class="bg-slate-900 px-5 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-white">Get Tickets</h3>
                        @if($cheapest && $cheapest->payablePrice() > 0)
                        <span class="text-xs text-slate-400">Price <span class="text-gold-400 text-base font-extrabold" @if($cheapest->usdReferencePrice() !== null) x-text="displayCurrency === 'EUR' ? '{{ number_format($cheapest->payablePrice(), 2) }} EUR' : '{{ number_format($cheapest->usdReferencePrice(), 2) }} USD'" @endif>{{ number_format($cheapest->payablePrice(), 2) }} {{ $cheapest->checkoutCurrency() }}</span></span>
                        @endif
                    </div>
                </div>
                <div class="p-5">
                    @foreach($event->ticketTypes as $ticketType)
                    @php
                        $remaining = $ticketType->quantity - $ticketType->sold_count;
                        $isSoldOut = $remaining <= 0;
                        $soldPct = $ticketType->quantity > 0 ? min(100, round(($ticketType->sold_count / $ticketType->quantity) * 100)) : 0;
                        $onSale = (!$ticketType->sales_start || $ticketType->sales_start->isPast())
                            && (!$ticketType->sales_end || $ticketType->sales_end->isFuture());
                    @endphp
                    <div class="py-3 {{ !$loop->last ? 'border-b border-slate-100' : '' }} {{ $isSoldOut ? 'opacity-50' : '' }}">
                        <div class="flex items-center justify-between mb-1.5">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">{{ $ticketType->name }}</p>
                                @if($ticketType->description)
                                <p class="text-[11px] text-slate-500 mt-0.5 leading-tight">{{ Str::limit($ticketType->description, 50) }}</p>
                                @endif
                            </div>
                            <div class="text-right shrink-0 ml-3">
                                @if($ticketType->payablePrice() > 0)
                                    @if($ticketType->usdReferencePrice() !== null)
                                    <div class="flex items-center justify-end gap-2">
                                        <p class="text-lg font-extrabold text-slate-900" x-text="displayCurrency === 'EUR' ? '{{ number_format($ticketType->payablePrice(), 2) }} EUR' : '{{ number_format($ticketType->usdReferencePrice(), 2) }} USD'">{{ number_format($ticketType->payablePrice(), 2) }} EUR</p>
                                        <div class="inline-flex rounded-lg border border-slate-200 bg-white p-0.5" role="group" aria-label="Displayed currency">
                                            <button type="button" title="Show EUR price" aria-label="Show EUR price" :aria-pressed="displayCurrency === 'EUR'" @click="displayCurrency = 'EUR'" :class="displayCurrency === 'EUR' ? 'bg-gold-500 text-white' : 'text-slate-500 hover:bg-slate-100'" class="h-7 w-7 rounded-md text-sm font-bold transition-colors">€</button>
                                            <button type="button" title="Show USD price" aria-label="Show USD price" :aria-pressed="displayCurrency === 'USD'" @click="displayCurrency = 'USD'" :class="displayCurrency === 'USD' ? 'bg-gold-500 text-white' : 'text-slate-500 hover:bg-slate-100'" class="h-7 w-7 rounded-md text-sm font-bold transition-colors">$</button>
                                        </div>
                                    </div>
                                    <p x-show="displayCurrency === 'USD'" class="text-xs text-slate-500">Checkout charges EUR</p>
                                    @else
                                    <p class="text-lg font-extrabold text-slate-900">{{ number_format($ticketType->payablePrice(), 2) }} <span class="text-xs font-semibold text-slate-500">{{ $ticketType->checkoutCurrency() }}</span></p>
                                    @endif
                                @else
                                    <p class="text-sm font-extrabold text-emerald-600">Free</p>
                                @endif
                            </div>
                        </div>

                        {{-- Availability status (no slot counts) --}}
                        @if($isSoldOut)
                        <p class="mt-2 text-[10px] font-semibold text-red-500">Sold Out</p>
                        @elseif(!$onSale && $ticketType->sales_start && $ticketType->sales_start->isFuture())
                        <p class="mt-2 text-[10px] font-semibold text-slate-500">Sales open {{ $ticketType->sales_start->format('M d') }}</p>
                        @elseif($onSale && $ticketType->sales_end && $ticketType->sales_end->isFuture() && $ticketType->sales_end->diffInDays(now()) <= 3)
                        <p class="mt-2 text-[10px] font-semibold text-amber-600 flex items-center gap-1">
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Sales end in {{ $ticketType->sales_end->diffForHumans(null, true) }}
                        </p>
                        @endif
                    </div>
                    @endforeach

                    {{-- Sales window countdown --}}
                    @php
                        $nextSaleStart = $event->ticketTypes
                            ->filter(fn($tt) => $tt->sales_start && $tt->sales_start->isFuture())
                            ->sortBy('sales_start')->first();
                    @endphp
                    @if($nextSaleStart)
                    <div class="mt-4 rounded-xl bg-amber-50 border border-amber-200 p-3 text-center">
                        <p class="text-[11px] font-semibold text-amber-700">Ticket sales start {{ $nextSaleStart->sales_start->format('M d, Y') }}</p>
                    </div>
                    @endif

                    @if($hasAvailable)
                    <div class="mt-4 space-y-2">
                        <a href="{{ route('checkout.show', $event->slug) }}"
                            class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:from-gold-600 hover:to-gold-700 hover:shadow-xl transition-all duration-200 w-full hover:scale-[1.02]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                            Buy Tickets
                        </a>
                    </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-slate-100" x-data="{ copied: false, eventUrl: '{{ route('events.show', $event->slug) }}' }">
                        <button @click="navigator.clipboard.writeText(eventUrl).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                            class="flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all duration-200 w-full">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                            <span x-text="copied ? 'Copied!' : 'Share Event Link'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Enquiry Modal --}}
<div x-data="{ enquiryModal: false }">
    @if(session('enquiry_success'))
    <div class="fixed bottom-4 right-4 z-50 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700 shadow-lg">{{ session('enquiry_success') }}</div>
    @endif
    <button @@click="enquiryModal = true" class="fixed bottom-4 left-4 z-40 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/30 hover:shadow-xl hover:from-gold-600 hover:to-gold-700 transition-all duration-200 cursor-pointer"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg> Request Training Agenda</button>
    <div x-show="enquiryModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="enquiryModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="enquiryModal = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="enquiryModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="enquiryModal = false" class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Request Training Agenda</h2>
                        <p class="text-sm text-slate-500 mt-1">Fill in your details and we'll send you the full training agenda.</p>
                    </div>
                    <button @@click="enquiryModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <form action="{{ route('enquiries.store') }}" method="POST" onsubmit="var b=this.querySelector('button[type=submit]');b.disabled=true;b.innerHTML='Submitting...';">@csrf<input type="hidden" name="event_id" value="{{ $event->id }}">
                    <div style="position:absolute;left:-9999px" aria-hidden="true">
                        <input type="text" name="website" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Your Name <span class="text-red-500">*</span></label><input name="name" type="text" required maxlength="255" placeholder="e.g. Juan Dela Cruz" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Contact Number / Email <span class="text-red-500">*</span></label><input name="contact" type="text" required maxlength="255" placeholder="e.g. +63 912 345 6789 or email@example.com" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Company</label><input name="company" type="text" maxlength="255" placeholder="e.g. ABC Corporation" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Message <span class="text-red-500">*</span></label><textarea name="message" rows="3" required maxlength="5000" placeholder="Any specific questions or topics you'd like covered..." class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none"></textarea></div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @@click="enquiryModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:shadow-xl hover:from-gold-600 hover:to-gold-700 transition-all cursor-pointer"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg> Submit Enquiry</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function saveButton(initialSaved) {
    return {
        saved: initialSaved,
        loading: false,
        async toggle() {
            if (this.loading) return;
            this.loading = true;
            var el = this.$el;
            var saveUrl = el.dataset.saveUrl;
            var eventId = parseInt(el.dataset.eventId, 10);
            try {
                var res = await fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ event_id: eventId }),
                });
                var data = await res.json();
                if (res.ok) this.saved = data.saved;
            } catch (e) { /* silent */ }
            this.loading = false;
        },
    };
}

function countdown(targetIso) {
    var target = new Date(targetIso).getTime();
    return {
        days: '--',
        hours: '--',
        mins: '--',
        secs: '--',
        timer: null,
        start() {
            this.tick();
            this.timer = setInterval(function() { this.tick(); }.bind(this), 1000);
        },
        tick() {
            var now = Date.now();
            var diff = Math.max(0, target - now);
            this.days = String(Math.floor(diff / 86400000)).padStart(2, '0');
            this.hours = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
            this.mins = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
            this.secs = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
            if (diff <= 0) clearInterval(this.timer);
        },
    };
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-track-color]').forEach(function(el) {
        var color = el.dataset.trackColor;
        if (!color) return;
        el.style.backgroundColor = color + '15';
        el.style.color = color;
        el.style.borderColor = color + '30';
    });

    document.querySelectorAll('[data-progress]').forEach(function(el) {
        el.style.width = el.dataset.progress + '%';
    });
});
</script>
@endsection
