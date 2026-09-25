@extends('layouts.app')

@section('content')
<div class="flex min-h-[90vh]">

    {{-- LEFT BRAND PANEL (desktop only) --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gold-500 via-gold-600 to-amber-700 relative overflow-hidden items-center justify-center p-12">
        {{-- Animated floating background circles --}}
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 rounded-full bg-white animate-float-slow"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 rounded-full bg-white animate-float-slower"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full bg-white/5 animate-pulse-glow"></div>
        </div>
        {{-- Brand text --}}
        <div class="relative z-10 text-center max-w-md">
            <div class="inline-flex w-[156px] items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm shadow-lg overflow-hidden">
                @if($organization?->logo)<img src="{{ Storage::url($organization->logo) }}" alt="Logo" class="w-full">@else<span class="text-3xl font-extrabold text-white">L</span>@endif
            </div>
            <p class="text-lg text-white/80 leading-relaxed">Empowering professionals through world-class training, workshops, and events.</p>
            <div class="mt-8 flex justify-center gap-3">
                <div class="h-2 w-2 rounded-full bg-white/40"></div>
                <div class="h-2 w-8 rounded-full bg-white/80"></div>
                <div class="h-2 w-2 rounded-full bg-white/40"></div>
            </div>
        </div>
    </div>

    {{-- RIGHT FORM PANEL --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 bg-slate-50">
        <div class="w-full max-w-md">

            {{-- Mobile logo (hidden on desktop) --}}
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex w-[94px] items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm overflow-hidden">
                    @if($organization?->logo)<img src="{{ Storage::url($organization->logo) }}" alt="Logo" class="w-full">@else<span class="text-xl font-extrabold text-white">L</span>@endif
                </div>
            </div>

            {{-- Form card --}}
            <div class="bg-white py-8 px-6 sm:px-10 shadow-xl border border-slate-200 rounded-2xl">
                <div class="mb-6">
                    <h2 class="text-xl font-extrabold text-slate-900">Forgot your password?</h2>
                    <p class="text-sm text-slate-500 mt-1">Enter your email and we'll send you a reset link.</p>
                </div>

                @if(session('status'))
                <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ session('status') }}
                </div>
                @endif

                @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                            <ul class="mt-2 list-disc space-y-1 pl-5 text-xs text-red-700">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <form class="space-y-5" action="{{ route('password.email') }}" method="POST">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email address</label>
                        <div class="mt-1.5">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                   value="{{ old('email') }}" placeholder="you@example.com"
                                   class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-slate-900
                                          placeholder-slate-400 shadow-sm focus:border-gold-500 focus:ring-gold-500
                                          sm:text-sm border focus:outline-none">
                        </div>
                    </div>

                    <button type="submit"
                        class="flex w-full justify-center rounded-xl bg-gold-500 px-4 py-3 text-sm font-bold text-white
                               shadow-lg shadow-gold-500/30 hover:bg-gold-600 focus-visible:outline focus-visible:outline-2
                               focus-visible:outline-offset-2 focus-visible:outline-gold-500 transition-all duration-200 cursor-pointer">
                        Send Password Reset Link
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gold-600 hover:text-gold-700">
                        &larr; Back to login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
