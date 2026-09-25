<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Lifescend</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        gold: {
                            50: '#FFFDF0', 100: '#FFF8D6', 200: '#FFEE9E', 300: '#FFE066',
                            400: '#FBBF24', 500: '#D4AF37', 600: '#B8960F', 700: '#8B6F0A',
                            800: '#5E4A07', 900: '#3A2D04',
                        },
                    },
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans antialiased bg-gradient-to-br from-slate-900 via-slate-800 to-slate-700">
    <div class="min-h-full flex flex-col items-center justify-center px-6 py-24">
        {{-- Grid pattern overlay --}}
        <div class="absolute inset-0 opacity-[0.03]" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

        {{-- Glow orbs --}}
        <div class="absolute -top-40 -right-40 h-80 w-80 rounded-full bg-gold-500/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 h-80 w-80 rounded-full bg-gold-500/5 blur-3xl"></div>

        <div class="relative text-center max-w-lg">
            {{-- 404 graphic --}}
            <div class="mb-8">
                <div class="inline-flex items-center justify-center">
                    <span class="text-[140px] font-extrabold leading-none tracking-tighter text-white/10 select-none" style="font-size: clamp(6rem, 20vw, 10rem);">404</span>
                </div>
            </div>

            {{-- Icon --}}
            <div class="mx-auto mb-6 h-20 w-20 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center backdrop-blur-sm">
                <svg class="h-10 w-10 text-gold-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>

            {{-- Heading --}}
            <h1 class="text-4xl font-extrabold text-white sm:text-5xl leading-tight mb-4">
                Looks Like You've Wandered<br>
                <span class="text-gold-400">Off The Grid!</span>
            </h1>

            {{-- Description --}}
            <p class="text-base text-white/50 leading-relaxed max-w-sm mx-auto mb-10">
                The page you're looking for doesn't exist or has been moved. Let's get you back to familiar territory.
            </p>

            {{-- Back button --}}
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}"
               class="inline-flex items-center gap-3 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/10 px-8 py-4 text-sm font-bold text-white shadow-xl hover:bg-white/20 hover:border-white/20 transition-all duration-200 group">
                <div class="h-8 w-8 rounded-lg bg-gold-500 flex items-center justify-center shadow-lg shadow-gold-500/30 group-hover:shadow-gold-500/50 transition-shadow">
                    <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </div>
                <span>Go Back Home</span>
            </a>

            {{-- Support link --}}
            <p class="mt-8 text-sm text-white/30">
                Need help?
                <a href="{{ route('contact') }}" class="text-gold-400 hover:text-gold-300 font-semibold transition-colors">Contact Support</a>
            </p>
        </div>
    </div>
</body>
</html>