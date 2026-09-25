<div x-show="profileModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="profileModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="profileModal = false" class="fixed inset-0 bg-black/60"></div>
        <div x-show="profileModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="profileModal = false"
             x-data="{
                name: '{{ addslashes(Auth::user()->name) }}',
                username: '{{ addslashes(Auth::user()->username ?? '') }}',
                email: '{{ addslashes(Auth::user()->email) }}',
                verified: {{ Auth::user()->hasVerifiedEmail() ? 'true' : 'false' }},
                saving: false,
                saveProfile() {
                    this.saving = true;
                    fetch('{{ route('profile.update') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ name: this.name, username: this.username, email: this.email })
                    })
                    .then(r => r.json())
                    .then(d => {
                        this.saving = false;
                        if (d.success) {
                            this.verified = d.verified;
                            alert(d.message);
                            if (!d.verified) location.reload();
                        } else {
                            alert(Object.values(d.errors || { error: 'Save failed' }).flat().join('\n'));
                        }
                    })
                    .catch(() => { this.saving = false; alert('Network error'); });
                }
             }"
             class="relative w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <div><h2 class="text-xl font-extrabold text-slate-900">Profile Settings</h2><p class="text-sm text-slate-500 mt-1">Update your account details.</p></div>
                <button @@click="profileModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>

            <div class="flex items-center gap-4 pb-5 mb-5 border-b border-slate-100">
                <div class="h-14 w-14 rounded-full bg-gold-500 flex items-center justify-center font-bold text-white text-xl">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-bold text-slate-900" x-text="name">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500" x-text="email">{{ Auth::user()->email }}</p>
                    <template x-if="!verified">
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-xs text-amber-600 font-semibold">Email not verified</p>
                            <button @@click="fetch('{{ route('verification.send') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } }).then(r => { if(r.ok) alert('Verification email sent!'); })" class="text-xs font-bold text-gold-600 hover:text-gold-700 underline cursor-pointer">
                                Send verification
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Full Name</label>
                    <input type="text" x-model="name" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                    <input type="text" x-model="username" placeholder="e.g. johndoe" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" x-model="email" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:outline-none sm:text-sm">
                    <template x-if="!verified && email !== '{{ Auth::user()->email }}'">
                        <p class="text-xs text-amber-600 mt-1">Changing email will require verification.</p>
                    </template>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="button" @@click="profileModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                <button @@click="saveProfile()" :disabled="saving" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer disabled:opacity-50">
                    <span x-text="saving ? 'Saving...' : 'Save Changes'"></span>
                </button>
            </div>
        </div>
    </div>
</div>
