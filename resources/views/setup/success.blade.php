@extends('layouts.setup')

@section('content')
<div class="flex min-h-screen">
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gold-500 via-gold-600 to-amber-700 relative overflow-hidden items-center justify-center p-12">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 rounded-full bg-white"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 rounded-full bg-white"></div>
        </div>
        <div class="relative z-10 text-center max-w-md">
            <div class="inline-flex w-20 h-20 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm mb-6">
                <span class="text-3xl font-extrabold text-white">L</span>
            </div>
            <h1 class="text-4xl font-extrabold text-white mb-4">Lifescend</h1>
            <p class="text-lg text-white/80 leading-relaxed">Your platform is ready. Let's get started.</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 bg-slate-50">
        <div class="w-full max-w-md text-center">
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex w-16 h-16 items-center justify-center rounded-2xl bg-gold-500 mb-3">
                    <span class="text-2xl font-extrabold text-white">L</span>
                </div>
            </div>

            <div class="bg-white py-10 px-6 sm:px-10 shadow-xl border border-slate-200 rounded-2xl">
                <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-green-100 mb-5">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-extrabold text-slate-900 mb-2">Installation Complete!</h2>
                <p class="text-sm text-slate-500 mb-8">
                    Your organization and admin account have been created successfully. You can now sign in.
                </p>

                <a href="{{ route('login') }}"
                   class="inline-flex w-full items-center justify-center py-3 px-4 bg-gold-600 hover:bg-gold-700 text-white font-semibold rounded-xl transition-colors text-sm shadow-sm">
                    Go to Login
                </a>
            </div>

            <p class="mt-6 text-xs text-slate-400">
                &copy; {{ date('Y') }} Lifescend. All rights reserved.
            </p>
        </div>
    </div>
</div>
@endsection
