<!DOCTYPE html>
<html lang="en" class="h-full bg-white text-slate-800">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->name ?? $organization?->name ?? 'Lifescend' }} - Training & Events</title>

    @if($organization?->favicon ?? false)
    <link rel="icon" type="image/x-icon" href="{{ Storage::url($organization->favicon) }}">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
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
    <script src="https://js.stripe.com/v3/"></script>
    @stack('head')
    @stack('styles')

    <style>
        :root {
            --gold: #D4AF37;
            --gold-light: #FBBF24;
        }
        @keyframes float-slow {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(25px, -25px) scale(1.03); }
            50% { transform: translate(-15px, 15px) scale(0.97); }
            75% { transform: translate(10px, -10px) scale(1.01); }
        }
        @keyframes float-slower {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(-35px, 30px) scale(1.06); }
            66% { transform: translate(25px, -20px) scale(0.94); }
        }
        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(15px, -15px) rotate(2deg); }
            66% { transform: translate(-10px, 10px) rotate(-2deg); }
        }
        @keyframes pulse-glow {
            0%, 100% { opacity: 1; filter: blur(80px); }
            50% { opacity: 0.7; filter: blur(100px); }
        }
        .animate-float-slow { animation: float-slow 14s ease-in-out infinite; }
        .animate-float-slower { animation: float-slower 18s ease-in-out infinite; }
        .animate-float { animation: float 10s ease-in-out infinite; }
        .animate-pulse-glow { animation: pulse-glow 6s ease-in-out infinite; }
    </style>
</head>

<body class="h-full flex flex-col font-sans antialiased selection:bg-gold-400 selection:text-white" x-data="{ trainingModal: false }">

    <nav class="fixed top-0 z-50 w-full bg-gold-100 shadow-md" x-data="{ open: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                        <a href="{{ route('home') }}">
                            <div class="w-[78px] rounded-xl bg-gold-500 flex items-center justify-center shadow overflow-hidden">
                                @if($organization?->logo)<img src="{{ Storage::url($organization->logo) }}" alt="Logo" class="w-full">@else<span class="text-sm font-bold text-white">L</span>@endif
                            </div>
                        </a>
                </div>

                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="{{ route('home') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('home') ? 'bg-gold-500 text-white' : 'text-slate-700 hover:bg-gold-200/70 hover:text-slate-900' }}">
                            Home
                        </a>
                        <a href="{{ route('events') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('events') ? 'bg-gold-500 text-white' : 'text-slate-700 hover:bg-gold-200/70 hover:text-slate-900' }}">
                            Events
                        </a>
                        <a href="{{ route('about') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('about') ? 'bg-gold-500 text-white' : 'text-slate-700 hover:bg-gold-200/70 hover:text-slate-900' }}">
                            About Us
                        </a>
                        <a href="{{ route('partners') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('partners') ? 'bg-gold-500 text-white' : 'text-slate-700 hover:bg-gold-200/70 hover:text-slate-900' }}">
                            Partners
                        </a>
                        <a href="{{ route('contact') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('contact') ? 'bg-gold-500 text-white' : 'text-slate-700 hover:bg-gold-200/70 hover:text-slate-900' }}">
                            Contact
                        </a>

                        @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg px-3 py-2 text-sm font-medium transition-all duration-200 {{ request()->routeIs('dashboard*') ? 'bg-gold-500 text-white' : 'text-slate-700 hover:bg-gold-200/70 hover:text-slate-900' }}">
                            Dashboard
                        </a>
                        @include('notifications.partials.bell')
                        <a href="{{ route('logout') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-red-500 hover:bg-gold-200/70 hover:text-red-700 transition-all duration-200">
                            Logout
                        </a>
                        <div class="h-8 w-8 rounded-full bg-gold-500 flex items-center justify-center font-bold text-white text-xs ml-2">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        @else
                        <a href="{{ route('login') }}" class="text-slate-700 hover:bg-gold-200/70 hover:text-slate-900 rounded-lg px-3 py-2 text-sm font-medium">Login</a>
                        <a href="{{ route('register') }}" class="bg-black text-gold-400 hover:bg-slate-800 rounded-lg px-3.5 py-2 text-sm font-bold shadow-md">Register</a>
                        @endauth
                    </div>
                </div>

                <div class="flex md:hidden">
                    <button @click="open = !open" type="button" class="inline-flex items-center justify-center rounded-lg bg-gold-200/70 p-2 text-slate-700 hover:bg-gold-200 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            <path x-show="open" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" style="display: none;" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="open" class="md:hidden border-t border-gold-200 bg-gold-100" style="display: none;">
            <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
                <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-gold-200/70">Home</a>
                <a href="{{ route('events') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-gold-200/70">Events</a>
                <a href="{{ route('about') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-gold-200/70">About Us</a>
                <a href="{{ route('partners') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-gold-200/70">Partners</a>
                <a href="{{ route('contact') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-gold-200/70">Contact</a>
                @auth
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-gold-200/70">Dashboard</a>
                <a href="{{ route('logout') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-red-500 hover:bg-gold-200/70">Logout</a>
                @else
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-slate-700 hover:bg-gold-200/70">Login</a>
                <a href="{{ route('register') }}" class="block rounded-lg bg-black px-3.5 py-2 text-base font-bold text-gold-400 hover:bg-slate-800">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="flex-grow pt-16">
        @yield('content')
    </main>

    <footer class="bg-slate-900 border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Brand --}}
                <div>
                    <div class="mb-4">
                        <div class="w-[62px] rounded-lg bg-gold-500 flex items-center justify-center overflow-hidden">
                            @if($organization?->logo)<img src="{{ Storage::url($organization->logo) }}" alt="Logo" class="w-full">@else<span class="text-xs font-bold text-white">L</span>@endif
                        </div>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed max-w-xs">Empowering professionals through world-class training, workshops, and events.</p>
                    <div class="flex items-center gap-3 mt-5">
                        <a href="#" class="h-9 w-9 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-gold-500 hover:text-white transition-all"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg></a>
                        <a href="#" class="h-9 w-9 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-gold-500 hover:text-white transition-all"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/></svg></a>
                        <a href="#" class="h-9 w-9 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-gold-500 hover:text-white transition-all"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14zm-.5 3h-2.5c-1.5 0-2.5 1-2.5 2.5V11h-2v2h2v6h2v-6h2.5l.5-2h-3V8.5c0-.3.2-.5.5-.5H18.5v-2z"/></svg></a>
                    </div>
                </div>

                {{-- Quick Links --}}
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Quick Links</h3>
                    <div class="grid grid-cols-2 gap-x-8 gap-y-3">
                        <ul class="space-y-3">
                            <li><a href="{{ route('home') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Home</a></li>
                            <li><a href="{{ route('events') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Events</a></li>
                            <li><a href="{{ route('about') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">About Us</a></li>
                        </ul>
                        <ul class="space-y-3">
                            <li><a href="{{ route('partners') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Partners</a></li>
                            <li><a href="{{ route('contact') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Contact</a></li>
                            <li><a href="{{ route('testimonials') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Testimonials</a></li>
                        </ul>
                    </div>
                </div>

                {{-- For Attendees --}}
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">For Attendees</h3>
                    <ul class="space-y-3">
                        @auth
                        <li><a href="{{ route('dashboard') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">My Dashboard</a></li>
                        <li><a href="{{ route('tickets.index') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">My Tickets</a></li>
                        @else
                        <li><a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Sign In</a></li>
                        <li><a href="{{ route('register') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Create Account</a></li>
                        @endauth
                        <li><a href="{{ route('training.create') }}" class="text-sm text-slate-400 hover:text-gold-400 transition-colors">Request Training</a></li>
                    </ul>
                </div>

                {{-- Contact --}}
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Get In Touch</h3>
                    <ul class="space-y-3">
                        @if($organization->email ?? false)
                        <li class="flex items-start gap-3 text-sm text-slate-400">
                            <svg class="h-5 w-5 text-gold-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span>{{ $organization->email }}</span>
                        </li>
                        @endif
                        @if($organization->phone ?? false)
                        <li class="flex items-start gap-3 text-sm text-slate-400">
                            <svg class="h-5 w-5 text-gold-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                            <span>{{ $organization->phone }}</span>
                        </li>
                        @endif
                        <li class="flex items-start gap-3 text-sm text-slate-400">
                            <svg class="h-5 w-5 text-gold-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>Mon - Fri: 8:00 AM - 5:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-500">&copy; {{ date('Y') }} {{ $organization->name ?? 'Lifescend' }}. All rights reserved.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('privacy') }}" class="text-sm text-slate-500 hover:text-gold-400 transition-colors">Privacy Policy</a>
                    <a href="{{ route('terms') }}" class="text-sm text-slate-500 hover:text-gold-400 transition-colors">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    @include('training._request-modal')
</body>

</html>