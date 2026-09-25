<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Setup - Lifescend</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'sans-serif'] },
                    colors: {
                        gold: {
                            50:  '#FFFDF0',
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
</head>
<body class="h-full font-sans antialiased bg-slate-50">
    @yield('content')
</body>
</html>
