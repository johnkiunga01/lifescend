<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $organization?->name ?? 'Lifescend' }} - Dashboard</title>

    @if($organization?->favicon ?? false)
    <link rel="icon" type="image/x-icon" href="{{ Storage::url($organization->favicon) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.7/quill.snow.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        gold: {
                            50: '#FFFDF0',
                            100: '#FFF8D6',
                            200: '#FFEE9E',
                            300: '#FFE066',
                            400: '#FBBF24',
                            500: '#D4AF37',
                            600: '#B8960F',
                            700: '#8B6F0A',
                            800: '#5E4A07',
                            900: '#3A2D04',
                        },
                    },
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important
        }

        .rte .ql-toolbar {
            border-color: #cbd5e1;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            background: #f8fafc;
        }

        .rte .ql-container {
            border-color: #cbd5e1;
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            font-family: 'Outfit', sans-serif;
            font-size: 0.875rem;
        }

        .rte .ql-editor {
            min-height: 180px;
            max-height: 320px;
            overflow-y: auto;
        }

        .rte .ql-container.ql-snow:focus-within,
        .rte .ql-container.ql-snow {
            border-color: #cbd5e1;
        }

        .rte:focus-within .ql-toolbar,
        .rte:focus-within .ql-container {
            border-color: #D4AF37;
        }

        .rte .ql-toolbar button.ql-shortcode {
            width: auto;
            padding: 0 7px;
            color: #475569;
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
            font-weight: 700;
        }

        .rte .ql-toolbar button.ql-shortcode::before {
            content: 'Shortcode';
        }

        .rte .ql-toolbar button.ql-shortcode:hover {
            color: #B8960F;
        }
    </style>
</head>

<body class="h-full font-sans antialiased">

    <div class="flex h-full" x-data="{ sidebarOpen: false, profileModal: false, trainingModal: false }">

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" @@click="sidebarOpen = false" class="fixed inset-0 z-40 bg-black/50 lg:hidden" style="display: none;"></div>

        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-black flex flex-col transform transition-transform duration-200 lg:translate-x-0 lg:static lg:inset-auto {{-- mobile hidden by default --}}"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <div class="flex items-center h-16 px-6 border-b border-white/10 shrink-0 bg-gradient-to-r from-gold-500 to-gold-600">
                <a href="{{ route('dashboard') }}">
                    <div class="w-[70px] rounded-lg bg-gold-500 flex items-center justify-center overflow-hidden">
                        @if($organization?->logo)<img src="{{ Storage::url($organization->logo) }}" alt="Logo" class="w-full">@else<span class="text-sm font-bold text-white">L</span>@endif
                    </div>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">
                @php $role = Auth::user()->organizations()->first()?->pivot?->role ?? 'customer'; @endphp
                @php
                    if (in_array($role, ['admin', 'manager', 'staff'])) {
                        $trainingReqCount = \App\Models\TrainingRequest::where('status', 'pending')->count();
                        $ticketCount = \App\Models\Ticket::count();
                        $reservedCount = \App\Models\Order::whereIn('status', ['reserved', 'pending'])->whereDoesntHave('tickets')->count();
                        if (in_array($role, ['admin', 'manager'])) {
                            $enquiryCount = \App\Models\EventEnquiry::whereNull('replied_at')->count();
                            $contactMsgCount = \App\Models\ContactMessage::count();
                            $supportCount = \App\Models\AccountSupportRequest::where('status', 'open')->count();
                        }
                    }
                @endphp

                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150
                      {{ request()->routeIs('dashboard') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                @if($role === 'customer')
                {{-- Customer sidebar navigation --}}
                <div class="px-3 pt-6 pb-1">
                    <span class="px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/30">My Account</span>
                </div>
                <a href="{{ route('customer.tickets') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('customer.tickets') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    My Tickets
                </a>
                <a href="{{ route('customer.calendar') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('customer.calendar') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    My Calendar
                </a>
                <a href="{{ route('customer.saved-events') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('customer.saved-events') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z" /></svg>
                    Saved Events
                </a>
                <a href="{{ route('training.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('training.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    Training Requests
                </a>

                <div class="px-3 pt-6 pb-1">
                    <span class="px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/30">Billing & Support</span>
                </div>
                <a href="{{ route('customer.billing') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('customer.billing') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                    Billing History
                </a>
                <a href="{{ route('customer.enquiries') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('customer.enquiries') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                    My Enquiries
                </a>
                <a href="{{ route('customer.notifications') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('customer.notifications') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    Notifications
                </a>

                <a href="{{ route('testimonials') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('testimonials') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                    Testimonials
                </a>
                <a href="{{ route('customer.settings') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('customer.settings') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    Account Settings
                </a>
                <a href="{{ route('customer.help') }}"
                    class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('customer.help') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" /></svg>
                    Help & Support
                </a>
                @endif

                @if($role === 'admin' || $role === 'manager')
                <div class="px-3 pt-6 pb-1">
                    <span class="px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/30">Planning</span>
                </div>
                <a href="{{ route('manage.events.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.events.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Events
                </a>
                <a href="{{ route('manage.event-types.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.event-types.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Event Types
                </a>
                <a href="{{ route('manage.venues.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.venues.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Venues
                </a>
                <a href="{{ route('manage.speakers.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.speakers.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Speakers
                </a>
                <a href="{{ route('manage.sponsors.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.sponsors.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a3 3 0 01-3-3V7m8 4v2m-2-2v2m-4-2v2m-2-2v2" />
                    </svg>
                    Sponsors
                </a>
                <a href="{{ route('manage.partners.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.partners.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Partners
                </a>
                <a href="{{ route('manage.tickets.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.tickets.*') && request('view') !== 'reserved' ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    Tickets
                    @if(isset($ticketCount) && $ticketCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-white/20 text-white">{{ $ticketCount }}</span>
                    @endif
                </a>
                <a href="{{ route('manage.tickets.index', ['view' => 'reserved']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.tickets.*') && request('view') === 'reserved' ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Reserved
                    @if(isset($reservedCount) && $reservedCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-500 text-white">{{ $reservedCount }}</span>
                    @endif
                </a>
                <div class="mt-4 border-t border-white/10 pt-4">
                    <div class="px-3 pb-1">
                        <span class="px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/30">Operations</span>
                    </div>
                    <a href="{{ route('checkin.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('checkin.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Check-In
                    </a>
                    <a href="{{ route('manage.enquiries.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.enquiries.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Enquiries
                        @if(isset($enquiryCount) && $enquiryCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-500 text-white">{{ $enquiryCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('training.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('training.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        Training Requests
                        @if(isset($trainingReqCount) && $trainingReqCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-500 text-white">{{ $trainingReqCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('manage.testimonials.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.testimonials.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                        Testimonials
                    </a>
                    <a href="{{ route('manage.contact-messages.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.contact-messages.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Contact Messages
                        @if(isset($contactMsgCount) && $contactMsgCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-500 text-white">{{ $contactMsgCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('manage.account-support.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.account-support.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.479m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        Account Support
                        @if(isset($supportCount) && $supportCount > 0)
                        <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-500 text-white">{{ $supportCount }}</span>
                        @endif
                    </a>
                </div>
                <div class="mt-4 border-t border-white/10 pt-4">
                    <div class="px-3 pb-1">
                        <span class="px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/30">Finance</span>
                    </div>
                    <a href="{{ route('manage.payments.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.payments.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Payments
                    </a>
                    <a href="{{ route('manage.coupons.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.coupons.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Coupons
                    </a>
                </div>
                @endif
                @if($role === 'admin')
                <div class="mt-4 border-t border-white/10 pt-4">
                    <div class="px-3 pb-1">
                        <span class="px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/30">Admin</span>
                    </div>
                    <a href="{{ route('manage.settings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.settings.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                    <a href="{{ route('manage.users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.users.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        Users
                    </a>
                </div>
                @endif
                @if($role === 'manager')
                <div class="mt-4 border-t border-white/10 pt-4">
                    <a href="{{ route('manage.users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.users.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                        Users
                    </a>
                </div>
                @endif
                @if($role === 'admin' || $role === 'manager')
                <div class="mt-4 border-t border-white/10 pt-4">
                    <div class="px-3 pb-1">
                        <span class="px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/30">Analytics</span>
                    </div>
                    <a href="{{ route('manage.reports.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.reports.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Reports
                    </a>
                </div>
                @endif
                @if($role === 'staff')
                <div class="px-3 pt-6 pb-1">
                    <span class="px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-white/30">Operations</span>
                </div>
                <a href="{{ route('checkin.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('checkin.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Check-In Portal
                </a>
                <a href="{{ route('training.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('training.*') ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Training Requests
                    @if(isset($trainingReqCount) && $trainingReqCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-red-500 text-white">{{ $trainingReqCount }}</span>
                    @endif
                </a>
                <a href="{{ route('manage.tickets.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.tickets.*') && request('view') !== 'reserved' ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                    Tickets
                    @if(isset($ticketCount) && $ticketCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-white/20 text-white">{{ $ticketCount }}</span>
                    @endif
                </a>
                <a href="{{ route('manage.tickets.index', ['view' => 'reserved']) }}" class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors duration-150 {{ request()->routeIs('manage.tickets.*') && request('view') === 'reserved' ? 'bg-gold-500 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Reserved
                    @if(isset($reservedCount) && $reservedCount > 0)
                    <span class="ml-auto inline-flex items-center justify-center px-2 py-0.5 text-[10px] font-bold rounded-full bg-blue-500 text-white">{{ $reservedCount }}</span>
                    @endif
                </a>
                @endif
            </nav>

            <div class="border-t border-white/10 px-3 py-4">
                <div class="flex items-center gap-3 px-3">
                    <div class="h-8 w-8 rounded-full bg-white/20 flex items-center justify-center font-bold text-white text-xs">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-white/50 truncate">{{ ucfirst(Auth::user()->organizations()->first()?->pivot?->role ?? 'customer') }}</p>
                    </div>
                </div>
                <div class="mt-3 px-3 space-y-1">
                    <a href="{{ route('terms') }}" target="_blank" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-white/50 hover:text-white/80 hover:bg-white/5 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        Terms of Service
                    </a>
                    <a href="{{ route('privacy') }}" target="_blank" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-white/50 hover:text-white/80 hover:bg-white/5 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                        Privacy Policy
                    </a>
                </div>
                <a href="{{ route('logout') }}" class="mt-2 flex items-center gap-3 rounded-lg px-3 py-2 text-sm text-red-300 hover:bg-white/10 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Logout
                </a>
            </div>
        </aside>

        {{-- Main area --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top bar --}}
            <header class="h-16 bg-gradient-to-r from-gold-500 to-gold-600 border-b border-white/10 flex items-center justify-between px-4 lg:px-6 shrink-0">
                <div class="flex items-center gap-3">
                    <button @@click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg bg-black text-gold-400 hover:bg-slate-800">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <h2 class="text-lg font-bold text-white hidden sm:block">@yield('pageTitle', 'Dashboard')</h2>
                </div>
                <div class="flex items-center gap-4">
                    @include('notifications.partials.bell')
                    <a href="{{ route('home') }}" class="text-sm text-white/70 hover:text-white transition-colors">
                        View Site
                    </a>
                    <button @@click="profileModal = true" class="flex items-center gap-2 text-sm text-white/90 hover:text-white transition-colors cursor-pointer">
                        <div class="h-7 w-7 rounded-full bg-white/20 flex items-center justify-center font-bold text-white text-[10px]">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden sm:inline font-medium">{{ Auth::user()->name }}</span>
                    </button>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @if(!Auth::user()->hasVerifiedEmail())
                <div class="mb-6 flex items-center gap-3 rounded-xl bg-amber-50 border border-amber-200 px-5 py-3 text-sm text-amber-700">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                    <div class="flex-1">
                        <span class="font-semibold">Email not verified.</span>
                        <a href="{{ route('verification.notice') }}" class="font-semibold underline hover:no-underline">Verify your email</a> to access all features.
                    </div>
                </div>
                @endif
                @yield('content')
            </main>
        </div>

        @include('profile.modal')
        @include('training._request-modal')
    </div>

    @stack('scripts')
</body>

</html>
