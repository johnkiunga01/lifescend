@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-gold-50 via-white to-gold-100 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex items-center rounded-full bg-gold-400/10 px-4 py-1.5 text-sm font-semibold text-gold-600 ring-1 ring-inset ring-gold-400/30 mb-4">
            Get In Touch
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
            Contact <span class="text-gold-500">Us</span>
        </h1>
        <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">
            Have a question or want to discuss a training opportunity? We'd love to hear from you.
        </p>
    </div>
</div>

<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-12 lg:grid-cols-2">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 mb-6">Send Us a Message</h2>

            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            <form action="{{ route('contact.send') }}" method="POST" class="space-y-5" onsubmit="document.getElementById('submit-btn').disabled=true; document.getElementById('submit-btn').innerHTML='Sending...';">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Your Name <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" required value="{{ old('name') }}" placeholder="e.g. John Doe"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-gold-500 sm:text-sm focus:outline-none">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Your Email <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" placeholder="e.g. john@example.com"
                            class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-gold-500 sm:text-sm focus:outline-none">
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-medium text-slate-700 mb-1">Subject <span class="text-red-500">*</span></label>
                    <input id="subject" name="subject" type="text" required value="{{ old('subject') }}" placeholder="e.g. Training Inquiry"
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-gold-500 sm:text-sm focus:outline-none">
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-slate-700 mb-1">Message <span class="text-red-500">*</span></label>
                    <textarea id="message" name="message" rows="5" required placeholder="Tell us about your inquiry..."
                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-gold-500 sm:text-sm focus:outline-none resize-none">{{ old('message') }}</textarea>
                </div>

                <div>
                    <button id="submit-btn" type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-8 py-3.5 text-sm font-bold text-white shadow-lg shadow-gold-500/30 hover:bg-gold-600 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Send Message
                    </button>
                </div>
            </form>

            @if ($errors->any())
            <div class="mt-6 rounded-xl bg-red-50 border border-red-200 p-4">
                <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        <div class="space-y-8">
            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-6">Contact Information</h3>
                <div class="space-y-5">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center text-gold-600 shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Email</p>
                            <p class="text-sm text-slate-600">{{ $organization->email ?? 'info@lifescend.ai' }}</p>
                        </div>
                    </div>
                    @if($organization->address ?? false)
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center text-gold-600 shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Location</p>
                            <p class="text-sm text-slate-600">{{ $organization->address }}</p>
                        </div>
                    </div>
                    @endif
                    @if($organization->phone ?? false)
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center text-gold-600 shrink-0">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Phone</p>
                            <p class="text-sm text-slate-600">{{ $organization->phone }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
                <h3 class="text-lg font-bold text-slate-900 mb-4">Need Custom Training?</h3>
                <p class="text-sm text-slate-600 mb-4">
                    If you have specific training requirements, submit a request and our team will create a program tailored to your needs.
                </p>
                <button @@click="trainingModal = true" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all duration-200 cursor-pointer">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Request Training
                </button>
            </div>
        </div>
    </div>
</div>
@endsection