@extends('layouts.dashboard')

@section('pageTitle', 'Account Support')

@section('content')
<div x-data="{ detailModal: false, selected: { id: null, name: '', email: '', message: '', status: '', created_at: '' } }">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Account Support Requests</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $requests->total() }} total requests.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">User</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="px-3 py-3.5 text-right pr-6 text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $r)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 pl-6 pr-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $r->name ?? $r->user?->name ?? 'Guest' }}</p>
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $r->email }}</td>
                        <td class="px-3 py-4">
                            @if($r->status === 'open')
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-amber-100 text-amber-700">Open</span>
                            @else
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700">Resolved</span>
                            @endif
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-500">{{ $r->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-3 py-4 text-right pr-6">
                            <div class="flex items-center justify-end gap-2">
                                <button @@click="selected = { id: {{ $r->id }}, name: '{{ addslashes($r->name ?? $r->user?->name ?? 'Guest') }}', email: '{{ addslashes($r->email) }}', message: '{{ addslashes($r->message) }}', status: '{{ $r->status }}', created_at: '{{ $r->created_at->format('M d, Y H:i') }}' }; detailModal = true"
                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors cursor-pointer">
                                    View
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-12 text-center text-sm text-slate-500">No support requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">{{ $requests->links() }}</div>

    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="detailModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="detailModal = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="detailModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="detailModal = false" class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Support Request</h2>
                        <p class="text-sm text-slate-500 mt-1" x-text="'From ' + selected.name"></p>
                    </div>
                    <button @@click="detailModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <div class="bg-slate-50 rounded-xl p-4 mb-5 space-y-2 text-sm">
                    <p><span class="font-semibold text-slate-700">Name:</span> <span class="text-slate-600" x-text="selected.name"></span></p>
                    <p><span class="font-semibold text-slate-700">Email:</span> <span class="text-slate-600" x-text="selected.email"></span></p>
                    <p><span class="font-semibold text-slate-700">Date:</span> <span class="text-slate-600" x-text="selected.created_at"></span></p>
                    <p><span class="font-semibold text-slate-700">Status:</span> <span class="text-slate-600" x-text="selected.status"></span></p>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap" x-text="selected.message"></div>
                </div>
                <form x-bind:action="'/manage/account-support/' + selected.id + '/resolve'" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Admin Notes</label>
                        <textarea name="admin_notes" rows="3" placeholder="Add resolution notes..." class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" @@click="detailModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Mark Resolved
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection