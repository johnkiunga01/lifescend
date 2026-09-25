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
            Privacy <span class="text-gold-400">Policy</span>
        </h1>
        <p class="mx-auto mt-4 max-w-2xl text-lg text-slate-300">
            Learn how we collect, use, and protect your information.
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
                            <li><a href="#privacy-section-1" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">1</span>Why We Collect</a></li>
                            <li><a href="#privacy-section-2" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">2</span>What We Collect</a></li>
                            <li><a href="#privacy-section-3" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">3</span>How We Use It</a></li>
                            <li><a href="#privacy-section-4" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">4</span>Disclosure</a></li>
                            <li><a href="#privacy-section-5" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">5</span>Data Storage</a></li>
                            <li><a href="#privacy-section-6" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">6</span>Your Rights</a></li>
                            <li><a href="#privacy-section-7" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">7</span>Cookies</a></li>
                            <li><a href="#privacy-section-8" class="toc-link flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm text-slate-600"><span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[10px] font-bold text-slate-500">8</span>Policy Changes</a></li>
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

                {{-- Card 1 --}}
                <div id="privacy-section-1" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">1</span>
                        <h2 class="text-xl font-bold text-slate-900">Why We Collect Your Information</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>Lifescend Events Ltd (&ldquo;Lifescend&rdquo;) is committed to protecting your privacy in line with UK GDPR and the Data Protection Act 2018. This policy, together with our Terms &amp; Conditions, sets out how we collect, use, and protect personal data you provide to us.</p>
                        <p>We collect information to fulfil our contractual obligations and, where you have consented, to keep you informed about relevant news, events, products, and services we believe may be of interest to you.</p>
                        <div class="rounded-xl bg-amber-50 border-l-4 border-l-gold-500 py-4 px-5 text-slate-700">
                            <strong class="text-slate-800">Your consent:</strong> By registering for a Lifescend event or entering into a booking agreement with us, you accept and consent to the practices described in this policy.
                        </div>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div id="privacy-section-2" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">2</span>
                        <h2 class="text-xl font-bold text-slate-900">Information We Collect</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p class="font-semibold text-slate-800">Information you give us</p>
                        <p>This includes information provided when you fill in forms on our website, correspond with us by phone or email, register for an event, or report a problem. It may include your name, address, email, phone number, and payment details.</p>
                        <p class="font-semibold text-slate-800">Information we collect about you</p>
                        <p>We may collect personal data in a number of ways, including when you sign a booking form, during discussions with our team, from social media channels, or through any electronic means. Where we have a legitimate interest under UK GDPR, we may continue to hold and use your data without express consent, including in the following circumstances:</p>
                        <ul class="space-y-2 pl-1">
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Performance of a contract with you, or steps taken to enter one</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Compliance with a legal obligation</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Protection of your vital interests or those of another person</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Performance of a task carried out in the public interest</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Legitimate interests pursued by Lifescend or a third party</li>
                        </ul>
                        <p class="font-semibold text-slate-800">Technical information</p>
                        <p>We may automatically collect your IP address, browser type and version, time zone, operating system, and information about your visit such as pages viewed, clickstream data, and interaction behaviour on our site.</p>
                        <p class="font-semibold text-slate-800">Information from other sources</p>
                        <p>We work with trusted third parties including business partners, analytics providers, and payment processors. Where we receive information about you from these sources, we will notify you of the purpose for which we intend to use it.</p>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div id="privacy-section-3" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">3</span>
                        <h2 class="text-xl font-bold text-slate-900">How We Use Your Information</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>We use your information to:</p>
                        <ul class="space-y-2 pl-1">
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Fulfil obligations arising from contracts between you and us, and deliver the services you have requested</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Send you information about similar events, goods, or services you may find relevant</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Notify you about changes to our services or events</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Administer our website and conduct internal analysis, testing, and research</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Keep our site and systems safe and secure</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Measure the effectiveness of our communications and marketing</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>Make suggestions and recommendations about events or services that may interest you</li>
                        </ul>
                        <div class="rounded-xl bg-emerald-50 border-l-4 border-l-emerald-500 py-4 px-5 text-slate-700">
                            <strong class="text-slate-800">Marketing opt-out:</strong> If you do not want us to use your data for marketing purposes or to pass your details to third parties, you can opt out at any time by emailing <a href="mailto:info@lifescend.events" class="text-gold-600 hover:text-gold-700 font-semibold underline">info@lifescend.events</a>.
                        </div>
                    </div>
                </div>

                {{-- Card 4 --}}
                <div id="privacy-section-4" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">4</span>
                        <h2 class="text-xl font-bold text-slate-900">Disclosure of Your Information</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>We may share your personal information with members of our group, including subsidiaries and holding companies. We will also disclose your data to third parties in the following circumstances:</p>
                        <ul class="space-y-2 pl-1">
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>In the event of a sale or acquisition of our business or assets, to the prospective buyer or seller</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>If required by law, or to enforce our Terms &amp; Conditions and protect the rights, property, or safety of Lifescend, our clients, or others</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>To selected third-party providers engaged in the marketing of our services</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span>For fraud protection and credit risk reduction purposes</li>
                        </ul>
                    </div>
                </div>

                {{-- Card 5 --}}
                <div id="privacy-section-5" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">5</span>
                        <h2 class="text-xl font-bold text-slate-900">Where We Store Your Data</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>Data we collect may be transferred to and stored at a destination outside the UK or European Economic Area (EEA), and processed by staff operating outside these regions. By providing your data, you consent to this transfer and storage.</p>
                        <p>All information is stored on secure servers. Payment transactions are encrypted. Where you hold a password to access parts of our site, please keep it confidential and do not share it.</p>
                        <div class="rounded-xl bg-amber-50 border-l-4 border-l-gold-500 py-4 px-5 text-slate-700">
                            <strong class="text-slate-800">Internet transmission:</strong> While we take all reasonable steps to protect your data, the transmission of information over the internet cannot be guaranteed to be completely secure. Any transmission is at your own risk. We will retain your personal data only for as long as necessary to fulfil our statutory and contractual obligations.
                        </div>
                    </div>
                </div>

                {{-- Card 6 --}}
                <div id="privacy-section-6" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">6</span>
                        <h2 class="text-xl font-bold text-slate-900">Your Rights</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>Under UK GDPR you have the following rights regarding your personal data:</p>
                        <ul class="space-y-2 pl-1">
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span><strong>Right of access</strong> &mdash; Request a copy of the personal data we hold about you at any time.</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span><strong>Right to erasure</strong> &mdash; Request the removal of your personal information from our records.</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span><strong>Right to withdraw consent</strong> &mdash; Withdraw your consent to processing at any time, including for marketing.</li>
                            <li class="flex items-start gap-3"><span class="mt-1.5 h-1.5 w-1.5 shrink-0 rounded-full bg-gold-500"></span><strong>Right to object</strong> &mdash; Object to processing of your data for marketing or profiling purposes.</li>
                        </ul>
                        <a href="mailto:info@lifescend.events" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white hover:bg-gold-600 transition-colors shadow-md shadow-gold-500/20">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            info@lifescend.events
                        </a>
                    </div>
                </div>

                {{-- Card 7 --}}
                <div id="privacy-section-7" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">7</span>
                        <h2 class="text-xl font-bold text-slate-900">Cookies</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>Our website uses cookies to distinguish you from other users, provide a better browsing experience, and help us improve our site. You can control cookie preferences through your browser settings.</p>
                        <p>Our site may contain links to third-party websites. These sites have their own privacy policies and we accept no responsibility for them. Please review their policies before submitting any personal data.</p>
                    </div>
                </div>

                {{-- Card 8 --}}
                <div id="privacy-section-8" class="section-card rounded-3xl border border-slate-200 bg-white p-8 sm:p-10 mb-6">
                    <div class="flex items-center gap-4 mb-6">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gold-500 text-sm font-bold text-white shadow-md">8</span>
                        <h2 class="text-xl font-bold text-slate-900">Changes to This Policy</h2>
                    </div>
                    <div class="space-y-4 text-[15px] text-slate-600 leading-relaxed">
                        <p>We may update this privacy policy from time to time. Any changes will be posted on this page, and where appropriate we will notify you by email. Please check back regularly to stay informed.</p>
                        <p>This policy applies to all personal data collected through lifescend.events. For any questions, requests, or concerns, contact us at <a href="mailto:info@lifescend.events" class="text-gold-600 hover:text-gold-700 font-semibold underline">info@lifescend.events</a>. Lifescend Events Ltd is registered in England and Wales.</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center">
                    <p class="text-xs text-slate-400">Lifescend Events Ltd &middot; United Kingdom &middot; Effective: June 2026</p>
                </div>

            @else
                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-8 text-center shadow-sm">
                    <p class="text-slate-500">Privacy Policy content has not been published yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
