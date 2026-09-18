@extends('layouts.app')
@section('title', 'Règlements')

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

@php
    $moisLabels = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
@endphp

<div x-data="{ genererModal: false }">

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Règlements</h1>
        <p class="text-sm text-slate-500 mt-0.5">Échéances de loyer des contrats actifs — {{ number_format($stats['total']) }} règlements</p>
    </div>
    <div class="flex gap-2 flex-shrink-0">
        <button type="button" @click="genererModal = true"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90"
                style="background:#1a2440;">
            <i class="ti ti-calendar-plus text-base"></i> Générer les loyers
        </button>
        <a href="{{ route('reglements.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90"
           style="background:#F77F00;">
            <i class="ti ti-plus text-base"></i> Ajouter
        </a>
    </div>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(26,36,64,.10);">
            <i class="ti ti-cash text-2xl" style="color:#1a2440;"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['total']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,.14);">
            <i class="ti ti-clock-dollar text-2xl text-amber-500"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">À payer</p>
            <p class="text-xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['montant_a_payer'], 0, ',', ' ') }} F</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(59,130,246,.12);">
            <i class="ti ti-clock text-2xl text-blue-500"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Attente virement</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['attente_virement']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(0,154,68,.12);">
            <i class="ti ti-circle-check text-2xl" style="color:#009A44;"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total viré</p>
            <p class="text-xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['montant_vire'], 0, ',', ' ') }} F</p>
        </div>
    </div>
</div>

{{-- Filtres --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('reglements.index') }}">
        <div class="flex flex-col md:flex-row gap-3 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Contrat, policier, matricule…"
                           class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
            </div>
            <select name="statut" class="w-full md:w-44 px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Tous les statuts</option>
                @foreach($statuts as $st)
                <option value="{{ $st['value'] }}" {{ request('statut') === $st['value'] ? 'selected' : '' }}>{{ $st['label'] }}</option>
                @endforeach
            </select>
            <select name="type" class="w-full md:w-40 px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Tous les types</option>
                @foreach($types as $t)
                <option value="{{ $t['value'] }}" {{ request('type') === $t['value'] ? 'selected' : '' }}>{{ $t['label'] }}</option>
                @endforeach
            </select>
            <select name="mois" class="w-full md:w-36 px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Mois</option>
                @foreach($moisLabels as $num => $lib)
                <option value="{{ $num }}" {{ request('mois') == $num ? 'selected' : '' }}>{{ $lib }}</option>
                @endforeach
            </select>
            <select name="annee" class="w-full md:w-28 px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Année</option>
                @foreach($annees as $a)
                <option value="{{ $a }}" {{ request('annee') == $a ? 'selected' : '' }}>{{ $a }}</option>
                @endforeach
            </select>
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#1a2440;">
                    <i class="ti ti-search text-sm"></i>
                </button>
                @if(request()->hasAny(['search','statut','type','mois','annee']))
                <a href="{{ route('reglements.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
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
            @if($reglements->total() > 0)
                {{ $reglements->firstItem() }}–{{ $reglements->lastItem() }} sur {{ number_format($reglements->total()) }} résultats
            @else Aucun résultat @endif
        </span>
    </div>

    @if($reglements->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <i class="ti ti-cash text-3xl text-slate-300"></i>
        </div>
        <p class="text-base font-semibold text-slate-600">Aucun règlement trouvé</p>
        <p class="text-sm mt-1">Générez les loyers d'une période ou ajoutez un règlement.</p>
        <button type="button" @click="genererModal = true" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white" style="background:#1a2440;">
            <i class="ti ti-calendar-plus text-sm"></i> Générer les loyers
        </button>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100" style="background:#f8f9fb;">
                    <th class="px-5 py-3">Contrat / Policier</th>
                    <th class="px-5 py-3 hidden md:table-cell">Période</th>
                    <th class="px-5 py-3 hidden lg:table-cell">Type</th>
                    <th class="px-5 py-3 text-right">Montant</th>
                    <th class="px-5 py-3 text-center">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($reglements as $reglement)
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('contrats.show', $reglement->contratBail) }}" class="font-mono text-xs font-semibold text-blue-600 hover:underline">{{ $reglement->contratBail?->numero_contrat }}</a>
                        <div class="text-xs text-slate-500">{{ $reglement->contratBail?->policier?->nom }} {{ $reglement->contratBail?->policier?->prenoms }}</div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-slate-600">{{ $reglement->periode_label }}</td>
                    <td class="px-5 py-3.5 hidden lg:table-cell">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $reglement->typeEnum->badge() }}">{{ $reglement->typeEnum->label() }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-slate-700">{{ number_format($reglement->montant, 0, ',', ' ') }} F</td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $reglement->statutEnum->badge() }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $reglement->statutEnum->dot() }} inline-block"></span>
                            {{ $reglement->statutEnum->label() }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('reglements.show', $reglement) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Voir">
                                <i class="ti ti-eye text-base"></i>
                            </a>
                            @if($reglement->peutPasserEnAttenteVirement())
                            <form method="POST" action="{{ route('reglements.en_attente', $reglement) }}" title="Mettre en attente de virement">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition">
                                    <i class="ti ti-clock-check text-base"></i>
                                </button>
                            </form>
                            @endif
                            @if($reglement->peutEtreModifie())
                            <a href="{{ route('reglements.edit', $reglement) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Modifier">
                                <i class="ti ti-pencil text-base"></i>
                            </a>
                            @endif
                            @if($reglement->peutEtreAnnule())
                            <form method="POST" action="{{ route('reglements.annuler', $reglement) }}" onsubmit="return confirm('Annuler ce règlement ?')" title="Annuler">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 transition">
                                    <i class="ti ti-ban text-base"></i>
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
    @if($reglements->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3" style="background:#f8f9fb;">
        <p class="text-xs text-slate-500">Page {{ $reglements->currentPage() }} sur {{ $reglements->lastPage() }}</p>
        {{ $reglements->links() }}
    </div>
    @endif
    @endif
</div>

{{-- Modal génération mensuelle --}}
<div x-show="genererModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);" @click.self="genererModal = false">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
        <h3 class="text-lg font-bold text-slate-800 mb-1">Générer les loyers mensuels</h3>
        <p class="text-sm text-slate-500 mb-4">Crée un loyer pour chaque contrat actif non encore couvert sur la période choisie.</p>
        <form method="POST" action="{{ route('reglements.generer') }}">
            @csrf
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mois</label>
                    <select name="periode_mois" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                        @foreach($moisLabels as $num => $lib)
                        <option value="{{ $num }}" {{ $moisCourant == $num ? 'selected' : '' }}>{{ $lib }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Année</label>
                    <input type="number" name="periode_annee" value="{{ $anneeCourante }}" min="2020" max="2100"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" @click="genererModal = false" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#1a2440;">
                    <i class="ti ti-calendar-plus mr-1"></i> Générer
                </button>
            </div>
        </form>
    </div>
</div>

</div>

@endsection
