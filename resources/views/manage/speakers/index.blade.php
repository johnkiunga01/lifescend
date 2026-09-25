@extends('layouts.dashboard')

@section('pageTitle', 'Manage Speakers')

@section('content')
<div x-data="{ addModal: {{ old('_form') === 'add' ? 'true' : 'false' }}, editModal: {{ old('_form') === 'edit' ? 'true' : 'false' }}, editSpeaker: { id: {{ old('_form') === 'edit' ? (old('edit_id') ?: 'null') : 'null' }}, name: '{{ old('_form') === 'edit' ? addslashes(old('name', '')) : '' }}', email: '{{ old('_form') === 'edit' ? addslashes(old('email', '')) : '' }}', phone: '{{ old('_form') === 'edit' ? addslashes(old('phone', '')) : '' }}', bio: '{{ old('_form') === 'edit' ? addslashes(old('bio', '')) : '' }}', company: '{{ old('_form') === 'edit' ? addslashes(old('company', '')) : '' }}', job_title: '{{ old('_form') === 'edit' ? addslashes(old('job_title', '')) : '' }}' }, openEdit(s) { this.editSpeaker = { ...s }; this.editModal = true; } }">
    <div class="mb-6 flex items-center justify-between">
        <div><h1 class="text-2xl font-extrabold text-slate-900">Manage Speakers</h1><p class="text-sm text-slate-500 mt-1">{{ $speakers->count() }} speakers.</p></div>
        <button @@click="addModal = true" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg> Add Speaker</button>
    </div>

    @if(session('success'))<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
    @if($errors->any() && !session('success'))<div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50"><tr>
                    <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500 w-12">Photo</th>
                    <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Speaker</th>
                    <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                    <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Company</th>
                    <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Events</th>
                    <th class="px-3 py-3.5 text-right pr-6 text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($speakers as $s)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 pl-6 pr-3">
                            <img src="{{ $s->photo_url }}" alt="{{ $s->name }}" class="h-10 w-10 rounded-full object-cover bg-slate-100">
                        </td>
                        <td class="pr-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $s->name }}</p>
                            <p class="text-xs text-slate-500">{{ $s->job_title ?? '—' }}</p>
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $s->email ?? '—' }}</td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $s->company ?? '—' }}</td>
                        <td class="px-3 py-4 text-sm text-slate-500">{{ $s->events_count }}</td>
                        <td class="px-3 py-4 text-right pr-6">
                            <div class="flex items-center justify-end gap-2">
                                <button @@click="openEdit({{ json_encode(['id' => $s->id, 'name' => $s->name, 'email' => $s->email ?? '', 'phone' => $s->phone ?? '', 'bio' => $s->bio ?? '', 'company' => $s->company ?? '', 'job_title' => $s->job_title ?? '']) }})" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors cursor-pointer">Edit</button>
                                <form action="{{ route('manage.speakers.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this speaker? They will be removed from any assigned events and sessions.');">@csrf @method('DELETE')<button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-12 text-center text-sm text-slate-500">No speakers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Modal --}}
    <div x-show="addModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="addModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="addModal = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="addModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="addModal = false" class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div><h2 class="text-xl font-extrabold text-slate-900">Add Speaker</h2></div>
                    <button @@click="addModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form action="{{ route('manage.speakers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_form" value="add">
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label><input name="name" type="text" required placeholder="e.g. Taylor Otwell" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label class="block text-sm font-medium text-slate-700 mb-1">Email</label><input name="email" type="email" placeholder="e.g. taylor@example.com" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                            <div><label class="block text-sm font-medium text-slate-700 mb-1">Phone</label><input name="phone" type="text" placeholder="e.g. +1 555 123 4567" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label class="block text-sm font-medium text-slate-700 mb-1">Company</label><input name="company" type="text" placeholder="e.g. Laravel LLC" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                            <div><label class="block text-sm font-medium text-slate-700 mb-1">Job Title</label><input name="job_title" type="text" placeholder="e.g. CEO / Creator" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        </div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Photo</label><input name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gold-50 file:text-gold-700 hover:file:bg-gold-100 cursor-pointer"></div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Bio</label><textarea name="bio" rows="3" placeholder="Brief background about the speaker..." class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none"></textarea></div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @@click="addModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">Create Speaker</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="editModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="editModal = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="editModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="editModal = false" class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div><h2 class="text-xl font-extrabold text-slate-900">Edit Speaker</h2></div>
                    <button @@click="editModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form x-bind:action="`/manage/speakers/${editSpeaker.id}`" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <input type="hidden" name="_form" value="edit">
                    <input type="hidden" name="edit_id" x-model="editSpeaker.id">
                    <div class="space-y-4">
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-red-500">*</span></label><input name="name" type="text" required x-model="editSpeaker.name" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label class="block text-sm font-medium text-slate-700 mb-1">Email</label><input name="email" type="email" x-model="editSpeaker.email" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                            <div><label class="block text-sm font-medium text-slate-700 mb-1">Phone</label><input name="phone" type="text" x-model="editSpeaker.phone" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label class="block text-sm font-medium text-slate-700 mb-1">Company</label><input name="company" type="text" x-model="editSpeaker.company" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                            <div><label class="block text-sm font-medium text-slate-700 mb-1">Job Title</label><input name="job_title" type="text" x-model="editSpeaker.job_title" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm"></div>
                        </div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Photo</label><input name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-gold-50 file:text-gold-700 hover:file:bg-gold-100 cursor-pointer"></div>
                        <div><label class="block text-sm font-medium text-slate-700 mb-1">Bio</label><textarea name="bio" rows="3" x-model="editSpeaker.bio" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none"></textarea></div>
                    </div>
                    <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @@click="editModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">Update Speaker</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
