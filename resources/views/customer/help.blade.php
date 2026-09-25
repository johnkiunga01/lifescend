@extends('layouts.dashboard')

@section('pageTitle', 'Help & Support')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-900">Help & Support</h1>
    <p class="text-sm text-slate-500 mt-1">Find answers to common questions and how to reach us.</p>
</div>

<div class="grid gap-6 lg:grid-cols-2 mb-8">
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center mb-4">
            <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>
        </div>
        <h3 class="text-sm font-bold text-slate-900 mb-2">Frequently Asked Questions</h3>
        <div class="space-y-3">
            <details class="group rounded-xl border border-slate-200 overflow-hidden">
                <summary class="flex items-center justify-between p-4 text-sm font-semibold text-slate-900 cursor-pointer hover:bg-slate-50 transition-colors">How do I register for an event or training?<svg class="h-4 w-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></summary>
                <div class="p-4 pt-0 text-sm text-slate-600 leading-relaxed border-t border-slate-100">Browse our events page to explore available trainings, workshops, and conferences. Select a program that interests you, choose your preferred ticket type and quantity, then proceed to checkout. You will be prompted to provide attendee details and complete your purchase using a credit or debit card. Once your payment is confirmed, your tickets will be instantly issued and available in your dashboard under "My Event Passes".</div>
            </details>
            <details class="group rounded-xl border border-slate-200 overflow-hidden">
                <summary class="flex items-center justify-between p-4 text-sm font-semibold text-slate-900 cursor-pointer hover:bg-slate-50 transition-colors">What payment methods do you accept?<svg class="h-4 w-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></summary>
                <div class="p-4 pt-0 text-sm text-slate-600 leading-relaxed border-t border-slate-100">We accept all major credit and debit cards, including Visa, Mastercard, and American Express. All payments are processed securely through our payment gateway, ensuring your financial information remains protected at all times. Your payment is processed immediately upon submission, and you will receive a confirmation receipt via email.</div>
            </details>
            <details class="group rounded-xl border border-slate-200 overflow-hidden">
                <summary class="flex items-center justify-between p-4 text-sm font-semibold text-slate-900 cursor-pointer hover:bg-slate-50 transition-colors">How do I request a custom training program?<svg class="h-4 w-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></summary>
                <div class="p-4 pt-0 text-sm text-slate-600 leading-relaxed border-t border-slate-100">If you cannot find a training program that meets your specific needs, you can submit a custom training request. Click the "Request Training" button on your dashboard or the homepage, and fill out the form with details about your organization, preferred topics, number of attendees, and ideal timeline. Our team of pharma and biotech training specialists will review your submission and work with you to design a tailored program that aligns with your learning objectives.</div>
            </details>
            <details class="group rounded-xl border border-slate-200 overflow-hidden">
                <summary class="flex items-center justify-between p-4 text-sm font-semibold text-slate-900 cursor-pointer hover:bg-slate-50 transition-colors">How do I access my purchased tickets?<svg class="h-4 w-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></summary>
                <div class="p-4 pt-0 text-sm text-slate-600 leading-relaxed border-t border-slate-100">All your purchased tickets are accessible from your account dashboard under "My Event Passes". Each ticket includes a unique QR code that will be scanned at the event for check-in. You can view your ticket details, download them, or forward them to attendees at any time. If you purchased tickets for multiple people, each attendee's ticket will be listed individually with their assigned details.</div>
            </details>
            <details class="group rounded-xl border border-slate-200 overflow-hidden">
                <summary class="flex items-center justify-between p-4 text-sm font-semibold text-slate-900 cursor-pointer hover:bg-slate-50 transition-colors">Can I get a refund or transfer my ticket?<svg class="h-4 w-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></summary>
                <div class="p-4 pt-0 text-sm text-slate-600 leading-relaxed border-t border-slate-100">Refund and transfer policies vary by event and are specified on each event's details page. Generally, cancellations made at least seven days before the event date are eligible for a full refund. For cancellations made within seven days, a partial refund or credit toward a future event may be offered. To request a refund or transfer, please contact our support team through the "Send Us a Message" form or email us directly with your order details.</div>
            </details>
            <details class="group rounded-xl border border-slate-200 overflow-hidden">
                <summary class="flex items-center justify-between p-4 text-sm font-semibold text-slate-900 cursor-pointer hover:bg-slate-50 transition-colors">How does event check-in work?<svg class="h-4 w-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></summary>
                <div class="p-4 pt-0 text-sm text-slate-600 leading-relaxed border-t border-slate-100">On the day of the event, simply present your QR code — either printed or on your mobile device — at the registration desk. Our staff will scan your code to verify your registration and grant you entry. Each ticket can only be scanned once to prevent duplicate entry. If you are attending on behalf of someone else, please ensure you have the correct QR code assigned to you.</div>
            </details>
            <details class="group rounded-xl border border-slate-200 overflow-hidden">
                <summary class="flex items-center justify-between p-4 text-sm font-semibold text-slate-900 cursor-pointer hover:bg-slate-50 transition-colors">How do I update my account or profile information?<svg class="h-4 w-4 text-slate-400 group-open:rotate-180 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></summary>
                <div class="p-4 pt-0 text-sm text-slate-600 leading-relaxed border-t border-slate-100">You can update your profile information at any time by navigating to your Account Settings from the dashboard. From there, you can modify your name, email address, password, and notification preferences. For security reasons, changes to your email address may require verification before taking effect. If you encounter any issues updating your information, please reach out to our support team for assistance.</div>
            </details>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
        <div class="h-10 w-10 rounded-xl bg-gold-100 flex items-center justify-center mb-4">
            <svg class="h-5 w-5 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
        </div>
        <h3 class="text-sm font-bold text-slate-900 mb-2">Contact Us</h3>
        <p class="text-sm text-slate-500 mb-5">Need personalized help? Reach out to our support team.</p>
        <div class="space-y-4">
            @if($organization->email ?? false)
            <div class="flex items-start gap-3 text-sm text-slate-600">
                <svg class="h-5 w-5 text-gold-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                <span class="font-semibold">{{ $organization->email }}</span>
            </div>
            @endif
            @if($organization->phone ?? false)
            <div class="flex items-start gap-3 text-sm text-slate-600">
                <svg class="h-5 w-5 text-gold-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                <span>{{ $organization->phone }}</span>
            </div>
            @endif
            <div class="flex items-start gap-3 text-sm text-slate-600">
                <svg class="h-5 w-5 text-gold-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>Mon - Fri: 8:00 AM - 5:00 PM</span>
            </div>
        </div>
        <div class="mt-6 pt-5 border-t border-slate-100">
            <a href="{{ route('contact') }}" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all">
                Send Us a Message
            </a>
        </div>
    </div>
</div>
@endsection
