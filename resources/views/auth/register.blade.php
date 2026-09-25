@extends('layouts.app')

@section('content')
<div class="flex min-h-[90vh]">
    {{-- Left brand panel --}}
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gold-500 via-gold-600 to-amber-700 relative overflow-hidden items-center justify-center p-12">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 rounded-full bg-white animate-float-slow"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 rounded-full bg-white animate-float-slower"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] rounded-full bg-white/5 animate-pulse-glow"></div>
        </div>
        <div class="relative z-10 text-center max-w-md">
            <div class="inline-flex w-[156px] items-center justify-center rounded-2xl bg-white/10 backdrop-blur-sm shadow-lg overflow-hidden">
                @if($organization?->logo)<img src="{{ Storage::url($organization->logo) }}" alt="Logo" class="w-full">@else<span class="text-3xl font-extrabold text-white">L</span>@endif
            </div>
            <p class="text-lg text-white/80 leading-relaxed">Join thousands of professionals advancing their careers through world-class training.</p>
            <div class="mt-8 flex justify-center gap-3">
                <div class="h-2 w-2 rounded-full bg-white/40"></div>
                <div class="h-2 w-8 rounded-full bg-white/80"></div>
                <div class="h-2 w-2 rounded-full bg-white/40"></div>
            </div>
        </div>
    </div>

    {{-- Right form panel --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-10 bg-slate-50">
        <div class="w-full max-w-md">
            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="inline-flex w-[94px] items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm overflow-hidden">
                    @if($organization?->logo)<img src="{{ Storage::url($organization->logo) }}" alt="Logo" class="w-full">@else<span class="text-xl font-extrabold text-white">L</span>@endif
                </div>
            </div>

            <div class="bg-white py-8 px-6 sm:px-10 shadow-xl border border-slate-200 rounded-2xl">
                <div class="mb-6">
                    <h2 class="text-xl font-extrabold text-slate-900">Create your account</h2>
                    <p class="text-sm text-slate-500 mt-1">Join us and start attending events.</p>
                </div>

                <form class="space-y-5" action="{{ route('register') }}" method="POST" x-data="{
                    password: '',
                    get strength() {
                        let s = 0;
                        if (this.password.length >= 8) s++;
                        if (/[a-z]/.test(this.password) && /[A-Z]/.test(this.password)) s++;
                        if (/[0-9]/.test(this.password)) s++;
                        if (/[^a-zA-Z0-9]/.test(this.password)) s++;
                        return s;
                    },
                    get strengthLabel() {
                        return ['', 'Weak', 'Fair', 'Good', 'Strong'][this.strength];
                    },
                    get strengthColor() {
                        return ['', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-emerald-500'][this.strength];
                    },
                    get strengthTextColor() {
                        return ['', 'text-red-600', 'text-orange-600', 'text-yellow-600', 'text-emerald-600'][this.strength];
                    }
                }">
                    @csrf

                    @if ($errors->any())
                        <div class="rounded-xl bg-red-50 border border-red-200 p-4">
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

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">Full Name <span class="text-red-500">*</span></label>
                            <div class="mt-1.5">
                                <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="John Doe"
                                    class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm border focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label for="username" class="block text-sm font-medium text-slate-700">Username</label>
                            <div class="mt-1.5">
                                <input id="username" name="username" type="text" value="{{ old('username') }}" placeholder="johndoe (optional)"
                                    class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm border focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">Email address <span class="text-red-500">*</span></label>
                        <div class="mt-1.5">
                            <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="you@example.com"
                                class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm border focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">Password <span class="text-red-500">*</span></label>
                        <div class="relative mt-1.5" x-data="{ show: false }">
                            <input id="password" name="password" :type="show ? 'text' : 'password'" x-model="password" required placeholder="Create a strong password"
                                class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 pr-10 text-slate-900 placeholder-slate-400 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm border focus:outline-none">
                            <button type="button" @click="show = !show" tabindex="-1"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg x-show="show" style="display:none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                        <div class="mt-2 space-y-1">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                    <div x-show="password.length > 0" x-bind:class="strengthColor" class="h-full rounded-full transition-all duration-300" x-bind:style="'width: ' + (strength * 25) + '%'"></div>
                                </div>
                                <span x-show="password.length > 0" x-text="strengthLabel" x-bind:class="strengthTextColor" class="text-xs font-semibold"></span>
                            </div>
                            <ul class="text-xs text-slate-500 space-y-0.5">
                                <li x-bind:class="password.length >= 8 ? 'text-emerald-600' : ''" class="transition-colors duration-200">At least 8 characters</li>
                                <li x-bind:class="/[a-z]/.test(password) && /[A-Z]/.test(password) ? 'text-emerald-600' : ''" class="transition-colors duration-200">Uppercase &amp; lowercase letters</li>
                                <li x-bind:class="/[0-9]/.test(password) ? 'text-emerald-600' : ''" class="transition-colors duration-200">At least one number</li>
                                <li x-bind:class="/[^a-zA-Z0-9]/.test(password) ? 'text-emerald-600' : ''" class="transition-colors duration-200">At least one symbol (!@#$%^&amp;*)</li>
                            </ul>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm Password <span class="text-red-500">*</span></label>
                        <div class="relative mt-1.5" x-data="{ show: false }">
                            <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" required placeholder="Confirm your password"
                                class="block w-full rounded-xl border-slate-300 bg-white px-4 py-3 pr-10 text-slate-900 placeholder-slate-400 shadow-sm focus:border-gold-500 focus:ring-gold-500 sm:text-sm border focus:outline-none">
                            <button type="button" @click="show = !show" tabindex="-1"
                                class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.641 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>
                                <svg x-show="show" style="display:none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Terms acceptance --}}
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="terms" name="terms" type="checkbox" value="1" {{ old('terms') ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-slate-300 text-gold-500 focus:ring-gold-500">
                        </div>
                        <label for="terms" class="ml-2 block text-sm text-slate-600">
                            I accept the
                            <a href="{{ route('terms') }}" target="_blank" class="font-medium text-gold-600 hover:text-gold-700 underline">Terms &amp; Conditions</a>
                            and
                            <a href="{{ route('privacy') }}" target="_blank" class="font-medium text-gold-600 hover:text-gold-700 underline">Privacy Policy</a>
                            <span class="text-red-500">*</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="flex w-full justify-center rounded-xl bg-gold-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/30 hover:bg-gold-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gold-500 transition-all duration-200 cursor-pointer">
                        Create Account
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-slate-500">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-medium text-gold-600 hover:text-gold-700">Sign in</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
