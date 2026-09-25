@php
    if (!isset($events)) {
        $events = \App\Models\Event::where('status', 'published')->orderBy('name')->get();
    }
@endphp
<div x-show="trainingModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="trainingModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="trainingModal = false" class="fixed inset-0 bg-black/60"></div>
        <div x-show="trainingModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
             class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-2xl bg-white p-8 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Request Training</h2>
                    <p class="text-sm text-slate-500 mt-1">Tell us what training you need and we'll get back to you.</p>
                </div>
                <button @@click="trainingModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form action="{{ route('training.store') }}" method="POST" class="space-y-5" onsubmit="var b=document.getElementById('training-submit-btn');b.disabled=true;b.innerHTML='Submitting...';">
                @csrf

                <div style="position:absolute;left:-9999px" aria-hidden="true">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Your Name <span class="text-red-500">*</span></label>
                        <input name="contact_name" type="text" required maxlength="255" value="{{ Auth::user()->name ?? '' }}" placeholder="e.g. John Doe"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Your Email <span class="text-red-500">*</span></label>
                        <input name="contact_email" type="email" required maxlength="255" value="{{ Auth::user()->email ?? '' }}" placeholder="e.g. john@example.com"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                    <input name="contact_phone" type="text" maxlength="50" placeholder="e.g. +254 712 345 678"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>

                <div x-data="{ type: 'online' }">
                    <label class="block text-sm font-semibold text-slate-700 mb-3">Delivery Format</label>
                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <label class="relative cursor-pointer" @click="type = 'online'">
                            <input type="radio" name="training_type" value="online" class="sr-only" checked>
                            <div :class="type === 'online' ? 'border-gold-500 ring-2 ring-gold-500/30 bg-gold-50' : 'border-slate-200 bg-white hover:border-gold-300'"
                                 class="flex flex-col items-center gap-2 rounded-xl border p-4 transition-all duration-200 text-center">
                                <div :class="type === 'online' ? 'bg-gold-500' : 'bg-slate-200'" class="h-9 w-9 rounded-lg flex items-center justify-center transition-colors">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                </div>
                                <p :class="type === 'online' ? 'text-gold-700' : 'text-slate-700'" class="font-bold text-xs">Online</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer" @click="type = 'inhouse'">
                            <input type="radio" name="training_type" value="inhouse" class="sr-only">
                            <div :class="type === 'inhouse' ? 'border-emerald-500 ring-2 ring-emerald-500/30 bg-emerald-50' : 'border-slate-200 bg-white hover:border-emerald-300'"
                                 class="flex flex-col items-center gap-2 rounded-xl border p-4 transition-all duration-200 text-center">
                                <div :class="type === 'inhouse' ? 'bg-emerald-600' : 'bg-slate-200'" class="h-9 w-9 rounded-lg flex items-center justify-center transition-colors">
                                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                </div>
                                <p :class="type === 'inhouse' ? 'text-emerald-700' : 'text-slate-700'" class="font-bold text-xs">In-House</p>
                            </div>
                        </label>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Training Topic / Title <span class="text-red-500">*</span></label>
                        <input name="title" type="text" required maxlength="255" placeholder="e.g. Advanced PCR Techniques Workshop"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" rows="3" required maxlength="5000" placeholder="Describe the training you're looking for..."
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none"></textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Link to Event <span class="text-slate-400 font-normal">(optional)</span></label>
                    <select name="event_id" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                        <option value="">— Not linked to a specific event —</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }} ({{ $event->start_date->format('M Y') }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Preferred Date <span class="text-red-500">*</span></label>
                        <input name="preferred_date" type="date" required min="{{ now()->addDay()->toDateString() }}"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Estimated Attendees</label>
                        <input name="estimated_attendees" type="number" max="10000" min="1" placeholder="e.g. 25"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Your Message <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="2" required maxlength="2000" placeholder="Any specific requirements, objectives, or details..."
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" @@click="trainingModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                    <button id="training-submit-btn" type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>