@extends('layouts.dashboard')

@section('pageTitle', 'Edit Training Request')

@section('content')
<div class="py-8">
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('training.show', $trainingRequest->slug) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-500 hover:text-slate-700 mb-6 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Request
        </a>

        <div class="mb-8 text-center">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-gold-500 shadow-lg shadow-gold-500/30 mb-4">
                <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">
                Edit Training Request
            </h1>
            <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">
                Update your training request details below.
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-xl">
            <form action="{{ route('training.update', $trainingRequest->slug) }}" method="POST" class="space-y-6" onsubmit="var b=document.getElementById('edit-submit-btn');b.disabled=true;b.innerHTML='Updating...';">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="rounded-xl bg-red-50 border border-red-200 p-4">
                        <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Training Type Selection --}}
                <div x-data="{ type: '{{ old('training_type', $trainingRequest->training_type) }}' }">
                    <label class="block text-sm font-semibold uppercase tracking-wider text-slate-500 mb-3">
                        Training Delivery Format
                    </label>
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Online --}}
                        <label class="relative cursor-pointer" @click="type = 'online'">
                            <input type="radio" name="training_type" value="online" class="sr-only" :checked="type === 'online'">
                            <div :class="type === 'online' ? 'border-gold-500 ring-2 ring-gold-500/30 bg-gold-50' : 'border-slate-200 bg-white hover:border-gold-300'"
                                 class="flex flex-col items-center gap-3 rounded-2xl border p-6 transition-all duration-200 text-center">
                                <div :class="type === 'online' ? 'bg-gold-500' : 'bg-slate-200'"
                                     class="h-12 w-12 rounded-xl flex items-center justify-center transition-colors duration-200 shadow-lg">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p :class="type === 'online' ? 'text-gold-700' : 'text-slate-700'" class="font-bold text-sm">Online</p>
                                    <p class="text-xs text-slate-500 mt-1">Remote, virtual session via video conferencing</p>
                                </div>
                                <div :class="type === 'online' ? 'opacity-100' : 'opacity-0'" class="absolute top-3 right-3 transition-opacity">
                                    <svg class="h-5 w-5 text-gold-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </label>

                        {{-- Inhouse --}}
                        <label class="relative cursor-pointer" @click="type = 'inhouse'">
                            <input type="radio" name="training_type" value="inhouse" class="sr-only" :checked="type === 'inhouse'">
                            <div :class="type === 'inhouse' ? 'border-emerald-500 ring-2 ring-emerald-500/30 bg-emerald-50' : 'border-slate-200 bg-white hover:border-emerald-300'"
                                 class="flex flex-col items-center gap-3 rounded-2xl border p-6 transition-all duration-200 text-center">
                                <div :class="type === 'inhouse' ? 'bg-emerald-600' : 'bg-slate-200'"
                                     class="h-12 w-12 rounded-xl flex items-center justify-center transition-colors duration-200 shadow-lg">
                                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <p :class="type === 'inhouse' ? 'text-emerald-700' : 'text-slate-700'" class="font-bold text-sm">In-House</p>
                                    <p class="text-xs text-slate-500 mt-1">On-site trainer visits your premises</p>
                                </div>
                                <div :class="type === 'inhouse' ? 'opacity-100' : 'opacity-0'" class="absolute top-3 right-3 transition-opacity">
                                    <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Estimated Attendees --}}
                <div x-data="{ type: '{{ old('training_type', $trainingRequest->training_type) }}' }">
                    <label for="estimated_attendees" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Estimated Number of Attendees <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <input id="estimated_attendees" name="estimated_attendees" type="number"
                           placeholder="e.g. 25"
                           value="{{ old('estimated_attendees', $trainingRequest->estimated_attendees) }}"
                           min="1" max="10000"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm transition-colors">
                    <p class="mt-1.5 text-xs text-slate-500">How many people do you expect will attend this training?</p>
                </div>

                {{-- Training Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Training Topic / Title <span class="text-red-500">*</span>
                    </label>
                    <input id="title" name="title" type="text"
                           placeholder="e.g. Docker & Kubernetes for Laravel Developers"
                           value="{{ old('title', $trainingRequest->title) }}" required maxlength="255"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm transition-colors">
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Description & Objectives <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="4" required maxlength="5000"
                              placeholder="Describe what you'd like to learn, your team's current skill level, and any specific outcomes you're hoping for..."
                              class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm transition-colors resize-none">{{ old('description', $trainingRequest->description) }}</textarea>
                </div>

                {{-- Preferred Date --}}
                <div>
                    <label for="preferred_date" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Preferred Date <span class="text-red-500">*</span>
                    </label>
                    <input id="preferred_date" name="preferred_date" type="date"
                           value="{{ old('preferred_date', $trainingRequest->preferred_date->format('Y-m-d')) }}"
                           min="{{ now()->addDay()->toDateString() }}" required
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm transition-colors">
                </div>

                {{-- Optional: Linked Event --}}
                <div>
                    <label for="event_id" class="block text-sm font-medium text-slate-700 mb-1.5">
                        Link to Event <span class="text-slate-400 font-normal">(optional)</span>
                    </label>
                    <select id="event_id" name="event_id"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm transition-colors">
                        <option value="">— Not linked to a specific event —</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ old('event_id', $trainingRequest->event_id) == $event->id ? 'selected' : '' }}>
                                {{ $event->name }} ({{ $event->start_date->format('M Y') }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1.5 text-xs text-slate-500">If this training is part of a scheduled event, select it above.</p>
                </div>

                {{-- Submit --}}
                <div class="pt-2">
                    <button id="edit-submit-btn" type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-gold-500 px-4 py-3.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 11l5 5 5-5M12 4v12" />
                        </svg>
                        Update Training Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection