@extends('layouts.app')

@section('title', 'Gestion des Policiers')

@section('content')

{{-- Flash success --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-circle-check text-lg text-green-500"></i>
    {{ session('success') }}
</div>
@endif

{{-- ── PAGE HEADER ─────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Gestion des Policiers</h1>
        <p class="text-sm text-slate-500 mt-0.5">Répertoire du personnel logé — {{ number_format($stats['total']) }} policiers enregistrés</p>
    </div>
    <a href="{{ route('policiers.create') }}"
       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0"
       style="background:#F77F00;">
        <i class="ti ti-plus text-base"></i>
        Ajouter un policier
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
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total policiers</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['total']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(0,154,68,.12);">
            <i class="ti ti-user-check text-2xl" style="color:#009A44;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Actifs</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['actifs']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(245,158,11,.12);">
            <i class="ti ti-user-pause text-2xl text-amber-500"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Suspendus</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['suspendus']) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:rgba(247,127,0,.12);">
            <i class="ti ti-home-check text-2xl" style="color:#F77F00;"></i>
        </div>
        <div class="min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Avec bail actif</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['avec_bail']) }}</p>
        </div>
    </div>

</div>

{{-- ── FILTERS ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('policiers.index') }}">
        <div class="flex flex-col md:flex-row gap-3">

            {{-- Search --}}
            <div class="flex-1 min-w-0">
                <div class="relative">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Rechercher par matricule, nom, prénoms…"
                           class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:border-transparent"
                           style="--tw-ring-color:#F77F00;">
                </div>
            </div>

            {{-- Statut --}}
            <div class="w-full md:w-44">
                <select name="statut"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 bg-white">
                    <option value="">Tous les statuts</option>
                    @foreach(\App\Enums\StatutPolicier::cases() as $case)
                    <option value="{{ $case->value }}" {{ request('statut') === $case->value ? 'selected' : '' }}>
                        {{ $case->label() }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Grade --}}
            <div class="w-full md:w-52">
                <select name="grade_id"
                        class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 bg-white">
                    <option value="">Tous les grades</option>
                    @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" {{ request('grade_id') == $grade->id ? 'selected' : '' }}>
                        {{ $grade->libelle }}
                    </option>
                    @endforeach
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

            {{-- Actions --}}
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                        style="background:#1a2440;">
                    <i class="ti ti-search text-sm"></i>
                    <span class="hidden sm:inline">Filtrer</span>
                </button>
                @if(request()->hasAny(['search','statut','grade_id','localite_id']))
                <a href="{{ route('policiers.index') }}"
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
            @if($policiers->total() > 0)
                {{ $policiers->firstItem() }}–{{ $policiers->lastItem() }} sur {{ number_format($policiers->total()) }} résultats
            @else
                Aucun résultat
            @endif
        </span>
        @if(request()->hasAny(['search','statut','grade_id','localite_id']))
        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
            <i class="ti ti-filter text-xs"></i> Filtre actif
        </span>
        @endif
    </div>

    @if($policiers->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <i class="ti ti-users-group text-3xl text-slate-300"></i>
        </div>
        <p class="text-base font-semibold text-slate-600">Aucun policier trouvé</p>
        <p class="text-sm mt-1 text-slate-400">Modifiez vos filtres ou ajoutez un nouveau policier.</p>
        <a href="{{ route('policiers.create') }}"
           class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white"
           style="background:#F77F00;">
            <i class="ti ti-plus text-sm"></i> Ajouter un policier
        </a>
    </div>
    @else

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100"
                    style="background:#f8f9fb;">
                    <th class="px-5 py-3">Matricule</th>
                    <th class="px-5 py-3">Nom &amp; Prénoms</th>
                    <th class="px-5 py-3 hidden lg:table-cell">Grade</th>
                    <th class="px-5 py-3 hidden md:table-cell">Localité</th>
                    <th class="px-5 py-3">Statut</th>
                    <th class="px-5 py-3 text-center hidden sm:table-cell">Bail</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($policiers as $policier)
                <tr class="hover:bg-slate-50/70 transition-colors group">

                    {{-- Matricule --}}
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">
                            {{ $policier->matricule }}
                        </span>
                    </td>

                    {{-- Nom & Prénoms --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0"
                                 style="background:#1a2440;">
                                {{ mb_strtoupper(mb_substr($policier->nom, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800">{{ $policier->nom }}</div>
                                <div class="text-xs text-slate-500">{{ $policier->prenoms }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Grade --}}
                    <td class="px-5 py-3.5 hidden lg:table-cell">
                        <span class="text-slate-600 text-xs">{{ $policier->grade?->libelle ?? '—' }}</span>
                    </td>

                    {{-- Localité --}}
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        @if($policier->localite)
                        <span class="inline-flex items-center gap-1 text-xs text-slate-600">
                            <i class="ti ti-map-pin text-slate-400"></i>
                            {{ $policier->localite->libelle }}
                        </span>
                        @else
                        <span class="text-slate-300">—</span>
                        @endif
                    </td>

                    {{-- Statut --}}
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $policier->statut->badge() }}">
                            {{ $policier->statut->label() }}
                        </span>
                    </td>

                    {{-- Bail actif --}}
                    <td class="px-5 py-3.5 text-center hidden sm:table-cell">
                        @if($policier->contratsBail->where('statut', 'actif')->isNotEmpty())
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                <span class="h-2 w-2 rounded-full bg-green-500 inline-block"></span>
                                Actif
                            </span>
                        @else
                            <span class="text-slate-300 text-xs">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('policiers.show', $policier) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Voir la fiche">
                                <i class="ti ti-eye text-base"></i>
                            </a>
                            <a href="{{ route('policiers.edit', $policier) }}"
                               class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Modifier">
                                <i class="ti ti-pencil text-base"></i>
                            </a>
                            <form method="POST" action="{{ route('policiers.destroy', $policier) }}"
                                  onsubmit="return confirm('Supprimer {{ $policier->nom }} {{ $policier->prenoms }} ?')">
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
    @if($policiers->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3"
         style="background:#f8f9fb;">
        <p class="text-xs text-slate-500">
            Page {{ $policiers->currentPage() }} sur {{ $policiers->lastPage() }}
        </p>
        {{ $policiers->links() }}
    </div>
    @endif

    @endif
</div>

@endsection
