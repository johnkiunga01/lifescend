@extends('layouts.dashboard')

@section('pageTitle', 'Edit Event')

@section('content')
@php
$existingPricing = $event->ticketTypes->map(fn($tt) => [
'name' => $tt->name,
'description' => $tt->description ?? '',
'price' => (string) $tt->price,
'discount_price' => (string) ($tt->discount_price ?? ''),
'quantity' => (string) $tt->quantity,
'highlights' => is_array($tt->highlights) ? implode(', ', $tt->highlights) : ($tt->highlights ?? ''),
])->toJson();
$existingTopics = $event->topics->map(fn($t) => ['name' => $t->name, 'description' => $t->description ?? ''])->toJson();
$existingKeynotes = $event->speakers->filter(fn($s) => $s->pivot->featured)->map(fn($s) => [
    'speaker_id' => $s->id,
    'speak_time' => $s->pivot->speak_time ?? '',
])->toJson();
$existingSpeakers = $event->speakers->filter(fn($s) => !$s->pivot->featured)->values();
@endphp
<div x-data="{
    currentStep: 1,
    totalSteps: 4,
    pricing: {{ json_encode(old('pricing', $existingPricing ? json_decode($existingPricing, true) : [])) }},
    addPricing() { this.pricing.push({ name: '', description: '', price: '', discount_price: '', quantity: '100', highlights: '' }); },
    removePricing(i) { this.pricing.splice(i, 1); },
    topics: {{ old('keynotes') !== null ? json_encode(old('topics', [])) : $existingTopics }},
    addTopic() { this.topics.push({ name: '', description: '' }); },
    removeTopic(i) { this.topics.splice(i, 1); },
    keynotes: {{ old('keynotes') !== null ? json_encode(old('keynotes')) : $existingKeynotes }},
    removedSpeakerIds: {{ json_encode(old('remove_speaker_ids', [])) }},
    addKeynote() { this.keynotes.push({ speaker_id: '', speak_time: '' }); },
    removeKeynote(i) { this.keynotes.splice(i, 1); },
    toggleSpeakerRemoval(id) {
        if (this.removedSpeakerIds.includes(id)) { this.removedSpeakerIds = this.removedSpeakerIds.filter(item => item !== id); }
        else { this.removedSpeakerIds.push(id); }
    },
    showVenueForm: false,
    venueForm: { name: '', address: '', city: '', country: '', capacity: '' },
    venueErrors: {},
    venueSubmitting: false,
    async submitVenue() {
        this.venueSubmitting = true;
        this.venueErrors = {};
        try {
            const res = await fetch('{{ route('manage.venues.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(this.venueForm),
            });
            const data = await res.json();
            if (res.ok && data.success) {
                const opt = new Option(data.venue.name + ', ' + data.venue.city, data.venue.id, false, true);
                this.$refs.venueSelect.appendChild(opt);
                this.showVenueForm = false;
                this.venueForm = { name: '', address: '', city: '', country: '', capacity: '' };
            } else {
                this.venueErrors = data.errors || {};
            }
        } catch (e) {
            this.venueErrors = { name: ['Network error. Please try again.'] };
        }
        this.venueSubmitting = false;
    },
    speakerOptions: {{ json_encode(($speakers ?? collect())->map(fn($s) => ['id' => $s->id, 'name' => $s->name, 'company' => $s->company])->values()) }},
    speakerSearch: '',
    get filteredSpeakers() {
        if (!this.speakerSearch) return this.speakerOptions;
        const q = this.speakerSearch.toLowerCase();
        return this.speakerOptions.filter(s => s.name.toLowerCase().includes(q) || (s.company && s.company.toLowerCase().includes(q)));
    },
    showSpeakerForm: false,
    speakerForm: { name: '', email: '', company: '', job_title: '' },
    speakerErrors: {},
    speakerSubmitting: false,
    async submitSpeaker() {
        this.speakerSubmitting = true;
        this.speakerErrors = {};
        try {
            const res = await fetch('{{ route('manage.speakers.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(this.speakerForm),
            });
            const data = await res.json();
            if (res.ok && data.success) {
                this.speakerOptions.push({ id: data.speaker.id, name: data.speaker.name, company: data.speaker.company || '' });
                this.showSpeakerForm = false;
                this.speakerForm = { name: '', email: '', company: '', job_title: '' };
            } else {
                this.speakerErrors = data.errors || {};
            }
        } catch (e) {
            this.speakerErrors = { name: ['Network error. Please try again.'] };
        }
        this.speakerSubmitting = false;
    },
    partnerOptions: {{ json_encode(($partners ?? collect())->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values()) }},
    showPartnerForm: false,
    partnerForm: { name: '', website: '' },
    partnerErrors: {},
    partnerSubmitting: false,
    async submitPartner() {
        this.partnerSubmitting = true;
        this.partnerErrors = {};
        try {
            const res = await fetch('{{ route('manage.partners.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify(this.partnerForm),
            });
            const data = await res.json();
            if (res.ok && data.success) {
                this.partnerOptions.push({ id: data.partner.id, name: data.partner.name });
                this.showPartnerForm = false;
                this.partnerForm = { name: '', website: '' };
            } else {
                this.partnerErrors = data.errors || {};
            }
        } catch (e) {
            this.partnerErrors = { name: ['Network error. Please try again.'] };
        }
        this.partnerSubmitting = false;
    },
    validateStep(step) {
        const container = document.querySelector(`[data-step='${step}']`);
        if (!container) return true;
        const required = container.querySelectorAll('[required]');
        let valid = true;
        required.forEach(el => {
            el.classList.remove('border-red-400', 'ring-red-200');
            const val = el.tagName === 'SELECT' ? el.value : el.value.trim();
            if (!val) {
                el.classList.add('border-red-400', 'ring-red-200');
                el.reportValidity();
                valid = false;
            }
        });
        return valid;
    },
    nextStep() {
        if (this.validateStep(this.currentStep) && this.currentStep < this.totalSteps) this.currentStep++;
    },
    nextStep() {
        if (this.validateStep(this.currentStep) && this.currentStep < this.totalSteps) this.currentStep++;
    },
    prevStep() {
        if (this.currentStep > 1) this.currentStep--;
    },
    get isFirstStep() { return this.currentStep === 1; },
    get isLastStep() { return this.currentStep === this.totalSteps; }
}">

    {{-- Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gold-500 to-gold-600 p-8 mb-8 shadow-lg">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -top-10 -right-10 h-40 w-40 rounded-full bg-white blur-3xl"></div>
            <div class="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-white blur-3xl"></div>
        </div>
        <div class="relative">
            <a href="{{ route('manage.events.index') }}" class="text-sm text-white/70 hover:text-white font-semibold">&larr; Back to Events</a>
            <h1 class="text-3xl font-extrabold text-white mt-2">Edit Event</h1>
            <p class="text-gold-100 mt-1">Updating {{ $event->name }}</p>
        </div>
    </div>

    {{-- Step Progress --}}
    <div class="mb-8">
        <div class="flex items-center justify-between max-w-3xl mx-auto">
            <template x-for="(step, i) in [
                { num: 1, label: 'Event Info', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
                { num: 2, label: 'Media & Program', icon: 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z' },
                { num: 3, label: 'Ticketing', icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-20C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z' },
                { num: 4, label: 'Publish', icon: 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z' },
            ]" :key="step.num">
                <div class="flex flex-col items-center">
                    <div class="flex items-center">
                        <div :class="currentStep > step.num ? 'bg-emerald-500' : currentStep === step.num ? 'bg-gold-500 ring-4 ring-gold-200' : 'bg-slate-300'"
                             class="h-10 w-10 rounded-xl flex items-center justify-center text-white transition-all duration-300">
                            <template x-if="currentStep > step.num">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            </template>
                            <template x-if="currentStep <= step.num">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="step.icon" /></svg>
                            </template>
                        </div>
                        <template x-if="i < 3">
                            <div :class="currentStep > step.num ? 'bg-emerald-300' : 'bg-slate-200'" class="hidden sm:block w-16 lg:w-24 h-1 mx-3 rounded transition-all duration-300"></div>
                        </template>
                    </div>
                    <span :class="currentStep === step.num ? 'text-gold-600 font-bold' : 'text-slate-500'" class="text-xs mt-2 font-semibold hidden sm:block transition-colors duration-300" x-text="step.label"></span>
                </div>
            </template>
        </div>
    </div>

    <form action="{{ route('manage.events.update', $event->slug) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <input type="hidden" name="_form" value="edit">
        <input type="hidden" name="keynotes_present" value="1">

        @if($errors->any())
        <div class="rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700 shadow-sm mb-6">
            <ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Step 1: Event Information --}}
        <div data-step="1" x-show="currentStep === 1" x-cloak x-transition:enter.duration.300ms.opacity>
            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="h-9 w-9 rounded-xl bg-gold-500 flex items-center justify-center text-white"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                    <div><h2 class="text-lg font-bold text-slate-900">Event Information</h2><p class="text-xs text-slate-500">Basic details about your event</p></div>
                </div>
                <div class="space-y-5">
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Event Name <span class="text-red-500">*</span></label><input name="name" type="text" required                         value="{{ old('name', $event->name) }}" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                        <div class="rte" @open-speaker-picker.window="shortcodeInsertIndex = $event.detail.index; selectedSpeakerIds = []; speakerPicker = true" x-data="{ initialDescription: @js(old('description', $event->description ?? '')), speakerPicker: false, shortcodeMenu: false, selectedSpeakerIds: [], shortcodeInsertIndex: null, shortcodeSpeakers: @js(($speakers ?? collect())->map(fn ($speaker) => ['id' => $speaker->id, 'name' => $speaker->name, 'company' => $speaker->company, 'job_title' => $speaker->job_title, 'bio' => $speaker->bio, 'photo_url' => $speaker->photo_url])->values()), get selectedShortcodeSpeakers() { return this.shortcodeSpeakers.filter(speaker => this.selectedSpeakerIds.map(String).includes(String(speaker.id))); }, openSpeakerPicker(quill) { const q = quill || window.__eventDescriptionQuill; this.shortcodeInsertIndex = (q && q.getSelection) ? (q.getSelection(true) || { index: q.getLength(), length: 0 }).index : null; this.selectedSpeakerIds = []; this.speakerPicker = true; }, addSpeakerShortcode() { const q = window.__eventDescriptionQuill; const ids = this.selectedSpeakerIds || []; if (! q || ! ids.length) return; const code = '[speakers ids=' + ids.join(',') + ']'; const index = this.shortcodeInsertIndex ?? q.getLength(); q.insertText(index, '\n' + code + '\n', 'user'); q.setSelection(index + code.length + 2, 0, 'silent'); this.speakerPicker = false; this.selectedSpeakerIds = []; this.shortcodeInsertIndex = null; } }" x-init="
                            const quill = new Quill($refs.editorEl, {
                                theme: 'snow',
                                placeholder: 'Describe your event... use headings to organize sections and bullet points for highlights.',
                                modules: {
                                    toolbar: {
                                        container: [
                                            [{ header: [2, 3, false] }],
                                            ['bold', 'italic', 'underline'],
                                            [{ list: 'ordered' }, { list: 'bullet' }],
                                            ['blockquote', 'link', 'image', 'shortcode'],
                                            ['clean'],
                                        ],
                                        handlers: {
                                            shortcode: function () {
                                                const range = this.quill.getSelection(true) || { index: this.quill.getLength(), length: 0 };
                                                window.dispatchEvent(new CustomEvent('open-speaker-picker', { detail: { index: range.index } }));
                                            },
                                        },
                                    },
                                },
                            });
                            window.__eventDescriptionQuill = quill;
                            const toolbar = quill.getModule('toolbar');
                            toolbar.addHandler('image', () => {
                                const range = quill.getSelection(true) || { index: quill.getLength(), length: 0 };
                                const input = document.createElement('input');
                                input.type = 'file';
                                input.accept = 'image/jpeg,image/png,image/webp';
                                input.addEventListener('change', async () => {
                                    const image = input.files?.[0];
                                    if (!image) return;

                                    const formData = new FormData();
                                    formData.append('image', image);

                                    try {
                                        const response = await fetch(@js(route('manage.events.description-images.store')), {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                'Accept': 'application/json',
                                            },
                                            body: formData,
                                        });
                                        const result = await response.json();
                                        if (!response.ok || !result.url) {
                                            throw new Error(result.errors?.image?.[0] || result.message || 'Image upload failed.');
                                        }
                                        quill.insertEmbed(range.index, 'image', result.url, 'user');
                                        quill.setSelection(range.index + 1, 0, 'silent');
                                    } catch (error) {
                                        alert(error.message || 'Image upload failed. Please try again.');
                                    }
                                });
                                input.click();
                            });
                            quill.root.innerHTML = initialDescription;
                            $refs.descriptionInput.value = initialDescription;
                            quill.on('text-change', () => { $refs.descriptionInput.value = quill.root.innerHTML; });
                        ">
                            <div x-ref="editorEl"></div>
                            <input type="hidden" name="description" x-ref="descriptionInput">
                            @include('manage.events._speaker-shortcode-picker')
                        </div>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Event Type <span class="text-red-500">*</span></label><select name="event_type" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">@foreach($eventTypes as $et)                        <option value="{{ $et->slug }}" {{ old('event_type', $event->event_type) === $et->slug ? 'selected' : '' }}>{{ $et->name }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Venue</label><div class="flex items-end gap-2"><div class="flex-1"><select name="venue_id" x-ref="venueSelect" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"><option value="">— No venue —</option>@foreach($venues as $v)<option value="{{ $v->id }}" {{ old('venue_id', $event->venue_id) == $v->id ? 'selected' : '' }}>{{ $v->name }}, {{ $v->city }}</option>@endforeach</select></div><button type="button" @@click="showVenueForm = !showVenueForm" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-3 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-gold-600 transition-colors shrink-0 cursor-pointer"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>Add Venue</button></div></div>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Start Date <span class="text-red-500">*</span></label><input name="start_date" type="datetime-local" required                         value="{{ $event->start_date->format('Y-m-d\TH:i') }}" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">End Date <span class="text-red-500">*</span></label><input name="end_date" type="datetime-local" required value="{{ $event->end_date->format('Y-m-d\TH:i') }}" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Timezone</label><input name="timezone" type="text" value="{{ old('timezone', $event->timezone ?? $organization?->timezone ?? config('app.timezone')) }}" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                </div>
            </div>
        </div>

        {{-- Step 2: Media & Program --}}
        <div data-step="2" x-show="currentStep === 2" x-cloak x-transition:enter.duration.300ms.opacity>
            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-emerald-500 flex items-center justify-center text-white"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                        <div><h2 class="text-lg font-bold text-slate-900">Cover Image</h2><p class="text-xs text-slate-500">Upload an eye-catching banner for your event</p></div>
                    </div>
                    <div class="mt-5">
                        <div x-data="imageCropper({ name: 'cover_image', ratio: {{ ($organization?->settings['cover_image_width'] ?? 1600) / ($organization?->settings['cover_image_height'] ?? 900) }}, width: {{ $organization?->settings['cover_image_width'] ?? 1600 }}, height: {{ $organization?->settings['cover_image_height'] ?? 900 }}, label: 'Cover' })" x-init="{{ $event->cover_image ? "previewUrl = '{$event->cover_url}'" : '' }}">
                            <input type="hidden" name="remove_cover_image" value="0" x-ref="removeCoverRef">
                            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" @@change="onFileChange" x-ref="fileInput" class="hidden">
                            <div class="relative rounded-xl border-2 border-dashed border-slate-300 bg-slate-50 h-32 flex items-center justify-center overflow-hidden cursor-pointer group" @@click="$refs.fileInput.click()">
                                <img x-ref="preview" x-show="previewUrl" :src="previewUrl" class="h-full w-full object-cover">
                                <div class="text-center" x-show="!previewUrl">
                                    <svg class="mx-auto h-8 w-8 text-slate-400 group-hover:text-gold-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <p class="mt-2 text-xs text-slate-400 group-hover:text-gold-600">Click to upload cover</p>
                                    <p class="text-[10px] text-slate-400">{{ $organization?->settings['cover_image_width'] ?? 1600 }} × {{ $organization?->settings['cover_image_height'] ?? 900 }} px</p>
                                </div>
                                <button type="button" @@click.stop="clearCover(); $refs.removeCoverRef.value = '1'" x-show="previewUrl" class="absolute top-2 right-2 z-10 rounded-full bg-red-500/90 p-1.5 text-white hover:bg-red-600 shadow-md cursor-pointer">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        @include('manage.events._cropper-modal')
                    </div>
                </div>
            </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200/60">
                        <div class="h-9 w-9 rounded-xl bg-purple-500 flex items-center justify-center text-white"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                        <div><h2 class="text-lg font-bold text-slate-900">Program Content</h2><p class="text-xs text-slate-500">Topics, speakers, and keynotes</p></div>
                    </div>
                    <div class="space-y-8">
                        @if($existingSpeakers->isNotEmpty())
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 mb-3">Attached Speakers</h3>
                            <div class="grid gap-3 sm:grid-cols-2">
                                @foreach($existingSpeakers as $speaker)
                                <div class="rounded-xl border border-slate-200 bg-white p-4" :class="removedSpeakerIds.includes({{ $speaker->id }}) ? 'opacity-50 line-through' : ''">
                                    <div class="flex items-start justify-between gap-3">
                                        <div><p class="font-semibold text-slate-900">{{ $speaker->name }}</p><p class="text-sm text-slate-600">{{ $speaker->job_title ?? 'Speaker' }}@if($speaker->company), {{ $speaker->company }}@endif</p></div>
                                        <button type="button" @@click="toggleSpeakerRemoval({{ $speaker->id }})" class="text-xs text-red-500 hover:text-red-700 font-semibold"><span x-text="removedSpeakerIds.includes({{ $speaker->id }}) ? 'Undo' : 'Remove'"></span></button>
                                    </div>
                                    <template x-if="removedSpeakerIds.includes({{ $speaker->id }})"><input type="hidden" name="remove_speaker_ids[]" value="{{ $speaker->id }}"></template>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-slate-700">Training Topics</h3>
                                <button type="button" @@click="addTopic()" class="inline-flex items-center gap-1 text-xs font-bold text-gold-600 hover:text-gold-700 cursor-pointer"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add Topic</button>
                            </div>
                            <template x-for="(t, i) in topics" :key="i">
                                <div class="border border-slate-200 rounded-xl p-4 mb-3 bg-white">
                                    <div class="flex items-center justify-between mb-3"><span class="text-xs font-semibold text-slate-500" x-text="'Topic #' + (i + 1)"></span><button type="button" @@click="removeTopic(i)" class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer">Remove</button></div>
                                    <div class="mb-3"><input type="text" x-model="t.name" :name="'topics[' + i + '][name]'" placeholder="Topic title" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none"></div>
                                    <div><textarea x-model="t.description" :name="'topics[' + i + '][description]'" rows="2" placeholder="Brief description" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none resize-none"></textarea></div>
                                </div>
                            </template>
                            <p x-show="topics.length === 0" class="text-sm text-slate-500 italic">No topics added yet.</p>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-bold text-slate-700">Keynote Speakers</h3>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('manage.speakers.index') }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs font-bold text-slate-600 hover:text-slate-900 cursor-pointer"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4zM4 14h6v6H4v-6zm10 0h6v6h-6v-6z" /></svg> Manage Speakers</a>
                                    <button type="button" @@click="addKeynote()" class="inline-flex items-center gap-1 text-xs font-bold text-gold-600 hover:text-gold-700 cursor-pointer"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add Keynote</button>
                                    <button type="button" @@click="showSpeakerForm = true" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-700 cursor-pointer ml-2"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg> Add Speaker</button>
                                </div>
                            </div>
                            <template x-for="(k, i) in keynotes" :key="i">
                                <div class="border border-slate-200 rounded-xl p-4 mb-3 bg-white">
                                    <div class="flex items-center justify-between mb-3"><span class="text-xs font-semibold text-slate-500" x-text="'Keynote #' + (i + 1)"></span><button type="button" @@click="removeKeynote(i)" class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer">Remove</button></div>
                                    <div class="grid gap-3 sm:grid-cols-4">
                                        <div><input type="time" x-model="k.speak_time" :name="'keynotes[' + i + '][speak_time]'" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none"></div>
                                        <div class="sm:col-span-3"><input type="text" x-model="speakerSearch" placeholder="Search speakers..." class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none mb-2"><select x-model="k.speaker_id" :name="'keynotes[' + i + '][speaker_id]'" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none"><option value="">— Select speaker —</option><template x-for="s in filteredSpeakers" :key="s.id"><option :value="s.id" x-text="s.name + (s.company ? ' (' + s.company + ')' : '')"></option></template></select></div>
                                    </div>
                                </div>
                            </template>
                            <p x-show="keynotes.length === 0" class="text-sm text-slate-500 italic">No keynotes added yet.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Ticketing --}}
        <div data-step="3" x-show="currentStep === 3" x-cloak x-transition:enter.duration.300ms.opacity>
            <div class="rounded-2xl border border-gold-200 bg-gold-50 p-8 shadow-sm">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gold-200/60">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-xl bg-gold-500 flex items-center justify-center text-white"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-20C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z" /></svg></div>
                        <div><h2 class="text-lg font-bold text-slate-900">Ticketing & Pricing</h2><p class="text-xs text-slate-500">Set capacity and create ticket packages</p></div>
                    </div>
                    <button type="button" @@click="addPricing()" class="inline-flex items-center gap-1.5 rounded-lg bg-gold-500 px-4 py-2 text-xs font-bold text-white shadow-md shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer"><svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add Package</button>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 mb-6">
                    <div><label class="block text-sm font-medium text-slate-700 mb-1">Capacity</label><input name="capacity" type="number" min="1" value="{{ $event->capacity }}" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Payment Type</label>
                        <select name="payment_type" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                            <option value="stripe" selected>Stripe (Card)</option>
                        </select>
                    </div>
                </div>
                <template x-for="(p, i) in pricing" :key="i">
                    <div class="border border-gold-200 rounded-xl p-5 mb-4 bg-white shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center rounded-full bg-gold-100 px-3 py-1 text-xs font-bold text-gold-700" x-text="'Package #' + (i + 1)"></span>
                            <button type="button" @@click="removePricing(i)" class="text-xs text-red-500 hover:text-red-700 font-semibold cursor-pointer">Remove</button>
                        </div>
                        <div class="space-y-4">
                            <input type="text" x-model="p.name" :name="'pricing[' + i + '][name]'" placeholder="Package name" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-gold-500 focus:outline-none">
                            <textarea x-model="p.description" :name="'pricing[' + i + '][description]'" rows="2" placeholder="Package description" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 focus:border-gold-500 focus:outline-none resize-none"></textarea>
                            <div class="grid gap-3 sm:grid-cols-4">
                                <div><input type="number" step="0.01" min="0" x-model="p.price" :name="'pricing[' + i + '][price]'" placeholder="Price" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none"></div>
                                <div><input type="number" step="0.01" min="0" x-model="p.discount_price" :name="'pricing[' + i + '][discount_price]'" placeholder="Discounted" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none"></div>
                                <div><input type="number" min="1" x-model="p.quantity" :name="'pricing[' + i + '][quantity]'" placeholder="Qty" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none"></div>
                                <div><input type="text" x-model="p.highlights" :name="'pricing[' + i + '][highlights]'" placeholder="Highlights" class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 focus:border-gold-500 focus:outline-none"></div>
                            </div>
                        </div>
                    </div>
                </template>
                <p x-show="pricing.length === 0" class="text-sm text-slate-500 italic text-center py-4">No pricing packages added.</p>
            </div>
        </div>

        {{-- Step 4: Review & Publish --}}
        <div data-step="4" x-show="currentStep === 4" x-cloak x-transition:enter.duration.300ms.opacity>
            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-cyan-500 flex items-center justify-center text-white"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a3 3 0 01-3-3V7m8 4v2m-2-2v2m-4-2v2m-2-2v2" /></svg></div>
                        <div class="flex-1"><h2 class="text-lg font-bold text-slate-900">Partners & Sponsors</h2><p class="text-xs text-slate-500">Select partners and sponsors for your event</p></div>
                        <button type="button" @@click="showPartnerForm = true" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 hover:text-cyan-600 transition-colors cursor-pointer">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                            Add Partner
                        </button>
                    </div>
                    @php $eventSponsorIds = $event->sponsors->pluck('id')->toArray() ?? []; @endphp
                    <div class="grid gap-2 sm:grid-cols-3 max-h-48 overflow-y-auto border border-slate-200 rounded-xl p-4 bg-slate-50 mb-4">
                        @forelse($sponsors as $sp)
                        <label class="flex items-center gap-2 cursor-pointer hover:bg-white rounded-lg px-2 py-1.5 transition-colors">
                            <input type="checkbox" name="sponsor_ids[]" value="{{ $sp->id }}" {{ in_array($sp->id, old('sponsor_ids', $eventSponsorIds)) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
                            <span class="text-sm text-slate-700">{{ $sp->name }}</span>
                        </label>
                        @empty
                        <p class="text-sm text-slate-400 col-span-3">No sponsors available.</p>
                        @endforelse
                    </div>
                    <div class="grid gap-2 sm:grid-cols-3 max-h-32 overflow-y-auto border border-slate-200 rounded-xl p-4 bg-slate-50">
                        <template x-for="p in partnerOptions" :key="p.id">
                            <label class="flex items-center gap-2 cursor-pointer hover:bg-white rounded-lg px-2 py-1.5 transition-colors">
                                <input type="checkbox" name="partner_ids[]" :value="p.id" class="h-4 w-4 rounded border-slate-300 text-cyan-500 focus:ring-cyan-500">
                                <span class="text-sm text-slate-700" x-text="p.name"></span>
                            </label>
                        </template>
                        <p x-show="partnerOptions.length === 0" class="text-sm text-slate-400 col-span-3">No partners available. Click "Add Partner" to create one.</p>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-200/60">
                        <div class="h-9 w-9 rounded-xl bg-amber-500 flex items-center justify-center text-white"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg></div>
                        <div><h2 class="text-lg font-bold text-slate-900">Publish Settings</h2><p class="text-xs text-slate-500">Set event visibility and status</p></div>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Event Status <span class="text-red-500">*</span></label>
                            <select name="status" required class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                                <option value="draft" {{ $event->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ $event->status === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ $event->status === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Buttons --}}
        <div class="flex items-center justify-between pt-6 mt-2 border-t border-slate-200">
            <div>
                <button type="button" x-show="!isFirstStep" @@click="prevStep()" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                    Previous
                </button>
                <button type="button" x-show="isFirstStep" @@click="window.location.href='{{ route('manage.events.index') }}'" class="inline-flex items-center gap-2 rounded-xl border-2 border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer">
                    Cancel
                </button>
            </div>
            <div>
                <span class="text-xs text-slate-400 mr-4" x-text="'Step ' + currentStep + ' of ' + totalSteps"></span>
                <button type="button" x-show="!isLastStep" @@click="nextStep()" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                    Next
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
                <button type="submit" x-show="isLastStep" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-emerald-500/25 hover:bg-emerald-600 transition-all cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Update Event
                </button>
            </div>
        </div>
    </form>

    {{-- Add Venue Modal --}}
    <div x-show="showVenueForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);" @@click.self="showVenueForm = false; venueErrors = {}; venueForm = { name: '', address: '', city: '', country: '', capacity: '' }">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900">Add New Venue</h3>
                <button type="button" @@click="showVenueForm = false; venueErrors = {}; venueForm = { name: '', address: '', city: '', country: '', capacity: '' }" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="venueForm.name" placeholder="e.g. Manila Convention Center" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                    <p x-show="venueErrors.name" class="text-xs text-red-500 mt-1" x-text="venueErrors.name?.[0]"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">City <span class="text-red-500">*</span></label>
                    <input type="text" x-model="venueForm.city" placeholder="e.g. Manila" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                    <p x-show="venueErrors.city" class="text-xs text-red-500 mt-1" x-text="venueErrors.city?.[0]"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Address <span class="text-red-500">*</span></label>
                    <input type="text" x-model="venueForm.address" placeholder="e.g. 123 Roxas Boulevard" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                    <p x-show="venueErrors.address" class="text-xs text-red-500 mt-1" x-text="venueErrors.address?.[0]"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Country <span class="text-red-500">*</span></label>
                        <input type="text" x-model="venueForm.country" placeholder="e.g. Philippines" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                        <p x-show="venueErrors.country" class="text-xs text-red-500 mt-1" x-text="venueErrors.country?.[0]"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Capacity</label>
                        <input type="number" x-model="venueForm.capacity" placeholder="e.g. 500" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                        <p x-show="venueErrors.capacity" class="text-xs text-red-500 mt-1" x-text="venueErrors.capacity?.[0]"></p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @@click="showVenueForm = false; venueErrors = {}; venueForm = { name: '', address: '', city: '', country: '', capacity: '' }" class="rounded-xl border-2 border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">Cancel</button>
                <button type="button" @@click="submitVenue()" :disabled="venueSubmitting" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all disabled:opacity-50 cursor-pointer">
                    <span x-show="!venueSubmitting">Save Venue</span>
                    <span x-show="venueSubmitting">Saving...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Add Speaker Modal --}}
    <div x-show="showSpeakerForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);" @@click.self="showSpeakerForm = false; speakerErrors = {}; speakerForm = { name: '', email: '', company: '', job_title: '' }">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900">Add New Speaker</h3>
                <button type="button" @@click="showSpeakerForm = false; speakerErrors = {}; speakerForm = { name: '', email: '', company: '', job_title: '' }" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="speakerForm.name" placeholder="e.g. John Doe" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                    <p x-show="speakerErrors.name" class="text-xs text-red-500 mt-1" x-text="speakerErrors.name?.[0]"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" x-model="speakerForm.email" placeholder="e.g. john@example.com" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                    <p x-show="speakerErrors.email" class="text-xs text-red-500 mt-1" x-text="speakerErrors.email?.[0]"></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Company</label>
                        <input type="text" x-model="speakerForm.company" placeholder="e.g. Acme Corp" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                        <p x-show="speakerErrors.company" class="text-xs text-red-500 mt-1" x-text="speakerErrors.company?.[0]"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Job Title</label>
                        <input type="text" x-model="speakerForm.job_title" placeholder="e.g. CEO" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                        <p x-show="speakerErrors.job_title" class="text-xs text-red-500 mt-1" x-text="speakerErrors.job_title?.[0]"></p>
                    </div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @@click="showSpeakerForm = false; speakerErrors = {}; speakerForm = { name: '', email: '', company: '', job_title: '' }" class="rounded-xl border-2 border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">Cancel</button>
                <button type="button" @@click="submitSpeaker()" :disabled="speakerSubmitting" class="inline-flex items-center gap-2 rounded-xl bg-blue-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-blue-500/25 hover:bg-blue-600 transition-all disabled:opacity-50 cursor-pointer">
                    <span x-show="!speakerSubmitting">Save Speaker</span>
                    <span x-show="speakerSubmitting">Saving...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Add Partner Modal --}}
    <div x-show="showPartnerForm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);" @@click.self="showPartnerForm = false; partnerErrors = {}; partnerForm = { name: '', website: '' }">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-slate-900">Add New Partner</h3>
                <button type="button" @@click="showPartnerForm = false; partnerErrors = {}; partnerForm = { name: '', website: '' }" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" x-model="partnerForm.name" placeholder="e.g. Tech Corp" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                    <p x-show="partnerErrors.name" class="text-xs text-red-500 mt-1" x-text="partnerErrors.name?.[0]"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Website</label>
                    <input type="url" x-model="partnerForm.website" placeholder="e.g. https://example.com" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm placeholder-slate-400 focus:border-gold-500 focus:outline-none">
                    <p x-show="partnerErrors.website" class="text-xs text-red-500 mt-1" x-text="partnerErrors.website?.[0]"></p>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 mt-6">
                <button type="button" @@click="showPartnerForm = false; partnerErrors = {}; partnerForm = { name: '', website: '' }" class="rounded-xl border-2 border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all cursor-pointer">Cancel</button>
                <button type="button" @@click="submitPartner()" :disabled="partnerSubmitting" class="inline-flex items-center gap-2 rounded-xl bg-cyan-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-cyan-500/25 hover:bg-cyan-600 transition-all disabled:opacity-50 cursor-pointer">
                    <span x-show="!partnerSubmitting">Save Partner</span>
                    <span x-show="partnerSubmitting">Saving...</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
