@extends('layouts.dashboard')

@section('pageTitle', 'My Enquiries')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">My Enquiries</h1>
    <p class="text-sm text-slate-500 mt-1">View your event enquiries and staff responses.</p>
</div>

<div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
    @if($enquiries->isNotEmpty())
        <div class="divide-y divide-slate-100">
            @foreach($enquiries as $enquiry)
            <div class="p-6 hover:bg-slate-50 transition-colors">
                <div class="flex items-start justify-between gap-4 mb-3">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-gold-100 flex items-center justify-center shrink-0">
                            <svg class="h-4 w-4 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900">{{ $enquiry->event->name ?? 'General Enquiry' }}</p>
                            <p class="text-xs text-slate-500">{{ $enquiry->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    @if($enquiry->replied_at)
                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 shrink-0">Replied</span>
                    @else
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700 shrink-0">Pending</span>
                    @endif
                </div>
                <div class="ml-12">
                    <div class="rounded-xl bg-slate-50 p-4 border border-slate-200">
                        <p class="text-xs text-slate-500 mb-1.5 font-semibold">Your message:</p>
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $enquiry->message ?? 'No message provided.' }}</p>
                    </div>
                    @if($enquiry->replied_at)
                    <div class="rounded-xl bg-emerald-50 p-4 border border-emerald-200 mt-3">
                        <p class="text-xs text-emerald-600 mb-1.5 font-semibold">Staff replied ({{ $enquiry->replied_at->format('M d, Y \a\t g:i A') }}):</p>
                        @if($enquiry->reply_message)
                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $enquiry->reply_message }}</p>
                        @else
                        <p class="text-sm text-slate-700 leading-relaxed">Your enquiry has been reviewed. We will contact you at <strong>{{ $enquiry->contact }}</strong>.</p>
                        @endif

                        @if($enquiry->attachments->isNotEmpty())
                        <div class="mt-3 pt-3 border-t border-emerald-200 space-y-2">
                            <p class="text-xs font-semibold text-emerald-700">Attachments</p>
                            @foreach($enquiry->attachments as $attachment)
                            <div class="flex items-center justify-between gap-3 rounded-lg border border-emerald-200 bg-white px-3 py-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" /></svg>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-800 truncate">{{ $attachment->original_name }}</p>
                                        <p class="text-xs text-slate-400">{{ $attachment->humanSize() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 shrink-0">
                                    @if($attachment->isPreviewable())
                                    <a href="{{ route('enquiries.attachment.preview', $attachment->id) }}" target="_blank" class="rounded-lg px-2.5 py-1 text-xs font-semibold text-gold-600 hover:bg-gold-50 transition-colors">Preview</a>
                                    @endif
                                    <a href="{{ route('enquiries.attachment.download', $attachment->id) }}" class="rounded-lg px-2.5 py-1 text-xs font-semibold text-slate-600 hover:bg-slate-100 transition-colors">Download</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-16">
            <svg class="h-12 w-12 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
            <h3 class="text-lg font-semibold text-slate-500 mb-1">No enquiries yet</h3>
            <p class="text-sm text-slate-400">When you send an enquiry about an event, it will appear here.</p>
        </div>
    @endif
</div>
@endsection