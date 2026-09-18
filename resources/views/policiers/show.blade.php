@extends('layouts.app')

@section('title', $policier->nom . ' ' . $policier->prenoms)

@section('content')

{{-- Flash --}}
@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-circle-check text-lg text-green-500"></i>
    {{ session('success') }}
</div>
@endif

{{-- ── HEADER CARD ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">

        {{-- Avatar --}}
        <div class="h-16 w-16 rounded-2xl flex items-center justify-center text-white text-xl font-bold flex-shrink-0"
             style="background:#F77F00;">
            {{ mb_strtoupper(mb_substr($policier->nom, 0, 1)) }}{{ mb_strtoupper(mb_substr($policier->prenoms, 0, 1)) }}
        </div>

        {{-- Identity --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-slate-800">
                {{ $policier->nom }} {{ $policier->prenoms }}
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ $policier->grade?->libelle ?? '—' }}
                &bull;
                <span class="font-mono text-xs bg-slate-100 px-2 py-0.5 rounded">{{ $policier->matricule }}</span>
            </p>
        </div>

        {{-- Statut badge --}}
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $policier->statut->badge() }}">
            {{ $policier->statut->label() }}
        </span>

        {{-- Actions --}}
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('policiers.edit', $policier) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
               style="background:#F77F00;">
                <i class="ti ti-pencil text-sm"></i>
                <span class="hidden sm:inline">Modifier</span>
            </a>
            <a href="{{ route('policiers.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                <i class="ti ti-arrow-left text-sm"></i>
                <span class="hidden sm:inline">Retour</span>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- ── INFO GRID ────────────────────────────────────────────────── --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-id-badge mr-1.5 text-slate-400"></i> Informations générales
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Matricule</p>
                    <p class="font-mono font-semibold text-slate-800">{{ $policier->matricule }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Sexe</p>
                    <p class="text-slate-700">{{ $policier->sexe === 'M' ? 'Masculin' : 'Féminin' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Grade</p>
                    <p class="text-slate-700">{{ $policier->grade?->libelle ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Service</p>
                    <p class="text-slate-700">{{ $policier->service?->libelle ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Localité</p>
                    <p class="text-slate-700">{{ $policier->localite?->libelle ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Statut</p>
                    <p class="text-slate-700">{{ $policier->statut->label() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Date de naissance</p>
                    <p class="text-slate-700">
                        {{ $policier->date_naissance ? \Carbon\Carbon::parse($policier->date_naissance)->format('d/m/Y') : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Date prise de service</p>
                    <p class="text-slate-700">
                        {{ $policier->date_prise_service ? \Carbon\Carbon::parse($policier->date_prise_service)->format('d/m/Y') : '—' }}
                    </p>
                </div>
                <div class="sm:col-span-2">
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Propriétaire du logement</p>
                    <p class="text-slate-700">{{ $policier->proprietaire_logement ? 'Oui' : 'Non' }}</p>
                </div>
            </div>
        </div>

        {{-- Documents --}}
        @include('documents._panel_entite', [
            'entiteType' => 'policier',
            'entiteId'   => $policier->id,
            'liens'      => $policier->documentLiens,
        ])

        {{-- Historique des contrats --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-file-text mr-1.5 text-slate-400"></i> Historique des contrats de bail
            </h2>
            @if($policier->contratsBail->isEmpty())
            <p class="text-sm text-slate-400 py-4 text-center">Aucun contrat enregistré.</p>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-2 pr-4">Début</th>
                            <th class="pb-2 pr-4">Logement</th>
                            <th class="pb-2 pr-4">Taux bail</th>
                            <th class="pb-2">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($policier->contratsBail as $contrat)
                        <tr>
                            <td class="py-2 pr-4 text-slate-600">
                                {{ $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="py-2 pr-4 text-slate-700">
                                {{ $contrat->logementCivil?->adresse ?? '—' }}
                            </td>
                            <td class="py-2 pr-4 text-slate-600">
                                {{ $contrat->taux_bail ? number_format($contrat->taux_bail, 0, ',', ' ') . ' F' : '—' }}
                            </td>
                            <td class="py-2">
                                @if($contrat->statut === 'actif')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Actif</span>
                                @elseif($contrat->statut === 'resilie')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Résilié</span>
                                @elseif($contrat->statut === 'suspendu')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Suspendu</span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">{{ ucfirst($contrat->statut) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>

    {{-- ── BAIL ACTIF SIDEBAR ───────────────────────────────────────── --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-home mr-1.5 text-slate-400"></i> Bail actif
            </h2>
            @if($contratActif)
            <div class="space-y-3 text-sm">
                <div class="flex items-start gap-3 p-3 rounded-xl bg-green-50 border border-green-100">
                    <i class="ti ti-circle-check text-green-500 text-lg mt-0.5"></i>
                    <div>
                        <p class="font-semibold text-slate-800">Logement actif</p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $contratActif->logementCivil?->adresse ?? 'Adresse non renseignée' }}
                        </p>
                    </div>
                </div>
                @if($contratActif->montant_loyer)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Montant loyer</p>
                    <p class="text-lg font-bold text-slate-800">
                        {{ number_format($contratActif->montant_loyer, 0, ',', ' ') }} F CFA
                    </p>
                </div>
                @endif
                @if($contratActif->date_debut)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Depuis</p>
                    <p class="text-slate-700">{{ \Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y') }}</p>
                </div>
                @endif
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-6 text-slate-400">
                <i class="ti ti-home-off text-3xl mb-2"></i>
                <p class="text-sm text-center">Aucun bail actif</p>
                <p class="text-xs text-center mt-1">Ce policier ne dispose pas de contrat de bail en cours.</p>
            </div>
            @endif
        </div>
    </div>

</div>

@endsection
