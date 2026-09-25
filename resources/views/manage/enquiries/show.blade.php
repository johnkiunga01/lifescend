@extends('layouts.dashboard')

@section('pageTitle', 'Enquiry Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('manage.enquiries.index') }}" class="inline-flex items-center gap-1 text-sm text-gold-600 hover:text-gold-700 font-semibold mb-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                Back to Enquiries
            </a>
            <h1 class="text-2xl font-extrabold text-slate-900">Enquiry Details</h1>
            <p class="text-sm text-slate-500 mt-1">Submitted {{ $enquiry->created_at->format('M d, Y \a\t H:i') }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if($enquiry->replied_at)
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-emerald-100 text-emerald-700">Replied</span>
            @else
            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold bg-amber-100 text-amber-700">Pending</span>
            @endif
            <form action="{{ route('manage.enquiries.destroy', $enquiry->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this enquiry?');">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 transition-colors cursor-pointer">Delete</button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="p-6 space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Name</label>
                    <p class="text-sm font-semibold text-slate-900">{{ $enquiry->name }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Contact</label>
                    <p class="text-sm text-slate-900">{{ $enquiry->contact }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Company</label>
                    <p class="text-sm text-slate-900">{{ $enquiry->company ?? '—' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Event</label>
                    <p class="text-sm text-slate-900">{{ $enquiry->event->name ?? 'General' }}</p>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">Message</label>
                <div class="rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-700 whitespace-pre-wrap">{{ $enquiry->message }}</div>
            </div>

            @if($enquiry->replied_at)
            <div class="pt-4 border-t border-slate-100">
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Replied At</label>
                <p class="text-sm text-slate-900">{{ $enquiry->replied_at->format('M d, Y \a\t H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-extrabold text-slate-900 mb-4">Send Reply</h2>
        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6">
            <form action="{{ route('manage.enquiries.reply', $enquiry->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Reply Message <span class="text-red-500">*</span></label>
                        <textarea name="reply_message" rows="6" required placeholder="Type your response to this enquiry..." class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Send to Email</label>
                        <input type="email" name="reply_email" value="{{ $enquiry->contact }}" placeholder="Email address to send reply"
                               class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                        <p class="text-xs text-slate-500 mt-1">Auto-filled from contact field. Edit if needed. Leave as-is if contact is already an email.</p>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('manage.enquiries.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection