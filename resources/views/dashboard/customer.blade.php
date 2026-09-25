@extends('layouts.dashboard')

@section('pageTitle', 'My Hub')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">My Hub</h1>
    <p class="text-sm text-slate-500 mt-1">Training requests, event passes, and your learning journey.</p>
</div>

@if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        {{ session('success') }}
    </div>
@endif

{{-- Event Passes --}}
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm mb-6">
    <h2 class="text-lg font-bold text-slate-900 mb-5 flex items-center gap-2">
        <svg class="h-5 w-5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
        My Event Passes
    </h2>
    <div class="space-y-4">
        @forelse($tickets as $ticket)
            <a href="{{ ticket_url($ticket) }}" class="block bg-slate-50 rounded-xl p-5 border border-slate-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 hover:border-gold-300 hover:shadow-md transition-all group">
                <div>
                    <span class="inline-flex items-center rounded-full bg-gold-100 px-2.5 py-0.5 text-xs font-medium text-gold-700 mb-2">{{ $ticket->ticketType->name }}</span>
                    <h3 class="text-base font-bold text-slate-900 group-hover:text-gold-600 transition-colors">{{ $ticket->event->name }}</h3>
                    <p class="text-xs text-slate-500 mt-1">{{ $ticket->event->start_date->format('F d, Y') }}@if($ticket->event->venue) · {{ $ticket->event->venue->name }}@endif</p>
                </div>
                <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end shrink-0">
                    <div class="text-left md:text-right">
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Ticket Code</span>
                        <p class="text-sm font-mono font-bold text-slate-600">{{ $ticket->qr_code }}</p>
                    </div>
                    <div class="bg-white p-2.5 rounded-lg border border-slate-200 flex flex-col items-center justify-center">
                        <div class="h-10 w-10 bg-slate-900 rounded flex items-center justify-center font-bold text-[8px] text-white text-center leading-tight select-none overflow-hidden p-1">
                            <svg class="h-full w-full text-white/80" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="2"><rect x="10" y="10" width="80" height="80" rx="8"/><path d="M25 35h50M25 50h50M25 65h50" stroke-width="3"/></svg>
                        </div>
                        <span class="text-[8px] font-semibold mt-1 {{ $ticket->status === 'used' ? 'text-amber-600' : 'text-emerald-600' }}">{{ strtoupper($ticket->status) }}</span>
                    </div>
                </div>
            </a>
        @empty
            <div class="text-center py-10 text-slate-500 text-sm">
                <svg class="h-10 w-10 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                No event tickets yet.
            </div>
        @endforelse
    </div>
</div>

{{-- Private Trainings --}}
@if($privateTrainings->isNotEmpty())
<div class="rounded-2xl border border-purple-200 bg-white p-6 shadow-sm mb-6">
    <h2 class="text-lg font-bold text-slate-900 mb-5 flex items-center gap-2">
        <svg class="h-5 w-5 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
        My Private Trainings
        <span class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-[10px] font-bold text-purple-700 ml-1">{{ $privateTrainings->count() }}</span>
    </h2>
    <div class="space-y-4">
        @foreach($privateTrainings as $pt)
            <a href="{{ route('events.show', $pt->event->slug) }}" class="block bg-purple-50/50 rounded-xl p-5 border border-purple-200 hover:border-purple-300 hover:shadow-md transition-all group">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="inline-flex items-center gap-1 rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-semibold text-purple-700 mb-2">
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                            Private Training
                        </span>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-purple-600 transition-colors">{{ $pt->event->name }}</h3>
                        <p class="text-xs text-slate-500 mt-1">{{ $pt->event->start_date->format('F d, Y') }}@if($pt->event->venue) · {{ $pt->event->venue->name }}@endif</p>
                    </div>
                    <div class="shrink-0">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">Active</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endif

{{-- Training Requests --}}
<div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    <div class="flex items-center justify-between mb-5">
        <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
            <svg class="h-5 w-5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
            My Training Requests
        </h2>
        <a href="#" @@click.prevent="trainingModal = true" class="text-xs font-semibold text-gold-600 hover:text-gold-700">+ New Request</a>
    </div>
    <div class="space-y-4">
         @forelse($trainingRequests as $request)
            <a href="{{ route('training.show', $request->slug) }}" class="block bg-slate-50 rounded-xl p-5 border border-slate-200 hover:border-gold-300 hover:shadow-md transition-all group">
                <div class="flex flex-wrap justify-between items-start gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        @if($request->training_type === 'online')
                            <span class="inline-flex items-center gap-1 rounded-full bg-gold-100 px-2.5 py-0.5 text-xs font-semibold text-gold-700">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                Online
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                In-House
                            </span>
                        @endif
                    </div>
                    @php
                        $statusColors = ['pending' => 'bg-amber-100 text-amber-700', 'approved' => 'bg-emerald-100 text-emerald-700', 'rejected' => 'bg-red-100 text-red-700', 'completed' => 'bg-slate-100 text-slate-600'];
                        $colorClass = $statusColors[$request->status] ?? $statusColors['pending'];
                    @endphp
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $colorClass }}">{{ ucfirst($request->status) }}</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900 group-hover:text-gold-600 transition-colors">{{ $request->title }}</h3>
                <p class="text-xs text-slate-500 mt-1.5 line-clamp-2">{{ $request->description }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-4 text-[10px] text-slate-400">
                    <span>Preferred: {{ $request->preferred_date->format('M d, Y') }}</span>
                    @if($request->estimated_attendees)<span>Attendees: {{ number_format($request->estimated_attendees) }}</span>@endif
                    @if($request->event)<span>Event: {{ $request->event->name }}</span>@endif
                    <span>Submitted {{ $request->created_at->diffForHumans() }}</span>
                </div>
            </a>
        @empty
            <div class="text-center py-10 text-slate-500 text-sm">
                <svg class="h-10 w-10 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                No training requests yet.
                <button @@click="trainingModal = true" class="block mt-2 text-gold-600 hover:text-gold-700 font-semibold cursor-pointer">Submit your first training request &rarr;</button>
            </div>
        @endforelse
    </div>
</div>
@endsection
