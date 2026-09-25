@extends('layouts.setup')

@section('content')
<div class="flex min-h-screen">

    {{-- Left brand panel --}}
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
            <p class="text-lg text-white/80 leading-relaxed">Let's get your platform set up. This will only take a minute.</p>
            <div class="mt-10 space-y-3 text-left">
                @foreach ([
                    'Run database migrations',
                    'Create your organization',
                    'Set up your admin account',
                    'Verify your email (OTP sent)',
                ] as $step)
                <div class="flex items-center gap-3 text-white/80 text-sm">
                    <div class="w-5 h-5 rounded-full bg-white/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    {{ $step }}
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 bg-slate-50">
        <div class="w-full max-w-md">

            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex w-16 h-16 items-center justify-center rounded-2xl bg-gold-500 mb-3">
                    <span class="text-2xl font-extrabold text-white">L</span>
                </div>
                <h1 class="text-xl font-extrabold text-slate-900">Lifescend Setup</h1>
            </div>

            <div class="bg-white py-8 px-6 sm:px-10 shadow-xl border border-slate-200 rounded-2xl">
                <div class="mb-6">
                    <h2 class="text-xl font-extrabold text-slate-900">First-time Installation</h2>
                    <p class="text-sm text-slate-500 mt-1">Create your organization and admin account to get started.</p>
                </div>

                @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-200 p-4">
                    <div class="flex gap-2">
                        <svg class="h-5 w-5 text-red-500 flex-shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                        </svg>
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <form action="{{ route('setup.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Organization section --}}
                    <div>
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Organization</h3>
                        <div>
                            <label for="org_name" class="block text-sm font-medium text-slate-700 mb-1.5">Organization Name</label>
                            <input type="text" id="org_name" name="org_name" value="{{ old('org_name') }}" required autofocus
                                class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 @error('org_name') border-red-400 @enderror"
                                placeholder="Acme Training Ltd">
                        </div>
                    </div>

                    {{-- Admin account section --}}
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Administrator Account</h3>
                        <div class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 @error('name') border-red-400 @enderror"
                                    placeholder="Jane Doe">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 @error('email') border-red-400 @enderror"
                                    placeholder="admin@yourcompany.com">
                            </div>
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                                <div class="relative" x-data="{ show: false }">
                                    <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                        class="w-full px-3.5 py-2.5 pr-10 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500 @error('password') border-red-400 @enderror"
                                        placeholder="Min 8 chars, upper+lower, number, symbol">
                                    <button type="button" @click="show = !show" tabindex="-1"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600">
                                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        <svg x-show="show" style="display:none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
                                <div class="relative" x-data="{ show: false }">
                                    <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                                        class="w-full px-3.5 py-2.5 pr-10 border border-slate-200 rounded-xl text-sm placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500"
                                        placeholder="Repeat your password">
                                    <button type="button" @click="show = !show" tabindex="-1"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600">
                                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                        <svg x-show="show" style="display:none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-3 px-4 bg-gold-600 hover:bg-gold-700 text-white font-semibold rounded-xl transition-colors text-sm shadow-sm">
                        Install &amp; Create Admin Account
                    </button>

                    <p class="text-center text-xs text-slate-400 pt-1">
                        A 6-digit verification code will be sent to your email after installation.
                    </p>
                </form>
            </div>

            <p class="mt-4 text-center text-xs text-slate-400">
                This page is only visible before installation is complete.
            </p>
        </div>
    </div>
</div>
@endsection
