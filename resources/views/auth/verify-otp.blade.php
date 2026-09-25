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
            Enter the 6-digit code sent to <strong class="text-slate-700">{{ $email }}</strong>
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl border border-slate-200 rounded-2xl sm:px-10">

            @if(request('support') === '1')
            {{-- Support request form when OTP limit is exceeded --}}
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 mb-6 text-sm text-red-700">
                <p class="font-semibold">You've reached the maximum number of verification attempts.</p>
                <p class="mt-1">Submit a request below and an administrator will review your account.</p>
            </div>

            <form action="{{ route('account-support.store') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Your Name</label>
                    <input name="name" type="text" placeholder="e.g. John Doe"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Message <span class="text-red-500">*</span></label>
                    <textarea name="message" rows="4" required placeholder="Describe your issue and we'll help get you verified."
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm resize-none"></textarea>
                </div>
                <button type="submit"
                    class="flex w-full justify-center rounded-xl bg-gold-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/30 hover:bg-gold-600 transition-all duration-200">
                    Submit Support Request
                </button>
            </form>

            @else
            {{-- Normal OTP verification form --}}
            @if(session('status'))
            <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                {{ session('status') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-6 rounded-xl bg-red-50 border border-red-200 px-5 py-4 text-sm text-red-700">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('verification.verify-otp') }}" method="POST" class="space-y-6">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label for="otp" class="block text-sm font-medium text-slate-700 mb-2 text-center">Verification Code</label>
                    <div class="flex justify-center gap-2" x-data="{
                        digits: ['', '', '', '', '', ''],
                        focusNext(el, i) {
                            if (el.value.length === 1 && i < 5) {
                                document.getElementById('otp-' + (i + 1)).focus();
                            }
                        },
                        focusPrev(el, i) {
                            if (el.value.length === 0 && i > 0) {
                                document.getElementById('otp-' + (i - 1)).focus();
                            }
                        }
                    }">
                        @for($i = 0; $i < 6; $i++)
                        <input id="otp-{{ $i }}" name="digits[]" type="text" inputmode="numeric" maxlength="1"
                            x-model="digits[{{ $i }}]"
                            @@input="focusNext($el, {{ $i }})"
                            @@keydown.backspace="focusPrev($el, {{ $i }})"
                            class="w-12 h-14 text-center text-xl font-extrabold rounded-xl border border-slate-300 bg-white text-slate-900 shadow-sm focus:border-gold-500 focus:ring-gold-500 focus:outline-none transition-all duration-150"
                            autocomplete="one-time-code">
                        @endfor
                    </div>
                    <input type="hidden" name="otp" x-bind:value="digits.join('')">
                </div>

                <button type="submit"
                    class="flex w-full justify-center rounded-xl bg-gold-500 px-4 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/30 hover:bg-gold-600 transition-all duration-200">
                    Verify Email
                </button>
            </form>

            <div class="mt-6 flex flex-col items-center gap-2 text-sm text-slate-500" x-data="{
                cooldown: 0,
                startCooldown() {
                    this.cooldown = 180;
                    const interval = setInterval(() => {
                        this.cooldown--;
                        if (this.cooldown <= 0) {
                            clearInterval(interval);
                        }
                    }, 1000);
                }
            }">
                <div class="flex items-center gap-1">
                    <span>Didn't receive the code?</span>
                    <form action="{{ route('verification.resend-otp') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <button type="submit" x-on:click="startCooldown"
                            x-bind:disabled="cooldown > 0"
                            class="font-semibold underline transition-colors duration-200"
                            x-bind:class="cooldown > 0 ? 'text-slate-300 cursor-not-allowed' : 'text-gold-600 hover:text-gold-700 cursor-pointer'">
                            Resend
                        </button>
                    </form>
                </div>
                <p x-show="cooldown > 0" x-text="'Resend available in ' + Math.floor(cooldown / 60) + 'm ' + (cooldown % 60) + 's'" class="text-xs text-slate-400"></p>
            </div>

            @if(Auth::check())
            <div class="mt-4 text-center">
                <a href="{{ route('logout') }}" class="text-sm text-slate-400 hover:text-slate-600 transition-colors">
                    Not your email? Logout
                </a>
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection