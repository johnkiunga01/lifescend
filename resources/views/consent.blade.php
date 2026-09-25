@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-16">
    <div class="absolute inset-0 opacity-20">
        <div class="absolute -top-20 -right-20 h-80 w-80 rounded-full bg-gold-500 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 h-96 w-96 rounded-full bg-gold-400 blur-3xl"></div>
    </div>
    <div class="relative mx-auto max-w-3xl px-4 text-center sm:px-6 lg:px-8">
        <div class="inline-flex h-14 w-14 items-center justify-center rounded-xl bg-white/10 backdrop-blur-sm mb-4 shadow-lg ring-1 ring-inset ring-white/20">
            <svg class="h-7 w-7 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
        </div>
        <h1 class="text-3xl font-extrabold text-white">Policies Updated</h1>
        <p class="mt-2 text-base text-slate-300">Our policies have been updated. Please review and accept to continue.</p>
    </div>
</div>

<div class="bg-slate-50 py-12">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

        <form action="{{ route('consent.store') }}" method="POST">
            @csrf

            @if($needsTerms)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                    <div class="h-8 w-8 rounded-lg bg-indigo-500 flex items-center justify-center text-white"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Terms &amp; Conditions <span class="text-xs font-mono font-normal text-slate-400 ml-1">v{{ $termsVersion }}</span></h2>
                    </div>
                </div>
                <div class="prose prose-slate prose-sm max-w-none max-h-80 overflow-y-auto bg-white rounded-xl border border-slate-200 p-6 mb-4
                    prose-headings:font-bold prose-headings:text-slate-900
                    prose-h1:text-lg prose-h1:text-center prose-h1:mb-1
                    prose-h2:text-base prose-h2:mt-6 prose-h2:mb-3
                    prose-p:text-slate-600 prose-p:leading-relaxed
                    prose-blockquote:border-l-gold-400 prose-blockquote:bg-gold-50 prose-blockquote:rounded-lg prose-blockquote:py-3 prose-blockquote:px-4 prose-blockquote:not-italic prose-blockquote:text-slate-700 prose-blockquote:before:hidden
                    prose-table:text-sm prose-th:bg-slate-50 prose-th:px-4 prose-th:py-2 prose-td:px-4 prose-td:py-2 prose-td:border prose-td:border-slate-200
                    prose-hr:my-6 prose-ul:text-slate-600 prose-li:marker:text-gold-500
                    prose-a:text-gold-600 prose-a:no-underline
                    prose-strong:text-slate-800">
                    @if($termsContent)
                        {!! $termsContent !!}
                    @else
                        <p class="text-slate-400 italic text-center">No content available.</p>
                    @endif
                </div>
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="accept_terms" name="accept_terms" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
                    </div>
                    <label for="accept_terms" class="ml-2 block text-sm text-slate-600">
                        I have read and accept the updated <span class="font-semibold text-slate-800">Terms &amp; Conditions</span> <span class="text-red-500">*</span>
                    </label>
                </div>
                @error('accept_terms')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @endif

            @if($needsPrivacy)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
                <div class="flex items-center gap-3 mb-4 pb-3 border-b border-slate-100">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg></div>
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Privacy Policy <span class="text-xs font-mono font-normal text-slate-400 ml-1">v{{ $privacyVersion }}</span></h2>
                    </div>
                </div>
                <div class="prose prose-slate prose-sm max-w-none max-h-80 overflow-y-auto bg-white rounded-xl border border-slate-200 p-6 mb-4
                    prose-headings:font-bold prose-headings:text-slate-900
                    prose-h1:text-lg prose-h1:text-center prose-h1:mb-1
                    prose-h2:text-base prose-h2:mt-6 prose-h2:mb-3
                    prose-p:text-slate-600 prose-p:leading-relaxed
                    prose-blockquote:border-l-gold-400 prose-blockquote:bg-gold-50 prose-blockquote:rounded-lg prose-blockquote:py-3 prose-blockquote:px-4 prose-blockquote:not-italic prose-blockquote:text-slate-700 prose-blockquote:before:hidden
                    prose-table:text-sm prose-th:bg-slate-50 prose-th:px-4 prose-th:py-2 prose-td:px-4 prose-td:py-2 prose-td:border prose-td:border-slate-200
                    prose-hr:my-6 prose-ul:text-slate-600 prose-li:marker:text-gold-500
                    prose-a:text-gold-600 prose-a:no-underline
                    prose-strong:text-slate-800">
                    @if($privacyContent)
                        {!! $privacyContent !!}
                    @else
                        <p class="text-slate-400 italic text-center">No content available.</p>
                    @endif
                </div>
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input id="accept_privacy" name="accept_privacy" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
                    </div>
                    <label for="accept_privacy" class="ml-2 block text-sm text-slate-600">
                        I have read and accept the updated <span class="font-semibold text-slate-800">Privacy Policy</span> <span class="text-red-500">*</span>
                    </label>
                </div>
                @error('accept_privacy')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <div class="flex items-center justify-center gap-3">
                <a href="{{ route('logout') }}" class="rounded-xl px-6 py-3 text-sm font-semibold text-slate-600 hover:bg-slate-100 border border-slate-200 transition-colors">
                    Logout
                </a>
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                    Accept &amp; Continue
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
