@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')

{{-- Header / branding --}}
<div class="px-8 pt-10 pb-6 text-center border-b border-gray-100">
    <img src="/images/logo-police.png" alt="Logo Police Nationale CI"
         class="h-16 w-16 object-contain mx-auto mb-4">
    <h1 class="text-2xl font-bold tracking-widest" style="color:#1a2440;">GESTBAIL</h1>
    <p class="text-sm text-slate-500 mt-1">Police Nationale de Côte d'Ivoire</p>
    {{-- Orange divider --}}
    <div class="mt-4 mx-auto h-0.5 w-16 rounded" style="background:#F77F00;"></div>
</div>

{{-- Form --}}
<div class="px-8 py-8">

    @if ($errors->any())
    <div class="mb-5 flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
        <i class="ti ti-alert-circle text-red-500 text-lg mt-0.5 flex-shrink-0"></i>
        <div class="text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif

    @if (session('status'))
    <div class="mb-5 flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-700">
        <i class="ti ti-circle-check text-green-500"></i>
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">
                Adresse e-mail
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ti ti-mail text-slate-400 text-lg"></i>
                </span>
                <input id="email" name="email" type="email"
                       value="{{ old('email') }}"
                       autocomplete="email"
                       required
                       placeholder="prenom.nom@police.ci"
                       class="w-full pl-10 pr-4 py-2.5 border rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 transition-colors
                              {{ $errors->has('email') ? 'border-red-300 bg-red-50 focus:ring-red-200' : 'border-gray-200 bg-gray-50 focus:ring-orange-200 focus:border-orange-400' }}">
            </div>
            @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">
                Mot de passe
            </label>
            <div class="relative" id="passwordWrapper">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ti ti-lock text-slate-400 text-lg"></i>
                </span>
                <input id="password" name="password" type="password"
                       autocomplete="current-password"
                       required
                       placeholder="••••••••"
                       class="w-full pl-10 pr-10 py-2.5 border border-gray-200 bg-gray-50 rounded-xl text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-200 focus:border-orange-400 transition-colors">
                <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="ti ti-eye text-lg" id="eyeIcon"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember me --}}
        <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox"
                   class="h-4 w-4 rounded border-gray-300 text-orange-500 focus:ring-orange-400">
            <label for="remember" class="ml-2 text-sm text-slate-600 cursor-pointer">
                Se souvenir de moi
            </label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white shadow-sm hover:opacity-90 active:scale-[.98] transition-all"
                style="background-color:#F77F00;">
            <i class="ti ti-login text-base"></i>
            Connexion
        </button>

    </form>
</div>

{{-- Footer --}}
<div class="px-8 pb-6 text-center">
    <p class="text-[11px] text-slate-400">Système de Gestion des Baux Administratifs</p>
</div>

<script>
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            eyeIcon.className = isPassword ? 'ti ti-eye-off text-lg' : 'ti ti-eye text-lg';
        });
    }
</script>

@endsection
