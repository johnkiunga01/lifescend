{{-- Modal used by the Description editor's Speaker toolbar button. --}}
<div x-show="speakerPicker" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(15, 23, 42, 0.7);" @@click.self="speakerPicker = false">
    <div x-show="speakerPicker" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-3 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-3 scale-95" class="w-full max-w-3xl overflow-hidden rounded-3xl bg-white shadow-2xl" @@keydown.escape.window="speakerPicker = false">
        <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-slate-800 px-6 py-5 text-white sm:px-8">
            <div class="flex items-start justify-between gap-5">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-gold-300">Description shortcode</p>
                    <h3 class="mt-1 text-xl font-extrabold">Choose speakers to display</h3>
                    <p class="mt-1 text-sm text-slate-300">Their profile cards will appear at this position on the public event page.</p>
                </div>
                <button type="button" @@click="speakerPicker = false" class="rounded-xl p-2 text-slate-300 hover:bg-white/10 hover:text-white cursor-pointer" aria-label="Close speaker picker">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        <div class="grid max-h-[65vh] gap-6 overflow-y-auto p-6 sm:grid-cols-[0.9fr_1.1fr] sm:p-8">
            <div>
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm font-bold text-slate-800">Available speakers</p>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-500" x-text="shortcodeSpeakers.length"></span>
                </div>
                <div class="space-y-2">
                    <template x-for="speaker in shortcodeSpeakers" :key="speaker.id">
                        <label class="flex cursor-pointer items-center gap-3 rounded-2xl border border-slate-200 p-3 transition hover:border-gold-300 hover:bg-gold-50/50" :class="selectedSpeakerIds.map(String).includes(String(speaker.id)) ? 'border-gold-400 bg-gold-50 ring-1 ring-gold-300' : ''">
                            <input type="checkbox" :value="speaker.id" x-model="selectedSpeakerIds" class="h-4 w-4 rounded border-slate-300 text-gold-600 focus:ring-gold-500">
                            <img :src="speaker.photo_url || '/images/profile_placeholder.png'" :alt="speaker.name" class="h-10 w-10 shrink-0 rounded-xl bg-slate-100 object-cover" onerror="this.src='/images/profile_placeholder.png'">
                            <span class="min-w-0"><span class="block truncate text-sm font-bold text-slate-800" x-text="speaker.name"></span><span class="block truncate text-xs text-slate-500" x-text="[speaker.job_title, speaker.company].filter(Boolean).join(' · ') || 'Speaker'"></span></span>
                        </label>
                    </template>
                    <div x-show="shortcodeSpeakers.length === 0" class="rounded-2xl border border-dashed border-slate-300 p-5 text-center">
                        <p class="text-sm font-bold text-slate-700">No speakers available</p>
                        <a href="{{ route('manage.speakers.index') }}" target="_blank" rel="noopener" class="mt-2 inline-block text-sm font-bold text-gold-700 hover:text-gold-800">Manage Speakers</a>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                <div class="mb-3 flex items-center justify-between">
                    <p class="text-sm font-bold text-slate-800">Live preview</p>
                    <span class="text-xs font-medium text-slate-500" x-text="selectedShortcodeSpeakers.length ? selectedShortcodeSpeakers.length + ' selected' : 'Select one or more'"></span>
                </div>
                <div class="space-y-3">
                    <template x-for="speaker in selectedShortcodeSpeakers" :key="speaker.id">
                        <article class="flex gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <img :src="speaker.photo_url || '/images/profile_placeholder.png'" :alt="speaker.name" class="h-14 w-14 shrink-0 rounded-2xl bg-slate-100 object-cover" onerror="this.src='/images/profile_placeholder.png'">
                            <div class="min-w-0"><h4 class="font-bold text-slate-900" x-text="speaker.name"></h4><p class="mt-0.5 text-xs font-medium text-gold-700" x-text="[speaker.job_title, speaker.company].filter(Boolean).join(' · ')"></p><p class="mt-2 line-clamp-3 text-xs leading-5 text-slate-600" x-text="speaker.bio || 'Speaker profile' "></p></div>
                        </article>
                    </template>
                    <div x-show="selectedShortcodeSpeakers.length === 0" class="flex min-h-44 flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-center">
                        <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        <p class="mt-3 text-sm font-bold text-slate-600">Your selected speakers will preview here.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-8">
            <code class="text-xs text-slate-500" x-show="selectedSpeakerIds.length" x-text="'[speakers ids=' + selectedSpeakerIds.join(',') + ']' "></code>
            <span x-show="!selectedSpeakerIds.length" class="text-xs text-slate-400">Select at least one speaker to continue.</span>
            <div class="flex items-center justify-end gap-3"><button type="button" @@click="speakerPicker = false" class="rounded-xl px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 cursor-pointer">Cancel</button><button type="button" @@click="addSpeakerShortcode()" :disabled="!selectedSpeakerIds.length" class="rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/20 hover:bg-gold-600 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer">Insert speakers</button></div>
        </div>
    </div>
</div>
