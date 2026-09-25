@extends('layouts.app')

@section('content')
{{-- Hero Section --}}
<div class="relative overflow-hidden bg-gradient-to-br from-amber-50 via-white via-[85%] to-gold-100 border-b border-slate-200">
    {{-- Abstract pharma & biotech pattern overlay --}}
    <div class="absolute inset-0 opacity-[0.015]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'80\' height=\'80\' viewBox=\'0 0 80 80\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23D4AF37\' fill-opacity=\'1\'%3E%3Ccircle cx=\'40\' cy=\'40\' r=\'3\'/%3E%3Ccircle cx=\'20\' cy=\'20\' r=\'2\'/%3E%3Ccircle cx=\'60\' cy=\'20\' r=\'2\'/%3E%3Ccircle cx=\'20\' cy=\'60\' r=\'2\'/%3E%3Ccircle cx=\'60\' cy=\'60\' r=\'2\'/%3E%3C/g%3E%3Cpath d=\'M40 10L45 20 40 30 35 20zM40 50L45 60 40 70 35 60z\' fill=\'%23D4AF37\' opacity=\'0.3\'/%3E%3C/svg%3E');"></div>

    {{-- Radial gradient overlay for depth --}}
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-gold-200/20 via-transparent to-transparent"></div>

    {{-- Hero background image --}}
    <img src="{{ asset('images/hero-bg.png') }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-[0.08]" aria-hidden="true">
    <div class="absolute inset-0 bg-gradient-to-r from-gold-200/5 via-amber-100/5 to-gold-200/10"></div>

    {{-- Animated background shapes --}}
    <div class="absolute inset-0 overflow-hidden">
        <div class="floating-shape absolute -top-24 -right-24 h-96 w-96 rounded-full bg-gradient-to-br from-gold-300/15 to-amber-300/15 blur-3xl animate-float-slow"></div>
        <div class="floating-shape absolute -bottom-24 -left-24 h-[30rem] w-[30rem] rounded-full bg-gradient-to-tr from-amber-200/20 to-gold-300/15 blur-3xl animate-float-slower"></div>
        <div class="floating-shape absolute top-1/4 right-1/4 h-56 w-56 rounded-full bg-gradient-to-bl from-gold-400/10 to-amber-200/10 blur-3xl animate-float"></div>
        <div class="floating-shape absolute top-1/3 left-1/4 h-40 w-40 rounded-full bg-amber-300/15 blur-3xl animate-float-slow" style="animation-delay: -3s;"></div>
        <div class="floating-shape absolute bottom-1/4 right-1/3 h-36 w-36 rounded-full bg-gold-400/10 blur-3xl animate-float" style="animation-delay: -5s;"></div>
        <div class="floating-shape absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-64 w-64 rounded-full bg-gradient-to-r from-gold-200/10 to-amber-200/10 blur-3xl animate-float-slower" style="animation-delay: -7s;"></div>
    </div>

    {{-- Subtle dot grid --}}
    <div class="absolute inset-0 opacity-[0.02]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Ccircle cx=\'20\' cy=\'20\' r=\'1\' fill=\'%23000\'/%3E%3C/svg%3E');"></div>

    {{-- Content --}}
    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 sm:py-20">
        <div class="text-center max-w-4xl mx-auto">
            {{-- Badge --}}
            <div class="inline-flex items-center gap-2 rounded-full bg-gold-500/10 px-4 py-1.5 ring-1 ring-inset ring-gold-500/20 mb-5">
                <span class="h-2 w-2 rounded-full bg-gold-500 animate-pulse"></span>
                <span class="text-sm font-semibold text-gold-700">Specialized Pharma &amp; Biotech Training Platform</span>
            </div>

            {{-- Heading --}}
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-6xl leading-[1.1]">
                <span class="text-slate-800">Advance Your Career in</span><br>
                <span class="text-gold-600">Pharmaceutical &amp; Life Sciences</span>
            </h1>

            {{-- Description --}}
            <p class="mx-auto mt-4 max-w-2xl text-base text-slate-600 leading-relaxed">
                Explore hands-on trainings, workshops, and conferences in pharma, biotech, molecular biology, bioinformatics, and laboratory sciences. Learn from industry experts and gain practical skills for the modern lab.
            </p>

            {{-- CTAs --}}
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#events" class="group inline-flex items-center gap-2 rounded-xl bg-gold-500 px-7 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/30 hover:bg-gold-600 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200">
                    Browse Events
                    <svg class="h-4 w-4 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-gold-400 bg-white px-7 py-3 text-sm font-bold text-gold-600 hover:bg-gold-50 hover:border-gold-500 transition-all duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    Get Started
                </a>
            </div>

            {{-- Trust indicators --}}
            <div class="mt-10 flex flex-wrap items-center justify-center gap-6 text-sm text-slate-500">
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">Pharma &amp; Biotech Experts</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">Hands-On Lab Training</span>
                </div>
                <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium">LinkedIn Verified</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom fade to white --}}
    <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-white to-transparent"></div>
</div>

<div class="bg-white py-12 sm:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-flex items-center rounded-full bg-gold-100 px-4 py-1.5 text-xs font-semibold text-gold-700 ring-1 ring-inset ring-gold-200 mb-4">Simple Process</span>
            <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">How It <span class="text-gold-500">Works</span></h2>
            <p class="mt-4 text-slate-600 max-w-xl mx-auto">Three easy steps to find and register for the perfect pharma or biotech training program.</p>
        </div>

        <div class="grid gap-6 sm:grid-cols-3">
            <div class="relative text-center group">
                <div class="mx-auto h-12 w-12 rounded-xl bg-gold-100 flex items-center justify-center mb-3 group-hover:bg-gold-500 transition-colors duration-300">
                    <span class="text-lg font-extrabold text-gold-600 group-hover:text-white transition-colors duration-300">1</span>
                </div>
                <div class="hidden sm:block absolute top-6 left-[60%] w-[calc(80%)] h-0.5 border-t-2 border-dashed border-slate-200"></div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Browse Trainings</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Explore our catalog of pharma and biotech trainings, workshops, and lab courses tailored for life science professionals.</p>
            </div>
            <div class="relative text-center group">
                <div class="mx-auto h-12 w-12 rounded-xl bg-gold-100 flex items-center justify-center mb-3 group-hover:bg-gold-500 transition-colors duration-300">
                    <span class="text-lg font-extrabold text-gold-600 group-hover:text-white transition-colors duration-300">2</span>
                </div>
                <div class="hidden sm:block absolute top-6 left-[60%] w-[calc(80%)] h-0.5 border-t-2 border-dashed border-slate-200"></div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Register &amp; Pay</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Secure your spot with easy registration and flexible payment options.</p>
            </div>
            <div class="relative text-center group">
                <div class="mx-auto h-12 w-12 rounded-xl bg-gold-100 flex items-center justify-center mb-3 group-hover:bg-gold-500 transition-colors duration-300">
                    <span class="text-lg font-extrabold text-gold-600 group-hover:text-white transition-colors duration-300">3</span>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Train &amp; Advance</h3>
                <p class="text-sm text-slate-500 leading-relaxed">Attend expert-led sessions, gain practical lab skills, and earn your LinkedIn-recognized certificate plus digital credentials.</p>
            </div>
        </div>
    </div>
</div>

{{-- Events/Trainings Section --}}
<div id="events" class="bg-slate-50 py-20 sm:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-12 gap-4">
            <div>
                <span class="inline-flex items-center rounded-full bg-gold-100 px-4 py-1.5 text-xs font-semibold text-gold-700 ring-1 ring-inset ring-gold-200 mb-4">Discover Opportunities</span>
                <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">Pharma &amp; Biotech <span class="text-gold-500">Events &amp; Trainings</span></h2>
                <p class="mt-3 text-slate-600 max-w-xl">Browse our lineup of pharmaceutical and biotechnology training programs, workshops, and conferences.</p>
            </div>
            <a href="{{ route('events') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gold-600 hover:text-gold-700 transition-colors shrink-0">
                View All Events
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>

        @if($events->count() > 0)
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($events as $event)
            <a href="{{ route('events.show', $event->slug) }}" class="group block rounded-2xl border border-slate-200 bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="aspect-[16/9] overflow-hidden relative bg-slate-100">
                    <img src="{{ $event->cover_url }}" alt="{{ $event->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
                    <div class="absolute bottom-3 left-3">
                        <span class="inline-flex items-center rounded-full bg-white/20 backdrop-blur-sm px-3 py-1 text-[10px] font-bold text-white border border-white/20">{{ ucfirst($event->event_type) }}</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-3">
                        @if(!$event->cover_url)
                        <span class="inline-flex items-center rounded-full bg-gold-100 px-3 py-1 text-xs font-semibold text-gold-700">{{ ucfirst($event->event_type) }}</span>
                        @endif
                        <span class="text-xs text-slate-400 flex items-center gap-1">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $event->start_date->format('M d, Y') }}
                        </span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 group-hover:text-gold-600 transition-colors mb-2 line-clamp-1">{{ $event->name }}</h3>
                    <p class="text-sm text-slate-600 line-clamp-2 mb-4 leading-relaxed">{{ Str::limit(strip_tags($event->description), 140) }}</p>
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <div class="flex items-center text-xs text-slate-500 gap-3">
                            <span class="flex items-center gap-1">
                                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $event->venue->name ?? 'Online' }}
                            </span>
                        </div>
                        <span class="text-xs font-semibold text-gold-600 group-hover:translate-x-0.5 transition-transform inline-flex items-center gap-1">
                            Learn More
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-20 bg-white rounded-2xl border border-slate-200">
            <svg class="h-16 w-16 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <h3 class="text-xl font-semibold text-slate-500 mb-2">No upcoming trainings yet</h3>
            <p class="text-slate-400">Check back soon for new pharma and biotech training programs.</p>
        </div>
        @endif
    </div>
</div>

{{-- Stats Section --}}
<div class="relative overflow-hidden bg-gradient-to-r from-gold-500 to-gold-600 py-20">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 gap-8 md:grid-cols-4">
            <div class="text-center">
                <div class="inline-flex h-12 w-12 rounded-xl bg-black/10 items-center justify-center mb-4 backdrop-blur-sm">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-4xl font-extrabold text-white">{{ $events->count() }}</p>
                <p class="mt-2 text-sm font-medium text-white/80">Active Trainings</p>
            </div>
            <div class="text-center">
                <div class="inline-flex h-12 w-12 rounded-xl bg-black/10 items-center justify-center mb-4 backdrop-blur-sm">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <p class="text-4xl font-extrabold text-white">20+</p>
                <p class="mt-2 text-sm font-medium text-white/80">Pharma &amp; Biotech Experts</p>
            </div>
            <div class="text-center">
                <div class="inline-flex h-12 w-12 rounded-xl bg-black/10 items-center justify-center mb-4 backdrop-blur-sm">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <p class="text-4xl font-extrabold text-white">500+</p>
                <p class="mt-2 text-sm font-medium text-white/80">Scientists Trained</p>
            </div>
            <div class="text-center">
                <div class="inline-flex h-12 w-12 rounded-xl bg-black/10 items-center justify-center mb-4 backdrop-blur-sm">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <p class="text-4xl font-extrabold text-white">10+</p>
                <p class="mt-2 text-sm font-medium text-white/80">Training Programs</p>
            </div>
        </div>
    </div>
</div>

{{-- Features Section --}}
<div class="bg-slate-50 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="inline-flex items-center rounded-full bg-gold-100 px-4 py-1.5 text-xs font-semibold text-gold-700 ring-1 ring-inset ring-gold-200 mb-3">Why Choose Us</span>
            <h2 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">Built for <span class="text-gold-500">Pharma &amp; Biotech Professionals</span></h2>
            <p class="mt-3 text-slate-600 max-w-xl mx-auto">Everything you need to advance your pharma or biotech career in one place.</p>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="h-10 w-10 rounded-lg bg-gold-100 flex items-center justify-center mb-4">
                    <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1.5">Lab-Focused Training</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Hands-on molecular biology, cell culture, PCR, and sequencing techniques for real-world lab application.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="h-10 w-10 rounded-lg bg-gold-100 flex items-center justify-center mb-4">
                    <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1.5">Lab Sites</h3>
                <p class="text-xs text-slate-500 leading-relaxed">State-of-the-art laboratory venues equipped with modern pharma and biotech instrumentation and safety facilities.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="h-10 w-10 rounded-lg bg-gold-100 flex items-center justify-center mb-4">
                    <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1.5">LinkedIn-Recognized Certificates</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Earn LinkedIn-recognized certificates, shareable digital badges, and professional development credits that boost your pharma and biotech credentials.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="h-10 w-10 rounded-lg bg-gold-100 flex items-center justify-center mb-4">
                    <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.479m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1.5">Industry Experts</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Learn from PhD-level scientists and pharma and biotech professionals with extensive research and industry experience.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="h-10 w-10 rounded-lg bg-gold-100 flex items-center justify-center mb-4">
                    <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1.5">Flexible Formats</h3>
                <p class="text-xs text-slate-500 leading-relaxed">In-person lab sessions, virtual workshops, and hybrid options designed for busy life science professionals.</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                <div class="h-10 w-10 rounded-lg bg-gold-100 flex items-center justify-center mb-4">
                    <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900 mb-1.5">Custom Programs</h3>
                <p class="text-xs text-slate-500 leading-relaxed">Tailored pharma and biotech training designed for your team's specific research focus and laboratory requirements.</p>
            </div>
        </div>
    </div>
</div>

{{-- Testimonials Section --}}
@if($testimonials->isNotEmpty())
<div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-24 sm:py-32">
    <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23D4AF37\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    <div class="absolute top-1/4 -left-40 h-80 w-80 rounded-full bg-gold-500/5 blur-3xl"></div>
    <div class="absolute bottom-1/4 -right-40 h-80 w-80 rounded-full bg-gold-500/5 blur-3xl"></div>

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 relative">
        <div class="text-center mb-14">
            <span class="inline-flex items-center gap-2 rounded-full bg-gold-500/10 px-4 py-1.5 text-xs font-semibold text-gold-400 ring-1 ring-inset ring-gold-500/20 mb-4">
                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                Testimonials
            </span>
            <h2 class="text-3xl font-extrabold text-white sm:text-4xl">What Our <span class="text-gold-400">Community Says</span></h2>
            <p class="mt-3 text-slate-400 max-w-xl mx-auto">Hear from professionals who have transformed their careers through our programs.</p>
        </div>

        <div class="relative" x-data="{ current: 0, total: {{ $testimonials->count() }}, autoplay: null }" x-init="autoplay = setInterval(() => { current = (current + 1) % total }, 5000)" @@mouseenter="clearInterval(autoplay)" @@mouseleave="autoplay = setInterval(() => { current = (current + 1) % total }, 5000)">
            <div class="overflow-hidden rounded-2xl bg-white/5 backdrop-blur-sm border border-white/10">
                <div class="flex transition-transform duration-500 ease-in-out" :style="'transform: translateX(-' + (current * 100) + '%)'">
                    @foreach($testimonials as $t)
                    <div class="min-w-full px-6 sm:px-16 lg:px-24 py-16 sm:py-20">
                        <div class="mx-auto max-w-3xl text-center">
                            <svg class="h-10 w-10 mx-auto text-gold-500/40 mb-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" /></svg>
                            <blockquote class="text-lg sm:text-xl text-slate-200 leading-relaxed font-light">&ldquo;{{ $t->content }}&rdquo;</blockquote>
                            <div class="mt-8 flex items-center justify-center gap-1 mb-6">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="h-5 w-5 {{ $i <= $t->rating ? 'text-gold-400' : 'text-white/10' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                            <div class="flex items-center justify-center gap-3">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-gold-400 to-gold-600 flex items-center justify-center text-sm font-bold text-white shadow-lg">
                                    {{ strtoupper(substr($t->user->name, 0, 1)) }}
                                </div>
                                <div class="text-left">
                                    <p class="text-sm font-bold text-white">{{ $t->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $t->created_at->format('M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-center gap-2 mt-8">
                @foreach($testimonials as $i => $t)
                <button @@click="current = {{ $i }}" class="h-2 rounded-full transition-all duration-300 cursor-pointer" :class="current === {{ $i }} ? 'w-8 bg-gold-500' : 'w-2 bg-white/20 hover:bg-white/40'"></button>
                @endforeach
            </div>

            <button @@click="current = (current - 1 + total) % total" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 sm:-translate-x-6 h-10 w-10 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/20 transition-all cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button @@click="current = (current + 1) % total" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 sm:translate-x-6 h-10 w-10 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 flex items-center justify-center text-white/70 hover:text-white hover:bg-white/20 transition-all cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>
</div>
@endif

{{-- CTA Section --}}
<div class="bg-gradient-to-br from-gold-500 to-gold-600 py-20 relative overflow-hidden">
    <div class="absolute inset-0 opacity-[0.04]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23000000\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-white/5 blur-3xl"></div>
    <div class="relative mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
        <span class="inline-flex items-center rounded-full bg-white/20 px-4 py-1.5 text-xs font-semibold text-white backdrop-blur-sm mb-6">Get Started Today</span>
        <h2 class="text-3xl font-extrabold text-white sm:text-4xl lg:text-5xl leading-tight">
            Looking for a Specific<br>
            <span class="text-yellow-200">Pharma or Biotech Training?</span>
        </h2>
        <p class="mt-4 text-lg text-white/80 max-w-xl mx-auto">
            Can't find what you need? Submit a training request and our pharma and biotech experts will design a program tailored to your laboratory or organization.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
            <button @@click="trainingModal = true" class="inline-flex items-center gap-2 rounded-xl bg-white px-8 py-3.5 text-sm font-bold text-gold-700 shadow-lg hover:bg-gold-50 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Request Training
            </button>
            <a href="{{ route('events') }}" class="inline-flex items-center gap-2 rounded-xl border-2 border-white/30 px-8 py-3.5 text-sm font-bold text-white hover:bg-white/10 hover:border-white/50 transition-all duration-200">
                Browse All Events
            </a>
        </div>
    </div>
</div>
@endsection