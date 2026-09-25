@extends('layouts.dashboard')

@section('pageTitle', 'My Calendar')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">My Calendar</h1>
    <p class="text-sm text-slate-500 mt-1">Your upcoming registered events and training requests grouped by month.</p>
</div>

@if($eventsByMonth->isNotEmpty())
    @foreach($eventsByMonth as $month => $items)
    <div class="mb-8 last:mb-0">
        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
            <svg class="h-5 w-5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            {{ $month }}
        </h2>
        <div class="space-y-4">
            @foreach($items as $item)
                @if($item->type === 'ticket')
                    @php $ticket = $item->model; @endphp
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-start gap-4">
                                <div class="text-center min-w-[48px]">
                                    <p class="text-xs font-bold text-gold-600 uppercase">{{ $ticket->event->start_date->format('M') }}</p>
                                    <p class="text-2xl font-extrabold text-slate-900 leading-none mt-0.5">{{ $ticket->event->start_date->format('d') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $ticket->event->name }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        {{ $ticket->event->start_date->format('g:i A') }} @if($ticket->event->venue)· {{ $ticket->event->venue->name }}@endif
                                    </p>
                                    <span class="inline-flex items-center rounded-full bg-gold-100 px-2 py-0.5 text-[10px] font-semibold text-gold-700 mt-1.5">{{ $ticket->ticketType->name }}</span>
                                </div>
                            </div>
                            <a href="{{ ticket_url($ticket) }}" class="text-xs font-bold text-gold-600 hover:text-gold-700 shrink-0">View Ticket</a>
                        </div>
                    </div>
                @elseif($item->type === 'training_request')
                    @php $tr = $item->model; @endphp
                    <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-all">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex items-start gap-4">
                                <div class="text-center min-w-[48px]">
                                    <p class="text-xs font-bold text-gold-600 uppercase">{{ $tr->preferred_date->format('M') }}</p>
                                    <p class="text-2xl font-extrabold text-slate-900 leading-none mt-0.5">{{ $tr->preferred_date->format('d') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $tr->title }}</p>
                                    <p class="text-xs text-slate-500 mt-0.5">
                                        @if($tr->training_type === 'online')
                                            Online Training
                                        @else
                                            In-House Training
                                        @endif
                                        @if($tr->event)· {{ $tr->event->name }}@endif
                                    </p>
                                    <div class="flex items-center gap-2 mt-1.5">
                                        @php
                                            $statusColors = ['pending' => 'bg-amber-100 text-amber-700', 'approved' => 'bg-emerald-100 text-emerald-700', 'rejected' => 'bg-red-100 text-red-700', 'completed' => 'bg-slate-100 text-slate-600'];
                                            $colorClass = $statusColors[$tr->status] ?? $statusColors['pending'];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $colorClass }}">{{ ucfirst($tr->status) }}</span>
                                        @if($tr->visibility === 'private')
                                            <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-[10px] font-semibold text-purple-700">Private</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <a href="{{ route('training.show', $tr->slug) }}" class="text-xs font-bold text-gold-600 hover:text-gold-700 shrink-0">View Details</a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endforeach
@else
    <div class="text-center py-20 bg-white rounded-2xl border border-slate-200">
        <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        <h3 class="text-lg font-semibold text-slate-500 mb-1">No upcoming events</h3>
        <p class="text-sm text-slate-400 mb-4">Register for an event or submit a training request to see it on your calendar.</p>
        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('events') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">Browse Events</a>
            <a href="{{ route('training.create') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 transition-all">Request Training</a>
        </div>
    </div>
@endif
@endsection