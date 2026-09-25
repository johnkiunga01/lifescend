@extends('layouts.app')

@push('styles')
<style>
    html { scroll-behavior: smooth; }
    .section-card { transition: box-shadow .2s, transform .2s; }
    .section-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.06); transform: translateY(-1px); }
    .toc-link { transition: all .15s; }
    .toc-link:hover { background: #fefce8; color: #a16207; }
</style>
@endpush

@section('content')
{{-- Hero --}}
<div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 py-20">
    <div class="absolute inset-0 opacity-[0.15]">
        <div class="absolute -top-20 -right-20 h-80 w-80 rounded-full bg-gold-500 blur-3xl"></div>
        <div class="absolute -bottom-20 -left-20 h-96 w-96 rounded-full bg-gold-400 blur-3xl"></div>
    </div>
    <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
        <span class="inline-flex items-center rounded-full bg-white/10 px-4 py-1.5 text-sm font-semibold text-gold-300 ring-1 ring-inset ring-white/20 backdrop-blur-sm mb-5">
            Legal
        </span>
        <h1 class="text-4xl font-extrabold tracking-tight text-white sm:text-5xl lg:text-6xl">
            Terms &amp; <span class="text-gold-400">Conditions</span>
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-300">
            Please read these terms carefully before using our services.
        </p>
    </div>
</div>

{{-- Main --}}
<div class="w-full px-6 py-12 sm:px-10 lg:px-16">
    <div class="lg:grid lg:grid-cols-[240px_1fr] lg:gap-12">

        {{-- ToC --}}
        <div class="mb-8 lg:mb-0" x-data="{ open: false }">
            <div class="lg:sticky lg:top-24">
                <button @@click="open = !open" class="flex w-full items-center justify-between rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm lg:hidden">
                    On this page
                    <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                </button>
                <nav class="mt-2 lg:mt-0" :class="open ? 'block' : 'hidden lg:block'">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <h3 class="mb-3 text-[10px] font-bold uppercase tracking-[.15em] text-slate-400">On this page</h3>
                        <ul class="space-y-1">
                            <li><a href="#section-1" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">1</span>Payment Terms</a></li>
                            <li><a href="#section-2" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">2</span>Cancellation &amp; Refunds</a></li>
                            <li><a href="#section-3" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">3</span>Our Cancellations</a></li>
                            <li><a href="#section-4" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">4</span>Force Majeure</a></li>
                            <li><a href="#section-5" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">5</span>Governing Law</a></li>
                            <li><a href="#section-6" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">6</span>Participant Details</a></li>
                        </ul>
                    </div>
                    <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm text-center">
                        <p class="text-[10px] font-bold uppercase tracking-[.15em] text-slate-400 mb-1">Effective</p>
                        <p class="text-sm font-semibold text-slate-700">June 2026</p>
                    </div>
                </nav>
            </div>
        </div>

        {{-- Content --}}
        <div>
            @if($content)
                @php
                    $sections = [
                        1 => ['title' => 'Payment Terms', 'id' => 'section-1'],
                        2 => ['title' => 'Cancellation by the Client & Refund Policy', 'id' => 'section-2'],
                        3 => ['title' => 'Cancellation or Postponement by Lifescend', 'id' => 'section-3'],
                        4 => ['title' => 'Force Majeure', 'id' => 'section-4'],
                        5 => ['title' => 'Governing Law & Disputes', 'id' => 'section-5'],
                        6 => ['title' => 'Participant Details', 'id' => 'section-6'],
                    ];
                @endphp

                {{-- Card 1: Payment Terms --}}
                <div id="section-1" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">1</span>
                        <h2 class="text-xl font-bold text-slate-900">Payment Terms</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>Full payment of the invoiced amount is due within 14 days of the invoice date.</p>
                        <p>If your organisation&rsquo;s internal policy requires a different payment schedule, you may request an exception from your account manager. All exceptions must be confirmed in writing by email before they take effect.</p>
                        <div class="rounded-xl bg-amber-50 border-l-4 border-l-gold-500 py-4 px-5 text-slate-700">
                            <strong class="text-slate-800">Please note:</strong> Lifescend Events reserves the right to refuse entry if payment has not been received before the course start date and no written exception has been agreed. The registration fee covers course materials and a certificate of attendance.
                        </div>
                    </div>
                </div>

                {{-- Card 2: Cancellation --}}
                <div id="section-2" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">2</span>
                        <h2 class="text-xl font-bold text-slate-900">Cancellation by the Client &amp; Refund Policy</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>To cancel your place, please notify us in writing by email. The following terms apply based on how far in advance you cancel:</p>
                        <div class="overflow-hidden rounded-xl border border-slate-200 shadow-sm">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-slate-50">
                                        <th class="px-5 py-3 text-left font-bold text-slate-700 border-b-2 border-gold-400">Cancellation timing</th>
                                        <th class="px-5 py-3 text-left font-bold text-slate-700 border-b-2 border-gold-400">Outcome</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-slate-100">
                                        <td class="px-5 py-3.5 text-slate-600">Less than 1 month before the event</td>
                                        <td class="px-5 py-3.5 text-slate-600">No refund issued</td>
                                    </tr>
                                    <tr>
                                        <td class="px-5 py-3.5 text-slate-600">1 month or more before the event</td>
                                        <td class="px-5 py-3.5 text-slate-600">50% of invoice charged</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p>Non-attendance does not qualify for a refund or discount &mdash; the full fee remains payable.</p>
                        <div class="rounded-xl bg-emerald-50 border-l-4 border-l-emerald-500 py-4 px-5 text-slate-700">
                            <strong class="text-slate-800">Transfer option:</strong> You may transfer your registration to another Lifescend course taking place within 12 months of the original date. Transfers are permitted once only and are subject to availability.
                        </div>
                    </div>
                </div>

                {{-- Card 3: Our Cancellations --}}
                <div id="section-3" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">3</span>
                        <h2 class="text-xl font-bold text-slate-900">Cancellation or Postponement by Lifescend</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>In the unlikely event that Lifescend must cancel or postpone a course due to exceptional circumstances, you will receive a credit to the full value of your payment.</p>
                        <p>This credit may be applied to any Lifescend course within 12 months of the original event date.</p>
                    </div>
                </div>

                {{-- Card 4: Force Majeure --}}
                <div id="section-4" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">4</span>
                        <h2 class="text-xl font-bold text-slate-900">Force Majeure</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>Neither party will be liable for costs or losses arising from circumstances beyond their reasonable control. These include but are not limited to:</p>
                        <ul class="space-y-2 pl-5">
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Acts of terrorism or war</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Industrial action or strikes</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Natural disasters</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Public health emergencies or illness</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Industry-wide disruption</li>
                        </ul>
                        <p>In such cases, Lifescend reserves the right to reschedule the event or arrange an alternative facilitator.</p>
                    </div>
                </div>

                {{-- Card 5: Governing Law --}}
                <div id="section-5" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">5</span>
                        <h2 class="text-xl font-bold text-slate-900">Governing Law &amp; Disputes</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>These terms are governed by the laws of England and Wales. Any disputes will be subject to the exclusive jurisdiction of the courts of England and Wales.</p>
                        <p>If you have a complaint or wish to raise an appeal, please contact us directly and we will work to resolve the matter promptly.</p>
                        <a href="mailto:info@lifescend.events" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white hover:bg-gold-600 transition-colors shadow-md shadow-gold-500/20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            info@lifescend.events
                        </a>
                    </div>
                </div>

                {{-- Card 6: Participant Details --}}
                <div id="section-6" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">6</span>
                        <h2 class="text-xl font-bold text-slate-900">Participant Details</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>Participant details may be updated at any time before the course begins, at no additional cost. Please contact your account manager or email us to make any amendments.</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center">
                    <p class="text-sm text-slate-500 italic">These terms apply to all registrations made through lifescend.events. By completing your registration, you confirm that you have read and accepted these terms. For any questions, contact us at <a href="mailto:info@lifescend.events" class="text-gold-600 hover:text-gold-700 font-semibold underline">info@lifescend.events</a>.</p>
                    <p class="mt-3 text-xs text-slate-400">Lifescend Events Ltd &middot; United Kingdom &middot; Effective: June 2026</p>
                </div>

            @else
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 text-center shadow-sm">
                    <p class="text-slate-500">Terms &amp; Conditions content has not been published yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
