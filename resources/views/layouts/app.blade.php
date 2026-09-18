<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GESTBAIL — @yield('title', 'Tableau de bord')</title>
    <link rel="icon" href="/favicon.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>[x-cloak]{display:none!important}</style>
</head>
<body class="bg-gray-50 font-sans antialiased">

<div class="flex h-screen overflow-hidden">

    {{-- ═══════════════════════════════════════════════════════
         SIDEBAR
    ═══════════════════════════════════════════════════════ --}}
    {{-- Desktop : dans le flux flex (w-64, flex-shrink-0)
         Mobile  : fixed hors flux, cachée via translateX, ouverte par JS --}}
    <aside id="sidebar"
           class="flex flex-col w-[272px] min-h-screen flex-shrink-0 transition-transform duration-300 z-30"
           style="background:linear-gradient(180deg,#243366 0%,#1e2d52 55%,#1b2748 100%);">

        {{-- Logo area --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-white/5" style="background-color:#1a2440; min-height:76px;">
            <img src="/images/logo-police.png" alt="Logo Police" class="h-11 w-11 object-contain flex-shrink-0">
            <div class="leading-tight">
                <div class="text-white font-bold text-lg tracking-widest">GESTBAIL</div>
                <div class="text-slate-300 text-[11px] leading-tight">Police Nationale CI</div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto py-4 space-y-5">

            @php
                $linkBase = 'flex items-center gap-3.5 px-4 py-3 text-[15px] font-medium nav-item rounded-r-lg mx-2 transition-colors';

                // Lien simple (toujours visible)
                $solo = [
                    ['dashboard', 'dashboard*', 'ti-layout-dashboard', 'Dashboard'],
                ];

                // Groupes déroulants
                $groups = [
                    ['title' => 'Gestion', 'icon' => 'ti-folder', 'items' => [
                        ['policiers.index', 'policiers*', 'ti-users', 'Policiers'],
                        ['logements.index', 'logements*', 'ti-building', 'Logements'],
                        ['proprietaires.index', 'proprietaires*', 'ti-user-check', 'Propriétaires'],
                    ]],
                    ['title' => 'Contrats', 'icon' => 'ti-file-text', 'items' => [
                        ['bordereaux.index', 'bordereaux*', 'ti-folders', 'Bordereaux'],
                        ['contrats.index', 'contrats*', 'ti-file-text', 'Contrats de bail'],
                    ]],
                    ['title' => 'Paiements', 'icon' => 'ti-cash', 'items' => [
                        ['reglements.index', 'reglements*', 'ti-cash', 'Règlements'],
                        ['virements.index', 'virements*', 'ti-building-bank', 'Virements'],
                    ]],
                    ['title' => 'GED', 'icon' => 'ti-paperclip', 'items' => [
                        ['documents.index', 'documents*', 'ti-paperclip', 'Documents'],
                    ]],
                    ['title' => 'Rapports', 'icon' => 'ti-chart-bar', 'items' => [
                        ['rapports.index', 'rapports*', 'ti-chart-bar', 'États & Statistiques'],
                    ]],
                ];

                if (auth()->user()?->hasRole('administrateur')) {
                    $groups[] = ['title' => 'Administration', 'icon' => 'ti-shield-lock', 'items' => [
                        ['parametres.utilisateurs.index', 'parametres.utilisateurs.*', 'ti-users-group', 'Utilisateurs'],
                        ['parametres.activites.index',    'parametres.activites.*',    'ti-activity',    'Journal d\'activités'],
                    ]];
                    $groups[] = ['title' => 'Paramètres', 'icon' => 'ti-settings', 'items' => [
                        ['parametres.index', 'parametres.index', 'ti-settings', 'Vue d\'ensemble'],
                        ['parametres.grades.index', 'parametres.grades.*', 'ti-award', 'Grades'],
                        ['parametres.localites.index', 'parametres.localites.*', 'ti-map-pin', 'Localités'],
                        ['parametres.services.index', 'parametres.services.*', 'ti-building-community', 'Services'],
                        ['parametres.nomenclatures.index', 'parametres.nomenclatures.*', 'ti-list-details', 'Statuts & types'],
                    ]];
                }
            @endphp

            {{-- Liens simples --}}
            <div>
                @foreach($solo as [$route, $pattern, $icon, $label])
                <a href="{{ route($route) }}"
                   class="{{ request()->routeIs($pattern) ? 'text-orange-300 nav-item-active '.$linkBase : 'text-slate-200 hover:text-white '.$linkBase }}"
                   style="{{ request()->routeIs($pattern) ? 'background:rgba(247,127,0,.12);border-left:3px solid #F77F00;' : '' }}">
                    <i class="ti {{ $icon }} text-xl w-6 text-center"></i>
                    <span>{{ $label }}</span>
                </a>
                @endforeach
            </div>

            {{-- Groupes déroulants --}}
            @foreach($groups as $group)
                @php $groupActif = collect($group['items'])->contains(fn($it) => request()->routeIs($it[1])); @endphp
                <div x-data="{ open: {{ $groupActif ? 'true' : 'false' }} }">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between px-5 mb-1.5 py-1.5 group/hd">
                        <span class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-wider text-slate-400 group-hover/hd:text-slate-200 transition-colors">
                            <i class="ti {{ $group['icon'] }} text-sm"></i> {{ $group['title'] }}
                        </span>
                        <i class="ti ti-chevron-down text-sm text-slate-400 group-hover/hd:text-slate-200 transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open" @if(! $groupActif) style="display:none" @endif>
                        @foreach($group['items'] as [$route, $pattern, $icon, $label])
                        <a href="{{ route($route) }}"
                           class="{{ request()->routeIs($pattern) ? 'text-orange-300 nav-item-active '.$linkBase : 'text-slate-200 hover:text-white '.$linkBase }}"
                           style="{{ request()->routeIs($pattern) ? 'background:rgba(247,127,0,.12);border-left:3px solid #F77F00;' : '' }}">
                            <i class="ti {{ $icon }} text-xl w-6 text-center"></i>
                            <span>{{ $label }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </nav>

        {{-- Bottom: user info + logout --}}
        <div class="border-t border-slate-700/50 p-4">
            @auth
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                     style="background-color:#F77F00;">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-[11px] text-slate-400 truncate">
                        {{ method_exists(Auth::user(), 'getRoleNames') ? Auth::user()->getRoleNames()->first() ?? 'Utilisateur' : 'Utilisateur' }}
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Déconnexion"
                            class="text-slate-400 hover:text-red-400 transition-colors p-1">
                        <i class="ti ti-logout text-lg"></i>
                    </button>
                </form>
            </div>
            @endauth
        </div>
    </aside>

    {{-- ═══════════════════════════════════════════════════════
         MAIN AREA
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden lg:ml-0">

        {{-- Top header --}}
        <header class="bg-white shadow-sm flex items-center justify-between px-6 h-[72px] flex-shrink-0 z-20">
            <div class="flex items-center gap-4">
                {{-- Mobile hamburger --}}
                <button id="sidebarToggle" class="lg:hidden text-slate-500 hover:text-slate-700 p-1">
                    <i class="ti ti-menu-2 text-2xl"></i>
                </button>
                <h1 class="text-lg font-semibold text-slate-800 truncate max-w-[180px] sm:max-w-none">@yield('title', 'Tableau de bord')</h1>
            </div>

            <div class="flex items-center gap-3">
                {{-- Notification bell --}}
                <button class="relative p-2 text-slate-500 hover:text-slate-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="ti ti-bell text-xl"></i>
                    <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full" style="background-color:#F77F00;"></span>
                </button>

                {{-- User avatar dropdown --}}
                @auth
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="h-8 w-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
                             style="background-color:#F77F00;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden sm:block text-sm font-medium text-slate-700">{{ Auth::user()->name }}</span>
                        <i class="ti ti-chevron-down text-slate-400 text-sm"></i>
                    </button>
                    <div x-show="open" x-cloak @click.outside="open = false"
                         class="absolute right-0 mt-1 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-xs text-slate-500">Connecté en tant que</p>
                            <p class="text-sm font-medium text-slate-800 truncate">{{ Auth::user()->email }}</p>
                        </div>
                        <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-gray-50">
                            <i class="ti ti-user text-slate-400"></i> Mon profil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="ti ti-logout text-red-400"></i> Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
            @yield('content')
        </main>

    </div>
</div>

{{-- Mobile sidebar overlay --}}
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-20 hidden"></div>

<script>
    const sidebar  = document.getElementById('sidebar');
    const toggle   = document.getElementById('sidebarToggle');
    const overlay  = document.getElementById('sidebarOverlay');
    const BREAKPOINT = 1024; // lg

    function isMobile() { return window.innerWidth < BREAKPOINT; }

    function applyMobileLayout() {
        if (isMobile()) {
            // Sortir du flux et cacher
            sidebar.style.position  = 'fixed';
            sidebar.style.top       = '0';
            sidebar.style.left      = '0';
            sidebar.style.height    = '100vh';
            sidebar.style.transform = 'translateX(-100%)';
            sidebar.style.zIndex    = '50';
        } else {
            // Remettre dans le flux
            sidebar.style.position  = '';
            sidebar.style.top       = '';
            sidebar.style.left      = '';
            sidebar.style.height    = '';
            sidebar.style.transform = '';
            sidebar.style.zIndex    = '';
            overlay.classList.add('hidden');
        }
    }

    applyMobileLayout();
    window.addEventListener('resize', applyMobileLayout);

    if (toggle) {
        toggle.addEventListener('click', () => {
            if (sidebar.style.transform === 'translateX(-100%)') {
                sidebar.style.transform = 'translateX(0)';
                overlay.classList.remove('hidden');
            } else {
                sidebar.style.transform = 'translateX(-100%)';
                overlay.classList.add('hidden');
            }
        });
        overlay.addEventListener('click', () => {
            sidebar.style.transform = 'translateX(-100%)';
            overlay.classList.add('hidden');
        });
    }
</script>

@livewireScripts
@stack('scripts')

{{-- Modale de prévisualisation universelle --}}
@include('documents._preview_modal')
</body>
</html>
