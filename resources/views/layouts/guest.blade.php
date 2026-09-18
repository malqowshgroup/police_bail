<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GESTBAIL — @yield('title', 'Connexion')</title>
    <link rel="icon" href="/favicon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center font-sans antialiased"
      style="background: linear-gradient(135deg, #111827 0%, #1a2440 50%, #1e2d52 100%);">

    {{-- Background decorative elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        {{-- Top-left CI orange stripe --}}
        <div class="absolute -top-20 -left-20 w-96 h-96 rounded-full opacity-5"
             style="background: radial-gradient(circle, #F77F00 0%, transparent 70%);"></div>
        {{-- Bottom-right CI green stripe --}}
        <div class="absolute -bottom-20 -right-20 w-96 h-96 rounded-full opacity-5"
             style="background: radial-gradient(circle, #009A44 0%, transparent 70%);"></div>
        {{-- CI flag color bars (top) --}}
        <div class="absolute top-0 left-0 right-0 h-1 flex">
            <div class="flex-1" style="background:#F77F00;"></div>
            <div class="flex-1 bg-white opacity-10"></div>
            <div class="flex-1" style="background:#009A44;"></div>
        </div>
        {{-- CI flag color bars (bottom) --}}
        <div class="absolute bottom-0 left-0 right-0 h-1 flex">
            <div class="flex-1" style="background:#F77F00;"></div>
            <div class="flex-1 bg-white opacity-10"></div>
            <div class="flex-1" style="background:#009A44;"></div>
        </div>
    </div>

    {{-- Auth card --}}
    <div class="relative w-full max-w-md mx-4">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            @yield('content')
        </div>
        <p class="text-center text-slate-500 text-xs mt-6">
            &copy; {{ date('Y') }} Police Nationale de Côte d'Ivoire — Tous droits réservés
        </p>
    </div>

</body>
</html>
