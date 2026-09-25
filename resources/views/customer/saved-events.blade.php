@extends('layouts.dashboard')

@section('pageTitle', 'Saved Events')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Saved Events</h1>
    <p class="text-sm text-slate-500 mt-1">Events you've bookmarked for later.</p>
</div>

@if($events->isNotEmpty())
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($events as $event)
        <div class="group rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
            <div class="aspect-[16/9] overflow-hidden bg-slate-100">
                <img src="{{ $event->cover_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-5">
                <span class="inline-flex items-center rounded-full bg-gold-100 px-2.5 py-0.5 text-xs font-semibold text-gold-700 mb-2">{{ ucfirst($event->event_type) }}</span>
                <h3 class="text-sm font-bold text-slate-900 group-hover:text-gold-600 transition-colors mt-2 line-clamp-1">{{ $event->name }}</h3>
                <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($event->description), 140) }}</p>
                <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100">
                    <span class="text-xs text-slate-500">{{ $event->start_date->format('M d, Y') }} @if($event->venue)· {{ $event->venue->name }}@endif</span>
                    <div class="flex gap-2">
                        <a href="{{ route('events.show', $event->slug) }}" class="text-xs font-bold text-gold-600 hover:text-gold-700">View</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="text-center py-20 bg-white rounded-2xl border border-slate-200">
        <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" /></svg>
        <h3 class="text-lg font-semibold text-slate-500 mb-1">No saved events</h3>
        <p class="text-sm text-slate-400 mb-4">Browse events and save the ones you're interested in.</p>
        <a href="{{ route('events') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">Browse Events</a>
    </div>
@endif
@endsection