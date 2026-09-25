@extends('layouts.app')

@section('content')
<div class="bg-gradient-to-br from-gold-50 via-white to-gold-100 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex items-center rounded-full bg-gold-400/10 px-4 py-1.5 text-sm font-semibold text-gold-600 ring-1 ring-inset ring-gold-400/30 mb-4">
            Who We Are
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl">
            About <span class="text-gold-500">Lifescend</span>
        </h1>
        <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">
            Empowering pharma and biotech professionals through world-class training and events.
        </p>
    </div>
</div>

<div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid gap-16 lg:grid-cols-2 items-center mb-20">
        <div>
            <h2 class="text-3xl font-extrabold text-slate-900">Our Mission</h2>
            <p class="mt-4 text-slate-600 leading-relaxed">
                Lifescend is dedicated to bridging the skills gap in the pharmaceutical and biotechnology industries by providing accessible, high-quality training programs and professional events. We connect life science professionals with industry experts through conferences, workshops, and customized training solutions.
            </p>
            <p class="mt-4 text-slate-600 leading-relaxed">
                Whether you're looking to upskill your team in the latest lab techniques, attend a pharma industry conference, or request tailored biotech training, we make professional development seamless and impactful.
            </p>
        </div>
        <div class="rounded-2xl bg-gold-500/10 p-8 border border-gold-200">
            <div class="text-center">
                <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gold-500 text-white text-2xl font-bold mb-4">M</div>
                <h3 class="text-xl font-bold text-slate-900">What We Offer</h3>
                <ul class="mt-4 space-y-3 text-left text-slate-600">
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-gold-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Pharma &amp; biotech conferences and networking events
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-gold-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Hands-on laboratory training programs
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-gold-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Expert-led workshops and masterclasses
                    </li>
                    <li class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-gold-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Custom training tailored to your organization
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="text-center mb-16">
        <h2 class="text-3xl font-extrabold text-slate-900">Our Values</h2>
    </div>
    <div class="grid gap-8 sm:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gold-100 text-gold-600 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Excellence</h3>
            <p class="mt-2 text-sm text-slate-600">We deliver high-quality pharma and biotech training experiences led by industry experts.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gold-100 text-gold-600 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Community</h3>
            <p class="mt-2 text-sm text-slate-600">We foster connections between life science learners, mentors, and organizations.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-gold-100 text-gold-600 mb-4">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-900">Innovation</h3>
            <p class="mt-2 text-sm text-slate-600">We stay ahead of pharma and biotech trends to deliver relevant, cutting-edge training.</p>
        </div>
    </div>
</div>

<div class="bg-gradient-to-r from-gold-500 to-gold-600 py-16">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold text-white">Ready to Advance?</h2>
        <p class="mt-4 text-lg text-white/80">Join the pharma and biotech community and take the next step in your professional journey.</p>
        <div class="mt-8">
            <a href="{{ route('events') }}" class="inline-flex items-center rounded-xl bg-white px-8 py-3.5 text-sm font-bold text-gold-600 hover:bg-white/90 shadow-lg transition-all duration-200">
                Browse Events
            </a>
        </div>
    </div>
</div>
@endsection
