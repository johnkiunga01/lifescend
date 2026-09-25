@extends('layouts.dashboard')

@section('pageTitle', 'Contact Messages')

@section('content')
<div x-data="{ detailModal: false, selectedMessage: { id: null, name: '', email: '', subject: '', message: '', created_at: '' } }">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Contact Messages</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $messages->total() }} total messages from the contact form.</p>
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
                        <th class="py-3.5 pl-6 pr-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Email</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Subject</th>
                        <th class="px-3 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="px-3 py-3.5 text-right pr-6 text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($messages as $msg)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 pl-6 pr-3">
                            <p class="text-sm font-semibold text-slate-900">{{ $msg->name }}</p>
                        </td>
                        <td class="px-3 py-4 text-sm text-slate-600">{{ $msg->email }}</td>
                        <td class="px-3 py-4 text-sm text-slate-600 max-w-[200px] truncate">{{ $msg->subject }}</td>
                        <td class="px-3 py-4 text-sm text-slate-500">{{ $msg->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-3 py-4 text-right pr-6">
                            <div class="flex items-center justify-end gap-2">
                                <button @@click="selectedMessage = { id: {{ $msg->id }}, name: '{{ addslashes($msg->name) }}', email: '{{ addslashes($msg->email) }}', subject: '{{ addslashes($msg->subject) }}', message: '{{ addslashes($msg->message) }}', created_at: '{{ $msg->created_at->format('M d, Y H:i') }}' }; detailModal = true"
                                        class="rounded-lg px-3 py-1.5 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors cursor-pointer">
                                    View
                                </button>
                                <form action="{{ route('manage.contact-messages.destroy', $msg->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this message?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-12 text-center text-sm text-slate-500">No contact messages yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6">
        {{ $messages->links() }}
    </div>

    {{-- Detail Modal --}}
    <div x-show="detailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="detailModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="detailModal = false" class="fixed inset-0 bg-black/60"></div>
            <div x-show="detailModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4" @@click.away="detailModal = false" class="relative w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900">Message Details</h2>
                        <p class="text-sm text-slate-500 mt-1" x-text="'From ' + selectedMessage.name"></p>
                    </div>
                    <button @@click="detailModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 mb-5 space-y-2 text-sm">
                    <p><span class="font-semibold text-slate-700">Name:</span> <span class="text-slate-600" x-text="selectedMessage.name"></span></p>
                    <p><span class="font-semibold text-slate-700">Email:</span> <span class="text-slate-600" x-text="selectedMessage.email"></span></p>
                    <p><span class="font-semibold text-slate-700">Subject:</span> <span class="text-slate-600" x-text="selectedMessage.subject"></span></p>
                    <p><span class="font-semibold text-slate-700">Date:</span> <span class="text-slate-600" x-text="selectedMessage.created_at"></span></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Message</label>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap" x-text="selectedMessage.message"></div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a x-bind:href="'mailto:' + selectedMessage.email" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Reply via Email
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection