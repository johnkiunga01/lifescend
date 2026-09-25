@extends('layouts.dashboard')

@section('pageTitle', 'Account Settings')

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-extrabold text-slate-900">Account Settings</h1>
    <p class="text-sm text-slate-500 mt-1">Manage your profile, email address, and password.</p>
</div>

@if(session('success'))
<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700 shadow-sm">
    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
    {{ session('success') }}
</div>
@endif

@if($errors->any() && !session('success'))
<div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700 shadow-sm">
    <ul class="list-disc pl-5 space-y-1">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="grid gap-6 lg:grid-cols-2">
    {{-- Profile --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">Profile Information</h2>
                    <p class="text-xs text-gold-200">Update your name and email address</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form action="{{ route('customer.settings.update') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="settings-name" class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                    <input id="settings-name" type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                           placeholder="e.g. John Doe"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                    @error('name')<p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="settings-email" class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input id="settings-email" type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                           placeholder="you@example.com"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                    @error('email')<p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>@enderror
                    @if(!Auth::user()->hasVerifiedEmail())
                    <div class="mt-2 flex items-center gap-2 rounded-lg bg-amber-50 border border-amber-200 px-3.5 py-2.5">
                        <svg class="h-4 w-4 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                        <span class="text-xs font-medium text-amber-700">Email not verified.</span>
                        <a href="{{ route('verification.send') }}" class="text-xs font-bold text-amber-700 underline ml-auto whitespace-nowrap" onclick="event.preventDefault(); fetch(this.href, {method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>alert('Verification email sent!'));">Resend verification</a>
                    </div>
                    @endif
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Password --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden" x-data="{
        password: '',
        get strength() {
            let s = 0;
            if (this.password.length >= 8) s++;
            if (/[a-z]/.test(this.password) && /[A-Z]/.test(this.password)) s++;
            if (/[0-9]/.test(this.password)) s++;
            if (/[^a-zA-Z0-9]/.test(this.password)) s++;
            return s;
        },
        get strengthLabel() { return ['', 'Weak', 'Fair', 'Good', 'Strong'][this.strength]; },
        get strengthColor() { return ['', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-emerald-500'][this.strength]; },
        get strengthTextColor() { return ['', 'text-red-600', 'text-orange-600', 'text-yellow-600', 'text-emerald-600'][this.strength]; }
    }">
        <div class="bg-gradient-to-r from-slate-800 to-slate-700 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm">
                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white">Change Password</h2>
                    <p class="text-xs text-slate-300">Update your account password</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form action="{{ route('customer.settings.update') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label for="settings-current-password" class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                    <input id="settings-current-password" type="password" name="current_password"
                           placeholder="Enter your current password"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                    @error('current_password')<p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="settings-new-password" class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                    <input id="settings-new-password" type="password" name="new_password" x-model="password" minlength="8"
                           placeholder="Create a new password"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                    <div x-show="password.length > 0" class="mt-2 space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 rounded-full bg-slate-200 overflow-hidden">
                                <div x-bind:class="strengthColor" class="h-full rounded-full transition-all duration-300" x-bind:style="'width: ' + (strength * 25) + '%'"></div>
                            </div>
                            <span x-text="strengthLabel" x-bind:class="strengthTextColor" class="text-xs font-semibold"></span>
                        </div>
                        <ul class="text-xs text-slate-500 space-y-0.5">
                            <li x-bind:class="password.length >= 8 ? 'text-emerald-600' : ''" class="transition-colors duration-200">At least 8 characters</li>
                            <li x-bind:class="/[a-z]/.test(password) && /[A-Z]/.test(password) ? 'text-emerald-600' : ''" class="transition-colors duration-200">Uppercase &amp; lowercase letters</li>
                            <li x-bind:class="/[0-9]/.test(password) ? 'text-emerald-600' : ''" class="transition-colors duration-200">At least one number</li>
                            <li x-bind:class="/[^a-zA-Z0-9]/.test(password) ? 'text-emerald-600' : ''" class="transition-colors duration-200">At least one symbol (!@#$%^&amp;*)</li>
                        </ul>
                    </div>
                    @error('new_password')<p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="settings-confirm-password" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                    <input id="settings-confirm-password" type="password" name="new_password_confirmation"
                           placeholder="Re-enter your new password"
                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-slate-800/25 hover:bg-slate-700 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
