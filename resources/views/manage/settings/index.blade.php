@extends('layouts.dashboard')

@section('pageTitle', 'Settings')

@php
    $defaultTerms = config('legal.default_terms');
    $defaultPrivacy = config('legal.default_privacy');
    $logoUrl = $org->logo ? Storage::url($org->logo) : null;
    $faviconUrl = $org->favicon ? Storage::url($org->favicon) : null;
@endphp

@section('content')
<div x-data="{ tcModal: false, privacyModal: false }">
    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <div class="h-10 w-10 rounded-xl bg-gold-500 flex items-center justify-center text-white shadow-lg shadow-gold-500/20">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900">Settings</h1>
                <p class="text-sm text-slate-500">Configure your organization settings and legal pages.</p>
            </div>
                </div>
            </div>

    @if(session('success'))<div class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-sm text-emerald-700 shadow-sm"><svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>{{ session('success') }}</div>@endif
    @if($errors->any() && !session('success'))<div class="mb-6 rounded-xl bg-red-50 border border-red-200 p-4 text-sm text-red-700 shadow-sm"><ul class="list-disc pl-5 space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

    <div class="grid gap-6 lg:grid-cols-3">

            {{-- Right sidebar / Page Status --}}
            <div class="lg:order-2 space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Page Status</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Terms &amp; Conditions</span>
                                @if(!empty($settings['terms_and_conditions']) || !empty($defaultTerms))
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-500 border border-slate-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                        Draft
                                    </span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-[10px] font-mono text-slate-400">v{{ $settings['terms_version'] ?? '—' }}</p>
                        </div>
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-600">Privacy Policy</span>
                                @if(!empty($settings['privacy_policy']) || !empty($defaultPrivacy))
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[10px] font-bold text-emerald-700 border border-emerald-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold text-slate-500 border border-slate-200">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                                        Draft
                                    </span>
                                @endif
                            </div>
                            <p class="mt-0.5 text-[10px] font-mono text-slate-400">v{{ $settings['privacy_version'] ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="mt-5 pt-4 border-t border-slate-100">
                        <a href="{{ route('terms') }}" target="_blank" class="flex items-center gap-2 text-xs text-gold-600 hover:text-gold-700 font-semibold transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            Preview Terms page
                        </a>
                        <a href="{{ route('privacy') }}" target="_blank" class="flex items-center gap-2 text-xs text-gold-600 hover:text-gold-700 font-semibold transition-colors mt-2">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            Preview Privacy page
                        </a>
                    </div>
                </div>
            </div>

            {{-- Left / Main column --}}
            <div class="lg:col-span-2 lg:order-1 space-y-6">

                {{-- Organization Info --}}
                <form action="{{ route('manage.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-white/20 flex items-center justify-center backdrop-blur-sm">
                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a3 3 0 01-3-3V7m8 4v2m-2-2v2m-4-2v2m-2-2v2" /></svg>
                            </div>
                            <div><h2 class="text-base font-bold text-white">Organization</h2><p class="text-xs text-gold-200">Your organization name, logo, and contact details</p></div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-5">
                            {{-- Logo & Favicon --}}
                            <div class="grid gap-5 sm:grid-cols-2 pb-5 border-b border-slate-100">
                                <div x-data="imageCropper({ name: 'logo', ratio: null, width: 0, height: 0, label: 'Logo' })">
                                    <div class="flex items-center gap-4">
                                        <div class="relative shrink-0">
                                            <div class="h-24 w-24 rounded-xl overflow-hidden bg-white ring-2 ring-slate-200 shadow-md">
                                                <template x-if="previewUrl">
                                                    <img :src="previewUrl" class="h-full w-full object-contain p-1">
                                                </template>
                                                <template x-if="!previewUrl">
                                                    @if($logoUrl)
                                                    <img src="{{ $logoUrl }}" alt="Logo" class="h-full w-full object-contain p-1">
                                                    @else
                                                    <div class="h-full w-full flex items-center justify-center text-slate-300 text-2xl font-extrabold">?</div>
                                                    @endif
                                                </template>
                                            </div>
                                            <button type="button" @@click="$refs.fileInput.click()" class="absolute -bottom-1 -right-1 h-7 w-7 rounded-full bg-gold-500 flex items-center justify-center shadow-lg shadow-gold-500/30 hover:bg-gold-600 transition-colors cursor-pointer border-2 border-white">
                                                <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            <input name="logo" type="file" accept="image/jpeg,image/png,image/webp" @@change="onFileChange" x-ref="fileInput" class="hidden">
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Organization Logo</p>
                                            <p class="text-xs text-slate-500 mt-0.5">Click the edit icon to crop.</p>
                                        </div>
                                    </div>
                                    @include('manage.events._cropper-modal')
                                </div>
                                <div x-data="imageCropper({ name: 'favicon', ratio: null, width: 0, height: 0, label: 'Favicon' })">
                                    <div class="flex items-center gap-4">
                                        <div class="relative shrink-0">
                                            <div class="h-20 w-20 rounded-xl overflow-hidden bg-white ring-2 ring-slate-200 shadow-md">
                                                <template x-if="previewUrl">
                                                    <img :src="previewUrl" class="h-full w-full object-contain p-1">
                                                </template>
                                                <template x-if="!previewUrl">
                                                    @if($faviconUrl)
                                                    <img src="{{ $faviconUrl }}" alt="Favicon" class="h-full w-full object-contain p-1">
                                                    @else
                                                    <div class="h-full w-full flex items-center justify-center text-slate-300 text-xs font-bold">16x16</div>
                                                    @endif
                                                </template>
                                            </div>
                                            <label for="org-favicon-input" class="absolute -bottom-1 -right-1 h-7 w-7 rounded-full bg-slate-700 flex items-center justify-center shadow-lg hover:bg-slate-600 transition-colors cursor-pointer border-2 border-white">
                                                <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </label>
                                            <input id="org-favicon-input" name="favicon" type="file" accept="image/jpeg,image/png,image/webp,image/x-icon" @@change="onFileChange" x-ref="fileInput" class="hidden">
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">Favicon</p>
                                            <p class="text-xs text-slate-500 mt-0.5">Browser tab icon. Click the edit icon to crop.</p>
                                        </div>
                                    </div>
                                    @include('manage.events._cropper-modal')
                                </div>
                            </div>

                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="org-name" class="block text-sm font-medium text-slate-700 mb-1.5">Organization Name</label>
                                    <input id="org-name" name="name" type="text" required value="{{ old('name', $org->name) }}"
                                           placeholder="e.g. Lifescend"
                                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                                </div>
                                <div>
                                    <label for="org-website" class="block text-sm font-medium text-slate-700 mb-1.5">Website</label>
                                    <input id="org-website" name="website" type="url" value="{{ old('website', $org->website) }}"
                                           placeholder="https://example.com"
                                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                                </div>
                            </div>
                            <div class="grid gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="org-email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                                    <input id="org-email" name="email" type="email" value="{{ old('email', $org->email) }}"
                                           placeholder="contact@example.com"
                                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                                </div>
                                <div>
                                    <label for="org-phone" class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                                    <input id="org-phone" name="phone" type="text" value="{{ old('phone', $org->phone) }}"
                                           placeholder="+1 234 567 890"
                                           class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                                 </div>
                            </div>
                            <div>
                                <label for="org-timezone" class="block text-sm font-medium text-slate-700 mb-1.5">Default Timezone</label>
                                <select id="org-timezone" name="timezone" class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                                    @foreach($timezones as $tz)
                                    <option value="{{ $tz }}" {{ old('timezone', $org->timezone) === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1.5 text-xs text-slate-500">Used as the default timezone across the system (e.g. new events) unless overridden.</p>
                            </div>

                            <div>
                                <label for="org-notification-email" class="block text-sm font-medium text-slate-700 mb-1.5">Notification Email</label>
                                <input id="org-notification-email" name="notification_email" type="email" value="{{ old('notification_email', $settings['notification_email'] ?? '') }}"
                                       placeholder="notifications@example.com"
                                       class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                                <p class="mt-1.5 text-xs text-slate-500">Notifications for new ticket purchases, enquiries, contact messages, testimonials, training requests, and account support requests will be sent to this email.</p>
                            </div>

                            <div class="pt-4">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="h-px flex-1 bg-slate-200"></div>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Contact &amp; Location Details</span>
                                    <div class="h-px flex-1 bg-slate-200"></div>
                                </div>
                                <div>
                                    <label for="org-address" class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                                    <textarea id="org-address" name="address" rows="3" placeholder="Street address, building, suite, etc."
                                        class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow resize-none">{{ old('address', $org->address) }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-slate-100 px-6 py-4 flex justify-end">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Save Organization
                        </button>
                    </div>
                </div>
                {{-- Cover Image Dimensions --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                        <div class="h-9 w-9 rounded-xl bg-amber-500 flex items-center justify-center text-white"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg></div>
                        <div><h2 class="text-lg font-bold text-slate-900">Cover Image Dimensions</h2><p class="text-xs text-slate-500 mt-0.5">Default crop size for event cover images.</p></div>
                    </div>
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Width (px)</label>
                            <input name="cover_image_width" type="number" min="1600" max="1600" value="{{ $settings['cover_image_width'] ?? 1600 }}"
                                   placeholder="1600"
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Height (px)</label>
                            <input name="cover_image_height" type="number" min="900" max="900" value="{{ $settings['cover_image_height'] ?? 900 }}"
                                   placeholder="900"
                                   class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:ring-1 focus:ring-gold-500 focus:outline-none sm:text-sm transition-shadow">
                        </div>
                    </div>
                    <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3 text-xs text-slate-500">
                        <p class="font-semibold text-slate-700">Current: <span class="text-slate-900">{{ $settings['cover_image_width'] ?? 1600 }} &times; {{ $settings['cover_image_height'] ?? 900 }} px</span> ({{ round(($settings['cover_image_width'] ?? 1600) / ($settings['cover_image_height'] ?? 900), 2) }}:1 aspect ratio)</p>
                        <p class="mt-1">Cover images are cropped at a fixed 1600 &times; 900 px size (16:9).</p>
                    </div>
                </div>

                {{-- Terms & Conditions Card --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-indigo-500 flex items-center justify-center text-white shrink-0"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg></div>
                            <div><h2 class="text-lg font-bold text-slate-900">Terms &amp; Conditions</h2><p class="text-xs text-slate-500 mt-0.5">Public page content at /terms-and-conditions</p></div>
                        </div>
                        <button type="button" @@click="tcModal = true" class="inline-flex items-center gap-1.5 rounded-xl bg-gold-500 px-4 py-2 text-xs font-bold text-white hover:bg-gold-600 transition-all shadow-sm cursor-pointer">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Edit Content
                        </button>
                    </div>
                    <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                        @php
                            $tcPreview = $settings['terms_and_conditions'] ?? $defaultTerms;
                            $tcWords = strip_tags($tcPreview);
                            $tcLen = mb_strlen($tcWords);
                            preg_match_all('/<h2[^>]*>([^<]+)<\/h2>/i', $tcPreview, $tcHeadings);
                        @endphp
                        @if($tcPreview)
                            <div class="text-xs text-slate-600 leading-relaxed space-y-1">
                                @if(!empty($tcHeadings[1]))
                                    <ol class="list-decimal list-inside space-y-0.5">
                                        @foreach($tcHeadings[1] as $heading)
                                            <li>{{ strip_tags($heading) }}</li>
                                        @endforeach
                                    </ol>
                                @else
                                    <p>{{ Str::limit($tcWords, 200) }}</p>
                                @endif
                            </div>
                            <p class="mt-2 text-[10px] font-medium text-slate-400 uppercase tracking-wider">v{{ $settings['terms_version'] ?? '—' }} &middot; ~{{ $tcLen }} characters</p>
                        @else
                            <p class="text-xs text-slate-400 italic">No content yet. Click "Edit Content" to add Terms &amp; Conditions.</p>
                        @endif
                    </div>
                </div>

                {{-- Privacy Policy Card --}}
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-emerald-500 flex items-center justify-center text-white shrink-0"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg></div>
                            <div><h2 class="text-lg font-bold text-slate-900">Privacy Policy</h2><p class="text-xs text-slate-500 mt-0.5">Public page content at /privacy-policy</p></div>
                        </div>
                        <button type="button" @@click="privacyModal = true" class="inline-flex items-center gap-1.5 rounded-xl bg-gold-500 px-4 py-2 text-xs font-bold text-white hover:bg-gold-600 transition-all shadow-sm cursor-pointer">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            Edit Content
                        </button>
                    </div>
                    <div class="mt-4 rounded-xl bg-slate-50 border border-slate-200 px-4 py-3">
                        @php
                            $ppPreview = $settings['privacy_policy'] ?? $defaultPrivacy;
                            $ppWords = strip_tags($ppPreview);
                            $ppLen = mb_strlen($ppWords);
                            preg_match_all('/<h2[^>]*>([^<]+)<\/h2>/i', $ppPreview, $ppHeadings);
                        @endphp
                        @if($ppPreview)
                            <div class="text-xs text-slate-600 leading-relaxed space-y-1">
                                @if(!empty($ppHeadings[1]))
                                    <ol class="list-decimal list-inside space-y-0.5">
                                        @foreach($ppHeadings[1] as $heading)
                                            <li>{{ strip_tags($heading) }}</li>
                                        @endforeach
                                    </ol>
                                @else
                                    <p>{{ Str::limit($ppWords, 200) }}</p>
                                @endif
                            </div>
                            <p class="mt-2 text-[10px] font-medium text-slate-400 uppercase tracking-wider">v{{ $settings['privacy_version'] ?? '—' }} &middot; ~{{ $ppLen }} characters</p>
                        @else
                            <p class="text-xs text-slate-400 italic">No content yet. Click "Edit Content" to add Privacy Policy.</p>
                        @endif
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-8 py-3 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        Save Settings
                    </button>
                </div>
            </div>

        </div>

        {{-- Terms & Conditions Modal --}}
        <div x-show="tcModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="tcModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="tcModal = false" class="fixed inset-0 bg-black/60"></div>
                <div x-show="tcModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
                     class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
                    <div class="sticky top-0 z-10 flex items-center justify-between bg-white px-6 py-4 border-b border-slate-200 rounded-t-2xl">
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-900">Terms &amp; Conditions @if($settings['terms_version'] ?? false)<span class="text-sm font-mono font-normal text-slate-400 ml-2">v{{ $settings['terms_version'] }}</span>@endif</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Edit the content for the public Terms &amp; Conditions page. HTML is supported.</p>
                        </div>
                        <button type="button" @@click="tcModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <textarea name="terms_and_conditions" rows="20" placeholder="Enter your Terms &amp; Conditions content here..." class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm font-mono">{{ $settings['terms_and_conditions'] ?? $defaultTerms }}</textarea>
                        <p class="mt-3 text-xs text-slate-500">This content will be displayed on the public <a href="{{ route('terms') }}" target="_blank" class="text-gold-600 hover:text-gold-700 font-semibold underline">/terms-and-conditions</a> page.</p>
                    </div>
                    <div class="sticky bottom-0 flex items-center justify-end gap-3 bg-white px-6 py-4 border-t border-slate-200 rounded-b-2xl">
                        <button type="button" @@click="tcModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                        <button type="button" @@click="tcModal = false" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Privacy Policy Modal --}}
        <div x-show="privacyModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex min-h-full items-center justify-center p-4">
                <div x-show="privacyModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @@click="privacyModal = false" class="fixed inset-0 bg-black/60"></div>
                <div x-show="privacyModal" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-4"
                     class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
                    <div class="sticky top-0 z-10 flex items-center justify-between bg-white px-6 py-4 border-b border-slate-200 rounded-t-2xl">
                        <div>
                            <h2 class="text-lg font-extrabold text-slate-900">Privacy Policy @if($settings['privacy_version'] ?? false)<span class="text-sm font-mono font-normal text-slate-400 ml-2">v{{ $settings['privacy_version'] }}</span>@endif</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Edit the content for the public Privacy Policy page. HTML is supported.</p>
                        </div>
                        <button type="button" @@click="privacyModal = false" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    <div class="p-6">
                        <textarea name="privacy_policy" rows="20" placeholder="Enter your Privacy Policy content here..." class="block w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900 placeholder-slate-400 focus:border-gold-500 focus:outline-none sm:text-sm font-mono">{{ $settings['privacy_policy'] ?? $defaultPrivacy }}</textarea>
                        <p class="mt-3 text-xs text-slate-500">This content will be displayed on the public <a href="{{ route('privacy') }}" target="_blank" class="text-gold-600 hover:text-gold-700 font-semibold underline">/privacy-policy</a> page.</p>
                    </div>
                    <div class="sticky bottom-0 flex items-center justify-end gap-3 bg-white px-6 py-4 border-t border-slate-200 rounded-b-2xl">
                        <button type="button" @@click="privacyModal = false" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">Cancel</button>
                        <button type="button" @@click="privacyModal = false" class="inline-flex items-center gap-2 rounded-xl bg-gold-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-gold-500/25 hover:bg-gold-600 transition-all cursor-pointer">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Done
                        </button>
                    </div>
                </div>
            </div>
        </div>

                </form>
</div>
@endsection
