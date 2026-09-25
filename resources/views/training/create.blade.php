@extends('layouts.dashboard')

@section('pageTitle', 'Request Training')

@section('content')
<div x-data class="py-8">
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-700 mb-6 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Dashboard
        </a>

        <div class="mb-8 text-center">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gold-500 shadow-lg shadow-gold-500/30 mb-4">
                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Request Training</h1>
            <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">
                Tell us what training you need and our team will get back to you with a tailored solution.
            </p>
        </div>

        <form action="{{ route('training.store') }}" method="POST" class="space-y-6" onsubmit="var b=document.getElementById('create-submit-btn');b.disabled=true;b.innerHTML='Submitting...';">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700 shadow-sm">
                    <ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-indigo-500 flex items-center justify-center text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <div><h2 class="text-lg font-bold text-slate-900">Contact Details</h2></div>
                </div>
                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Your Name <span class="text-red-500">*</span></label>
                        <input name="contact_name" type="text" required maxlength="255" value="{{ old('contact_name', Auth::user()->name) }}" placeholder="e.g. John Doe"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Your Email <span class="text-red-500">*</span></label>
                        <input name="contact_email" type="email" required maxlength="255" value="{{ old('contact_email', Auth::user()->email) }}" placeholder="e.g. john@example.com"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                </div>
                <div class="mt-5">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                    <input name="contact_phone" type="text" maxlength="50" value="{{ old('contact_phone') }}" placeholder="e.g. +254 712 345 678"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-gold-500 flex items-center justify-center text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div><h2 class="text-lg font-bold text-slate-900">Training Details</h2></div>
                </div>

                <div x-data="{ type: '{{ old('training_type', 'online') }}' }" class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-3">Delivery Format</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer" @click="type = 'online'">
                                <input type="radio" name="training_type" value="online" class="sr-only" :checked="type === 'online'">
                                <div :class="type === 'online' ? 'border-gold-500 ring-2 ring-gold-500/30 bg-gold-50' : 'border-slate-200 bg-white hover:border-gold-300'"
                                     class="flex flex-col items-center gap-3 rounded-2xl border p-6 transition-all duration-200 text-center">
                                    <div :class="type === 'online' ? 'bg-gold-500' : 'bg-slate-200'" class="h-12 w-12 rounded-xl flex items-center justify-center transition-colors">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div>
                                        <p :class="type === 'online' ? 'text-gold-700' : 'text-slate-700'" class="font-bold text-sm">Online</p>
                                        <p class="text-xs text-slate-500 mt-1">Virtual session</p>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer" @click="type = 'inhouse'">
                                <input type="radio" name="training_type" value="inhouse" class="sr-only" :checked="type === 'inhouse'">
                                <div :class="type === 'inhouse' ? 'border-emerald-500 ring-2 ring-emerald-500/30 bg-emerald-50' : 'border-slate-200 bg-white hover:border-emerald-300'"
                                     class="flex flex-col items-center gap-3 rounded-2xl border p-6 transition-all duration-200 text-center">
                                    <div :class="type === 'inhouse' ? 'bg-emerald-600' : 'bg-slate-200'" class="h-12 w-12 rounded-xl flex items-center justify-center transition-colors">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    </div>
                                    <div>
                                        <p :class="type === 'inhouse' ? 'text-emerald-700' : 'text-slate-700'" class="font-bold text-sm">In-House</p>
                                        <p class="text-xs text-slate-500 mt-1">On-site at your premises</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Training Topic / Title <span class="text-red-500">*</span></label>
                        <input name="title" type="text" required maxlength="255" value="{{ old('title') }}" placeholder="e.g. Advanced PCR Techniques Workshop"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="4" required maxlength="5000" placeholder="Describe the training you're looking for, learning objectives, and any specific requirements..."
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200/60">
                    <div class="h-9 w-9 rounded-xl bg-blue-500 flex items-center justify-center text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    </div>
                    <div><h2 class="text-lg font-bold text-slate-900">Schedule & Participants</h2></div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Preferred Date <span class="text-red-500">*</span></label>
                        <input name="preferred_date" type="date" required value="{{ old('preferred_date') }}" min="{{ now()->addDay()->toDateString() }}"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Estimated Attendees</label>
                        <input name="estimated_attendees" type="number" min="1" max="10000" value="{{ old('estimated_attendees') }}" placeholder="e.g. 25"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-indigo-500 flex items-center justify-center text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                    </div>
                    <div><h2 class="text-lg font-bold text-slate-900">Link to Event</h2></div>
                </div>
                <div>
                    <select name="event_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                        <option value="">— Not linked to a specific event —</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>{{ $event->name }} ({{ $event->start_date->format('M Y') }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">If this training is related to a scheduled event, select it above.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-purple-500 flex items-center justify-center text-white">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </div>
                    <div><h2 class="text-lg font-bold text-slate-900">Your Message</h2></div>
                </div>
                <div>
                    <textarea name="message" rows="4" required maxlength="2000" placeholder="Any specific requirements, objectives, or details you'd like us to know..."
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none">{{ old('message') }}</textarea>
                    <p class="text-xs text-slate-400 mt-1"><span class="text-red-500">*</span> Required</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-2">
                <a href="{{ route('dashboard') }}" class="rounded-xl border-2 border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">Cancel</a>
                <button id="create-submit-btn" type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Submit Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection