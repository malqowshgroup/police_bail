@extends('layouts.app')
@section('title', 'Contrats de bail')

@section('content')

@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-circle-check text-lg text-green-500"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-alert-circle text-lg text-red-500"></i> {{ session('error') }}
</div>
@endif

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Contrats de bail</h1>
        <p class="text-sm text-slate-500 mt-0.5">Gestion des baux des policiers — {{ number_format($stats['total']) }} contrats</p>
    </div>
    <a href="{{ route('contrats.create') }}"
       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0"
       style="background:#F77F00;">
        <i class="ti ti-plus text-base"></i> Nouveau contrat
    </a>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(26,36,64,.10);">
            <i class="ti ti-file-text text-2xl" style="color:#1a2440;"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['total']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(0,154,68,.12);">
            <i class="ti ti-file-check text-2xl" style="color:#009A44;"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Actifs</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['actifs']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(148,163,184,.18);">
            <i class="ti ti-clock text-2xl text-slate-500"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">En attente</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['attente']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,.14);">
            <i class="ti ti-player-pause text-2xl text-amber-500"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Suspendus</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['suspendus']) }}</p>
        </div>
    </div>
</div>

{{-- Filtres --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('contrats.index') }}">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 min-w-0">
                <div class="relative">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="N° contrat, policier, matricule, logement…"
                           class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
            </div>
            <div class="w-full md:w-48">
                <select name="statut" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $st)
                    <option value="{{ $st['value'] }}" {{ request('statut') === $st['value'] ? 'selected' : '' }}>{{ $st['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#1a2440;">
                    <i class="ti ti-search text-sm"></i><span class="hidden sm:inline ml-1">Filtrer</span>
                </button>
                @if(request()->hasAny(['search','statut']))
                <a href="{{ route('contrats.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                    <i class="ti ti-x text-sm"></i>
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between" style="background:#f8f9fb;">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            @if($contrats->total() > 0)
                {{ $contrats->firstItem() }}–{{ $contrats->lastItem() }} sur {{ number_format($contrats->total()) }} résultats
            @else Aucun résultat @endif
        </span>
        @if(request()->hasAny(['search','statut']))
        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
            <i class="ti ti-filter text-xs"></i> Filtre actif
        </span>
        @endif
    </div>

    @if($contrats->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <i class="ti ti-file-text text-3xl text-slate-300"></i>
        </div>
        <p class="text-base font-semibold text-slate-600">Aucun contrat trouvé</p>
        <p class="text-sm mt-1">Modifiez vos filtres ou créez un nouveau contrat.</p>
        <a href="{{ route('contrats.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white" style="background:#F77F00;">
            <i class="ti ti-plus text-sm"></i> Nouveau contrat
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100" style="background:#f8f9fb;">
                    <th class="px-5 py-3">N° Contrat</th>
                    <th class="px-5 py-3">Policier</th>
                    <th class="px-5 py-3 hidden md:table-cell">Logement</th>
                    <th class="px-5 py-3 hidden lg:table-cell">Début</th>
                    <th class="px-5 py-3 text-right">Taux mensuel</th>
                    <th class="px-5 py-3 text-center">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($contrats as $contrat)
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">
                            {{ $contrat->numero_contrat }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center text-white text-xs font-bold flex-shrink-0" style="background:#1a2440;">
                                {{ mb_strtoupper(mb_substr($contrat->policier?->nom ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800">{{ $contrat->policier?->nom }} {{ $contrat->policier?->prenoms }}</div>
                                <div class="text-xs text-slate-500">{{ $contrat->policier?->grade?->libelle ?? '—' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <span class="font-mono text-xs text-slate-600">{{ $contrat->logementCivil?->reference ?? '—' }}</span>
                        <div class="text-xs text-slate-400">{{ $contrat->logementCivil?->quartier }}</div>
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell text-xs text-slate-600">
                        {{ $contrat->date_debut?->format('d/m/Y') ?? '—' }}
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-slate-700">
                        {{ number_format($contrat->taux_bail, 0, ',', ' ') }} F
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $contrat->statutEnum->badge() }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $contrat->statutEnum->dot() }} inline-block"></span>
                            {{ $contrat->statutEnum->label() }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('contrats.show', $contrat) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Voir">
                                <i class="ti ti-eye text-base"></i>
                            </a>
                            <a href="{{ route('contrats.edit', $contrat) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Modifier">
                                <i class="ti ti-pencil text-base"></i>
                            </a>
                            @if($contrat->statut === 'en_attente')
                            <form method="POST" action="{{ route('contrats.destroy', $contrat) }}"
                                  onsubmit="return confirm('Supprimer le contrat {{ addslashes($contrat->numero_contrat) }} ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 transition" title="Supprimer">
                                    <i class="ti ti-trash text-base"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($contrats->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3" style="background:#f8f9fb;">
        <p class="text-xs text-slate-500">Page {{ $contrats->currentPage() }} sur {{ $contrats->lastPage() }}</p>
        {{ $contrats->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
