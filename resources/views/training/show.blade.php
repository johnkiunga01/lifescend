@extends('layouts.dashboard')

@section('pageTitle', 'Training Request')

@section('content')
<div class="py-6">
    <div class="mb-6">
        <a href="{{ route('training.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Training Requests
        </a>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden mb-6">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-gold-500 to-gold-600 px-8 py-6">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-2xl bg-white/20 flex items-center justify-center backdrop-blur-sm shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-white sm:text-3xl">{{ $trainingRequest->title }}</h1>
                        <p class="text-sm text-gold-200 mt-1">Training Request</p>
                    </div>
                </div>
                @php
                    $statusColors = ['pending' => 'bg-amber-100 text-amber-700', 'approved' => 'bg-emerald-100 text-emerald-700', 'rejected' => 'bg-red-100 text-red-700', 'completed' => 'bg-slate-100 text-slate-600'];
                    $colorClass = $statusColors[$trainingRequest->status] ?? $statusColors['pending'];
                @endphp
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-3.5 py-1.5 text-xs font-bold shadow-sm {{ $colorClass }}">{{ ucfirst($trainingRequest->status) }}</span>
                    @if($trainingRequest->status === 'pending' && $trainingRequest->user_id === Auth::id())
                        <a href="{{ route('training.edit', $trainingRequest->slug) }}" class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3.5 py-1.5 text-xs font-bold text-white hover:bg-white/30 transition-all">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Edit
                        </a>
                    @endif
                </div>
            </div>

            {{-- Requester info --}}
            <div class="flex items-center gap-3 mt-5 pt-5 border-t border-white/20">
                <div class="h-10 w-10 rounded-full bg-white/20 flex items-center justify-center text-sm font-bold text-white">
                    {{ strtoupper(substr($trainingRequest->contact_name ?? $trainingRequest->user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ $trainingRequest->contact_name ?? $trainingRequest->user->name }}</p>
                    <p class="text-xs text-gold-200/80">{{ $trainingRequest->contact_email ?? $trainingRequest->user->email }}</p>
                    @if($trainingRequest->contact_phone)
                    <p class="text-xs text-gold-200/60 mt-0.5">{{ $trainingRequest->contact_phone }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-8">
            {{-- Details Grid --}}
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500">Delivery Format</p>
                    <div class="mt-3 flex items-center gap-2.5">
                        @if($trainingRequest->training_type === 'online')
                            <div class="h-9 w-9 rounded-lg bg-gold-100 flex items-center justify-center"><svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg></div>
                            <span class="font-bold text-slate-900">Online</span>
                        @else
                            <div class="h-9 w-9 rounded-lg bg-emerald-100 flex items-center justify-center"><svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg></div>
                            <span class="font-bold text-slate-900">In-House</span>
                        @endif
                    </div>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500">Preferred Date</p>
                    <p class="mt-3 font-bold text-slate-900">{{ $trainingRequest->preferred_date->format('F d, Y') }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500">Estimated Attendees</p>
                    <p class="mt-3 font-bold text-slate-900">{{ $trainingRequest->estimated_attendees ? number_format($trainingRequest->estimated_attendees) : 'Not specified' }}</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500">Submitted</p>
                    <p class="mt-3 font-bold text-slate-900">{{ $trainingRequest->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            {{-- Description --}}
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-8">
                <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500 mb-4">Description & Objectives</p>
                <p class="text-sm text-slate-700 leading-relaxed">{{ $trainingRequest->description }}</p>
            </div>

            {{-- Linked Event --}}
            @if($trainingRequest->event)
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-8 flex items-center gap-4">
                <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500">Linked Event</p>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">{{ $trainingRequest->event->name }}</p>
                </div>
                <a href="{{ route('events.show', $trainingRequest->event->slug) }}" class="ml-auto text-xs font-bold text-gold-600 hover:text-gold-700 transition-colors">View Event &rarr;</a>
            </div>
            @endif

            {{-- Timeline --}}
            @if($trainingRequest->created_at)
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-slate-500 mb-6">Timeline</p>
                <div class="relative pl-8 before:absolute before:left-3 before:top-2 before:h-[calc(100%-1rem)] before:w-0.5 before:bg-slate-200">
                    <div class="relative mb-6 last:mb-0">
                        <div class="absolute -left-6 top-0.5 h-4 w-4 rounded-full border-2 border-gold-500 bg-white"></div>
                        <p class="text-sm font-bold text-slate-900">Request Submitted</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $trainingRequest->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    @if($trainingRequest->status === 'approved' || $trainingRequest->status === 'completed')
                    <div class="relative mb-6 last:mb-0">
                        <div class="absolute -left-6 top-0.5 h-4 w-4 rounded-full border-2 border-emerald-500 bg-white"></div>
                        <p class="text-sm font-bold text-slate-900">Approved</p>
                        <p class="text-xs text-slate-500 mt-0.5">Your training request has been approved</p>
                    </div>
                    @endif
                    @if($trainingRequest->status === 'completed')
                    <div class="relative last:mb-0">
                        <div class="absolute -left-6 top-0.5 h-4 w-4 rounded-full border-2 border-emerald-500 bg-emerald-500"></div>
                        <p class="text-sm font-bold text-slate-900">Completed</p>
                        <p class="text-xs text-slate-500 mt-0.5">Training has been completed</p>
                    </div>
                    @endif
                    @if($trainingRequest->status === 'rejected')
                    <div class="relative last:mb-0">
                        <div class="absolute -left-6 top-0.5 h-4 w-4 rounded-full border-2 border-red-500 bg-white"></div>
                        <p class="text-sm font-bold text-slate-900">Not Approved</p>
                        <p class="text-xs text-slate-500 mt-0.5">This request was not approved at this time</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Accept Action --}}
    @php
        $role = Auth::user()->organizations()->first()?->pivot?->role ?? 'customer';
    @endphp
    @if(in_array($role, ['admin', 'manager', 'staff']) && $trainingRequest->status === 'pending')
    <div x-data="{ showAcceptModal: false }">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Review Request</h3>
                <p class="text-xs text-slate-500 mt-1">Accept this training request and notify the requester.</p>
            </div>
            <button @@click="showAcceptModal = true" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:bg-emerald-600 transition-all cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                Accept Request
            </button>
        </div>

        <div x-show="showAcceptModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="showAcceptModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="showAcceptModal = false" class="fixed inset-0 bg-black/60"></div>
                <div x-show="showAcceptModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="showAcceptModal = false"
                     class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">Accept Request</h2>
                            <p class="text-sm text-slate-500 mt-1">Send an acceptance message to the requester.</p>
                        </div>
                        <button @@click="showAcceptModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form action="{{ route('training.accept', $trainingRequest->slug) }}" method="POST" class="space-y-4" onsubmit="var b=this.querySelector('button[type=submit]');b.disabled=true;b.innerHTML='Approving...';">
                        @csrf
                        <div>
                            <label for="accept-message" class="block text-sm font-medium text-slate-700 mb-1.5">Custom Message <span class="text-red-500">*</span></label>
                            <textarea id="accept-message" name="message" rows="5" required maxlength="5000" placeholder="Write a personalized message..." class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-emerald-500 focus:outline-none sm:text-sm resize-none"></textarea>
                            <p class="mt-1.5 text-xs text-slate-500">This message will be sent to {{ $trainingRequest->user->name }} via email.</p>
                        </div>
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button" @@click="showAcceptModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:bg-emerald-600 transition-all cursor-pointer">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Approve & Send Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Schedule Action --}}
    @if(in_array($role, ['admin', 'manager', 'staff']) && $trainingRequest->status === 'approved' && !$trainingRequest->event_id)
    <div x-data="{ showScheduleModal: false }">
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Schedule Training</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Create an event from this approved training request.</p>
                </div>
            </div>
            <button @@click="showScheduleModal = true" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Schedule Now
            </button>
        </div>

        <div x-show="showScheduleModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="showScheduleModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="showScheduleModal = false" class="fixed inset-0 bg-black/60"></div>
                <div x-show="showScheduleModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="showScheduleModal = false"
                     x-data="{
                        step: 1,
                        visibility: 'public',
                        venueType: 'online',
                        pricing: [],
                        topics: [],
                        keynotes: [],
                        addPricing() { this.pricing.push({ name: '', description: '', price: '', discount_price: '', quantity: '100', highlights: '' }); },
                        removePricing(i) { this.pricing.splice(i, 1); },
                        addTopic() { this.topics.push({ name: '', description: '' }); },
                        removeTopic(i) { this.topics.splice(i, 1); },
                        addKeynote() { this.keynotes.push({ speaker_id: '', speak_time: '' }); },
                        removeKeynote(i) { this.keynotes.splice(i, 1); },
                        next() { this.step++; },
                        prev() { this.step--; }
                    }"
                     class="relative w-full max-w-2xl rounded-2xl bg-white p-8 shadow-2xl overflow-y-auto max-h-[90vh]">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900">Schedule Training</h2>
                            <p class="text-sm text-slate-500 mt-1">Configure the event across all sections.</p>
                        </div>
                        <button @@click="showScheduleModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    {{-- Step indicator --}}
                    <div class="flex items-center gap-2 mb-6">
                        <template x-for="(s, i) in ['Basic Info &amp; Details', 'Pricing &amp; Cover', 'Program &amp; Partners', 'Visibility &amp; Submit']" :key="i">
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-[11px] font-bold"
                                          :class="step > i + 1 ? 'bg-emerald-500 text-white' : (step === i + 1 ? 'bg-gold-500 text-white' : 'bg-slate-200 text-slate-500')"
                                          x-text="i + 1"></span>
                                    <span class="text-[11px] font-semibold hidden sm:inline"
                                          :class="step === i + 1 ? 'text-gold-700' : 'text-slate-400'" x-html="s"></span>
                                </div>
                                <div x-show="i < 3" class="w-6 h-px bg-slate-300"></div>
                            </div>
                        </template>
                    </div>

                    <form action="{{ route('training.schedule', $trainingRequest->slug) }}" method="POST" enctype="multipart/form-data"
                          class="space-y-4" onsubmit="var b=this.querySelector('button[type=submit]');b.disabled=true;b.innerHTML='Creating...';">
                        @csrf

                        {{-- Step 1: Basic Info & Details --}}
                        <div x-show="step === 1">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-4">
                                <h3 class="text-sm font-bold text-slate-900 mb-4">Basic Information</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Event Name <span class="text-red-500">*</span></label>
                                        <input name="name" type="text" value="{{ $trainingRequest->title }}" required maxlength="255" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                                        <textarea name="description" rows="3" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm resize-none">{{ $trainingRequest->description }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <h3 class="text-sm font-bold text-slate-900 mb-4">Event Details</h3>
                                <div class="space-y-4">
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Event Type <span class="text-red-500">*</span></label>
                                            <select name="event_type" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                                @foreach($eventTypes as $et)
                                                    <option value="{{ $et->slug }}">{{ $et->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Venue</label>
                                            <select name="venue_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                                <option value="">— No venue —</option>
                                                @foreach($venues as $v)
                                                    <option value="{{ $v->id }}">{{ $v->name }}, {{ $v->city }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">Start Date <span class="text-red-500">*</span></label>
                                            <input name="start_date" type="date" value="{{ $trainingRequest->preferred_date->format('Y-m-d') }}" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 mb-1">End Date <span class="text-red-500">*</span></label>
                                            <input name="end_date" type="date" value="{{ $trainingRequest->preferred_date->format('Y-m-d') }}" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Timezone</label>
                                        <input name="timezone" type="text" value="UTC" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="flex justify-end pt-3">
                                <button type="button" @@click="next()" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                                    Next: Pricing & Cover <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Step 2: Pricing & Cover --}}
                        <div x-show="step === 2">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-4">
                                <h3 class="text-sm font-bold text-slate-900 mb-4">Cover Image</h3>
                                <div class="rounded-xl border-2 border-dashed border-slate-300 bg-white h-28 flex items-center justify-center cursor-pointer group hover:border-gold-400 transition-colors" @@click="$refs.coverInput.click()">
                                    <svg class="h-7 w-7 text-slate-400 group-hover:text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="ml-2 text-xs text-slate-400 group-hover:text-gold-600">Click to upload cover image</span>
                                    <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" x-ref="coverInput" class="hidden">
                                </div>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-amber-50 p-5 mb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-bold text-slate-900">Pricing Packages</h3>
                                    <button type="button" @@click="addPricing()" class="inline-flex items-center gap-1 rounded-lg bg-gold-500 px-3 py-1.5 text-[11px] font-bold text-white hover:bg-gold-600 transition-all cursor-pointer">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add Package
                                    </button>
                                </div>
                                <div class="grid gap-4 sm:grid-cols-2 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Capacity</label>
                                        <input name="capacity" type="number" min="1" value="{{ $trainingRequest->estimated_attendees }}" placeholder="e.g. 500" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Payment Type <span class="text-red-500">*</span></label>
                                        <select name="payment_type" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                            <option value="stripe">Stripe (Card)</option>
                                        </select>
                                    </div>
                                </div>
                                <template x-for="(p, i) in pricing" :key="i">
                                    <div class="border border-amber-200 rounded-xl p-4 mb-3 bg-white">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-bold text-amber-700" x-text="'Package #' + (i + 1)"></span>
                                            <button type="button" @@click="removePricing(i)" class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer">Remove</button>
                                        </div>
                                        <div class="space-y-3">
                                            <input type="text" x-model="p.name" :name="'pricing[' + i + '][name]'" placeholder="Package name e.g. Early Bird, VIP" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none">
                                            <textarea x-model="p.description" :name="'pricing[' + i + '][description]'" rows="2" placeholder="What's included" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none resize-none"></textarea>
                                            <div class="grid gap-2 sm:grid-cols-4">
                                                <input type="number" step="0.01" x-model="p.price" :name="'pricing[' + i + '][price]'" placeholder="Price" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none">
                                                <input type="number" step="0.01" x-model="p.discount_price" :name="'pricing[' + i + '][discount_price]'" placeholder="Discounted" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none">
                                                <input type="number" x-model="p.quantity" :name="'pricing[' + i + '][quantity]'" placeholder="Qty" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none">
                                                <input type="text" x-model="p.highlights" :name="'pricing[' + i + '][highlights]'" placeholder="Highlights (comma)" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <p x-show="pricing.length === 0" class="text-xs text-slate-500 italic">No packages yet.</p>
                            </div>
                            <div class="flex items-center justify-between pt-2">
                                <button type="button" @@click="prev()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg> Back
                                </button>
                                <button type="button" @@click="next()" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                                    Next: Program & Partners <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Step 3: Program & Partners --}}
                        <div x-show="step === 3">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-bold text-slate-900">Training Topics</h3>
                                    <button type="button" @@click="addTopic()" class="inline-flex items-center gap-1 text-xs font-bold text-gold-600 hover:text-gold-700 cursor-pointer">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add Topic
                                    </button>
                                </div>
                                <template x-for="(t, i) in topics" :key="i">
                                    <div class="border border-slate-200 rounded-xl p-4 mb-3 bg-white">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-semibold text-slate-500" x-text="'Topic #' + (i + 1)"></span>
                                            <button type="button" @@click="removeTopic(i)" class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer">Remove</button>
                                        </div>
                                        <div class="mb-3"><input type="text" x-model="t.name" :name="'topics[' + i + '][name]'" placeholder="Topic title" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none"></div>
                                        <div><textarea x-model="t.description" :name="'topics[' + i + '][description]'" rows="2" placeholder="Brief description" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none resize-none"></textarea></div>
                                    </div>
                                </template>
                                <p x-show="topics.length === 0" class="text-xs text-slate-500 italic">No topics added.</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-4">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-sm font-bold text-slate-900">Keynote Speakers</h3>
                                    <button type="button" @@click="addKeynote()" class="inline-flex items-center gap-1 text-xs font-bold text-gold-600 hover:text-gold-700 cursor-pointer">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add Keynote
                                    </button>
                                </div>
                                <template x-for="(k, i) in keynotes" :key="i">
                                    <div class="border border-slate-200 rounded-xl p-4 mb-3 bg-white">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-xs font-semibold text-slate-500" x-text="'Keynote #' + (i + 1)"></span>
                                            <button type="button" @@click="removeKeynote(i)" class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer">Remove</button>
                                        </div>
                                        <div class="grid gap-3 sm:grid-cols-4">
                                            <div><input type="time" x-model="k.speak_time" :name="'keynotes[' + i + '][speak_time]'" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none"></div>
                                            <div class="sm:col-span-3">
                                                <select x-model="k.speaker_id" :name="'keynotes[' + i + '][speaker_id]'" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm focus:border-gold-500 focus:outline-none">
                                                    <option value="">— Select speaker —</option>
                                                    @foreach($speakers as $s)
                                                        <option value="{{ $s->id }}">{{ $s->name }}@if($s->company) ({{ $s->company }})@endif</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <p x-show="keynotes.length === 0" class="text-xs text-slate-500 italic">No keynotes added.</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-4">
                                <h3 class="text-sm font-bold text-slate-900 mb-4">Partners & Sponsors</h3>
                                <div class="grid gap-2 sm:grid-cols-2 max-h-36 overflow-y-auto border border-slate-200 rounded-xl p-3 bg-white">
                                    @forelse($sponsors as $sp)
                                    <label class="flex items-center gap-2 cursor-pointer hover:bg-slate-50 rounded-lg px-2 py-1.5 transition-colors">
                                        <input type="checkbox" name="sponsor_ids[]" value="{{ $sp->id }}" class="h-4 w-4 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
                                        <span class="text-sm text-slate-700">{{ $sp->name }}</span>
                                    </label>
                                    @empty
                                    <p class="text-sm text-slate-400 col-span-2">No sponsors available.</p>
                                    @endforelse
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-2">
                                <button type="button" @@click="prev()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg> Back
                                </button>
                                <button type="button" @@click="next()" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                                    Next: Visibility <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                                </button>
                            </div>
                        </div>

                        {{-- Step 4: Venue, Visibility & Submit --}}
                        <div x-show="step === 4">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5 mb-4">
                                <h3 class="text-sm font-bold text-slate-900 mb-4">Venue Type</h3>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative cursor-pointer" @click="venueType = 'online'">
                                        <input type="radio" name="venue_type" value="online" class="sr-only" checked>
                                        <div :class="venueType === 'online' ? 'border-gold-500 ring-2 ring-gold-500/30 bg-gold-50' : 'border-slate-200 bg-white hover:border-gold-300'" class="flex flex-col items-center gap-2 rounded-xl border p-4 transition-all text-center">
                                            <div :class="venueType === 'online' ? 'bg-gold-500' : 'bg-slate-200'" class="h-9 w-9 rounded-lg flex items-center justify-center transition-colors">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                            </div>
                                            <p :class="venueType === 'online' ? 'text-gold-700' : 'text-slate-700'" class="font-bold text-xs">Online</p>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer" @click="venueType = 'inhouse'">
                                        <input type="radio" name="venue_type" value="inhouse" class="sr-only">
                                        <div :class="venueType === 'inhouse' ? 'border-emerald-500 ring-2 ring-emerald-500/30 bg-emerald-50' : 'border-slate-200 bg-white hover:border-emerald-300'" class="flex flex-col items-center gap-2 rounded-xl border p-4 transition-all text-center">
                                            <div :class="venueType === 'inhouse' ? 'bg-emerald-600' : 'bg-slate-200'" class="h-9 w-9 rounded-lg flex items-center justify-center transition-colors">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                            </div>
                                            <p :class="venueType === 'inhouse' ? 'text-emerald-700' : 'text-slate-700'" class="font-bold text-xs">In-House</p>
                                        </div>
                                    </label>
                                </div>
                                <div x-show="venueType === 'inhouse'" class="mt-4">
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Meeting Place <span class="text-red-500">*</span></label>
                                    <input name="meeting_place" type="text" placeholder="e.g. Conference Room A" :required="venueType === 'inhouse'" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                </div>
                                <div x-show="venueType === 'online'" class="mt-4 space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Platform</label>
                                        <input name="platform" type="text" placeholder="e.g. Zoom, Google Meet" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 mb-1">Meeting Link</label>
                                        <input name="meeting_link" type="url" placeholder="https://zoom.us/j/..." class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                    </div>
                                </div>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                                <h3 class="text-sm font-bold text-slate-900 mb-4">Visibility</h3>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative cursor-pointer" @click="visibility = 'public'">
                                        <input type="radio" name="visibility" value="public" class="sr-only" checked>
                                        <div :class="visibility === 'public' ? 'border-gold-500 ring-2 ring-gold-500/30 bg-gold-50' : 'border-slate-200 bg-white hover:border-gold-300'" class="flex flex-col items-center gap-2 rounded-xl border p-4 transition-all text-center">
                                            <div :class="visibility === 'public' ? 'bg-gold-500' : 'bg-slate-200'" class="h-9 w-9 rounded-lg flex items-center justify-center transition-colors">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                                            </div>
                                            <p :class="visibility === 'public' ? 'text-gold-700' : 'text-slate-700'" class="font-bold text-xs">Public</p>
                                        </div>
                                    </label>
                                    <label class="relative cursor-pointer" @click="visibility = 'private'">
                                        <input type="radio" name="visibility" value="private" class="sr-only">
                                        <div :class="visibility === 'private' ? 'border-purple-500 ring-2 ring-purple-500/30 bg-purple-50' : 'border-slate-200 bg-white hover:border-purple-300'" class="flex flex-col items-center gap-2 rounded-xl border p-4 transition-all text-center">
                                            <div :class="visibility === 'private' ? 'bg-purple-600' : 'bg-slate-200'" class="h-9 w-9 rounded-lg flex items-center justify-center transition-colors">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                            </div>
                                            <p :class="visibility === 'private' ? 'text-purple-700' : 'text-slate-700'" class="font-bold text-xs">Private</p>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-3">
                                <button type="button" @@click="prev()" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg> Back
                                </button>
                                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Create Event
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection