@extends('layouts.app')

@section('title', 'Gestion des Propriétaires')

@section('content')

{{-- Flash success --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-circle-check text-lg text-green-500"></i>
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-alert-circle text-lg text-red-500"></i>
    {{ session('error') }}
</div>
@endif

{{-- ── PAGE HEADER ─────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Gestion des Propriétaires</h1>
        <p class="text-sm text-slate-500 mt-0.5">Répertoire des propriétaires de logements — {{ number_format($stats['total']) }} enregistrés</p>
    </div>
    <a href="{{ route('proprietaires.create') }}"
       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0"
       style="background:#F77F00;">
        <i class="ti ti-plus text-base"></i>
        Ajouter un propriétaire
    </a>
</div>

{{-- ── STAT CARDS ───────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(26,36,64,.10);">
            <i class="ti ti-users text-2xl" style="color:#1a2440;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['total']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(247,127,0,.12);">
            <i class="ti ti-user text-2xl" style="color:#F77F00;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Personnes physiques</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['physiques']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(139,92,246,.12);">
            <i class="ti ti-building text-2xl text-purple-500"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Personnes morales</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['morales']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(0,154,68,.12);">
            <i class="ti ti-home text-2xl" style="color:#009A44;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Logements gérés</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['logements_count']) }}</p>
        </div>
    </div>

</div>

{{-- ── FILTERS ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('proprietaires.index') }}">
        <div class="flex flex-col md:flex-row gap-3">

            {{-- Search --}}
            <div class="flex-1 min-w-0">
                <div class="relative">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Rechercher par nom, raison sociale, pièce d'identité…"
                           class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent"
                           style="--tw-ring-color:#F77F00;">
                </div>
            </div>

            {{-- Type personne --}}
            <div class="w-full md:w-48">
                <select name="type_personne"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 bg-white">
                    <option value="">Tous les types</option>
                    <option value="physique" {{ request('type_personne') === 'physique' ? 'selected' : '' }}>Personne physique</option>
                    <option value="morale" {{ request('type_personne') === 'morale' ? 'selected' : '' }}>Personne morale</option>
                </select>
            </div>

            {{-- Localité --}}
            <div class="w-full md:w-44">
                <select name="localite_id"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 bg-white">
                    <option value="">Toutes les localités</option>
                    @foreach($localites as $localite)
                    <option value="{{ $localite->id }}" {{ request('localite_id') == $localite->id ? 'selected' : '' }}>
                        {{ $localite->libelle }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Actif --}}
            <div class="w-full md:w-36">
                <select name="actif"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 bg-white">
                    <option value="">Tous</option>
                    <option value="1" {{ request('actif') === '1' ? 'selected' : '' }}>Actifs</option>
                    <option value="0" {{ request('actif') === '0' ? 'selected' : '' }}>Inactifs</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                        style="background:#1a2440;">
                    <i class="ti ti-search text-sm"></i>
                    <span class="hidden sm:inline">Filtrer</span>
                </button>
                @if(request()->hasAny(['search','type_personne','localite_id','actif']))
                <a href="{{ route('proprietaires.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                    <i class="ti ti-x text-sm"></i>
                    <span class="hidden sm:inline">Réinitialiser</span>
                </a>
                @endif
            </div>

        </div>
    </form>
</div>

{{-- ── TABLE ────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Table header bar --}}
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between" style="background:#f8f9fb;">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            @if($proprietaires->total() > 0)
                {{ $proprietaires->firstItem() }}–{{ $proprietaires->lastItem() }} sur {{ number_format($proprietaires->total()) }} résultats
            @else
                Aucun résultat
            @endif
        </span>
        @if(request()->hasAny(['search','type_personne','localite_id','actif']))
        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
            <i class="ti ti-filter text-xs"></i> Filtre actif
        </span>
        @endif
    </div>

    @if($proprietaires->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <i class="ti ti-users text-3xl text-slate-300"></i>
        </div>
        <p class="text-base font-semibold text-slate-600">Aucun propriétaire trouvé</p>
        <p class="text-sm mt-1 text-slate-400">Modifiez vos filtres ou ajoutez un nouveau propriétaire.</p>
        <a href="{{ route('proprietaires.create') }}"
           class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white"
           style="background:#F77F00;">
            <i class="ti ti-plus text-sm"></i> Ajouter un propriétaire
        </a>
    </div>
    @else

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100"
                    style="background:#f8f9fb;">
                    <th class="px-5 py-3">Type</th>
                    <th class="px-5 py-3">Nom / Raison sociale</th>
                    <th class="px-5 py-3 hidden lg:table-cell">Pièce d'identité</th>
                    <th class="px-5 py-3 hidden md:table-cell">Téléphone</th>
                    <th class="px-5 py-3 hidden md:table-cell">Localité</th>
                    <th class="px-5 py-3 text-center">Logements</th>
                    <th class="px-5 py-3 text-center hidden sm:table-cell">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($proprietaires as $proprietaire)
                <tr class="hover:bg-slate-50/70 transition-colors group">

                    {{-- Type --}}
                    <td class="px-5 py-3.5">
                        @if($proprietaire->type_personne === 'physique')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                            <i class="ti ti-user mr-1 text-xs"></i> Physique
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                            <i class="ti ti-building mr-1 text-xs"></i> Morale
                        </span>
                        @endif
                    </td>

                    {{-- Nom --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background: {{ $proprietaire->type_personne === 'physique' ? '#F77F00' : '#1a2440' }};">
                                {{ mb_strtoupper(mb_substr($proprietaire->nom, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800">{{ $proprietaire->nom_complet }}</div>
                                @if($proprietaire->type_personne === 'morale' && $proprietaire->nom)
                                <div class="text-xs text-slate-500">Représentant : {{ $proprietaire->nom }}</div>
                                @elseif($proprietaire->prenoms)
                                <div class="text-xs text-slate-500">{{ $proprietaire->prenoms }}</div>
                                @endif
                            </div>
                        </div>
                    </td>

                    {{-- Pièce --}}
                    <td class="px-5 py-3.5 hidden lg:table-cell">
                        <div class="text-xs text-slate-500">{{ $proprietaire->type_piece_libelle }}</div>
                        <div class="font-mono text-xs text-slate-700">{{ $proprietaire->num_piece_identite }}</div>
                    </td>

                    {{-- Téléphone --}}
                    <td class="px-5 py-3.5 hidden md:table-cell text-slate-600 text-xs">
                        {{ $proprietaire->telephone }}
                    </td>

                    {{-- Localité --}}
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        @if($proprietaire->localite)
                        <span class="inline-flex items-center gap-1 text-xs text-slate-600">
                            <i class="ti ti-map-pin text-slate-400"></i>
                            {{ $proprietaire->localite->libelle }}
                        </span>
                        @else
                        <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    {{-- Logements --}}
                    <td class="px-5 py-3.5 text-center">
                        @if($proprietaire->logements_civils_count > 0)
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold text-white" style="background:#009A44;">
                            {{ $proprietaire->logements_civils_count }}
                        </span>
                        @else
                        <span class="text-slate-300 text-xs">0</span>
                        @endif
                    </td>

                    {{-- Statut --}}
                    <td class="px-5 py-3.5 text-center hidden sm:table-cell">
                        @if($proprietaire->actif)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Actif</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Inactif</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('proprietaires.show', $proprietaire) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Voir la fiche">
                                <i class="ti ti-eye text-base"></i>
                            </a>
                            <a href="{{ route('proprietaires.edit', $proprietaire) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Modifier">
                                <i class="ti ti-pencil text-base"></i>
                            </a>
                            <form method="POST" action="{{ route('proprietaires.destroy', $proprietaire) }}"
                                  onsubmit="return confirm('Supprimer {{ addslashes($proprietaire->nom_complet) }} ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 transition" title="Supprimer">
                                    <i class="ti ti-trash text-base"></i>
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($proprietaires->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3"
         style="background:#f8f9fb;">
        <p class="text-xs text-slate-500">
            Page {{ $proprietaires->currentPage() }} sur {{ $proprietaires->lastPage() }}
        </p>
        {{ $proprietaires->links() }}
    </div>
    @endif

    @endif
</div>

@endsection
