@extends('layouts.app')

@section('content')
<div class="flex min-h-[80vh] flex-col justify-center py-12 sm:px-6 lg:px-8 bg-slate-50">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="mx-auto h-16 w-16 rounded-2xl bg-gold-500 flex items-center justify-center shadow-lg shadow-gold-500/30">
            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold tracking-tight text-slate-900">
            Verify your email
        </h2>
        <p class="mt-2 text-center text-sm text-slate-500">
            Thanks for registering! Before you get started, please verify your email address.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl border border-slate-200 rounded-2xl sm:px-10">
            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('success') }}
            </div>
            @endif

            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <div class="text-sm text-amber-800">
                        <p class="font-semibold">Verification email sent to <strong>{{ Auth::user()->email }}</strong></p>
                        <p class="mt-1">If you didn't receive the email, click below to resend.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit"
                    class="flex w-full justify-center rounded-xl bg-gold-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/30 hover:bg-gold-600 transition-all duration-200">
                    Resend Verification Email
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
                    Skip for now &rarr;
                </a>
            </div>
        </div>

        <p class="mt-4 text-center text-xs text-slate-400">
            Didn't register with this email? <a href="{{ route('logout') }}" class="text-gold-600 hover:text-gold-700 font-medium">Logout</a>
        </p>
    </div>
</div>
@endsection