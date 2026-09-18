@extends('layouts.app')
@section('title', 'Logement ' . $logement->reference)

@section('content')

@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-circle-check text-lg text-green-500"></i> {{ session('success') }}
</div>
@endif

{{-- Header card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">

        <div class="h-14 w-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(247,127,0,.12);">
            <i class="ti ti-building text-2xl" style="color:#F77F00;"></i>
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-slate-800 font-mono">{{ $logement->reference }}</h1>
                @if($contratActif)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500 inline-block"></span> Occupé
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">Libre</span>
                @endif
            </div>
            <p class="text-sm text-slate-500 mt-1">
                {{ $logement->quartier }}
                @if($logement->ilot) — Îlot {{ $logement->ilot }} @endif
                @if($logement->lot) — Lot {{ $logement->lot }} @endif
                &bull; <i class="ti ti-map-pin text-xs"></i> {{ $logement->localite?->libelle ?? '—' }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2 flex-shrink-0">
            @if(!$contratActif)
            <a href="{{ route('contrats.create', ['logement_civil_id' => $logement->id]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
               style="background:#009A44;">
                <i class="ti ti-file-plus text-sm"></i>
                <span class="hidden sm:inline">Créer un contrat</span>
            </a>
            @endif
            <a href="{{ route('logements.edit', $logement) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
               style="background:#F77F00;">
                <i class="ti ti-pencil text-sm"></i>
                <span class="hidden sm:inline">Modifier</span>
            </a>
            <a href="{{ route('logements.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                <i class="ti ti-arrow-left text-sm"></i>
                <span class="hidden sm:inline">Retour</span>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Infos principales --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Détails logement --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-info-circle mr-1.5 text-slate-400"></i> Informations générales
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Référence</p>
                    <p class="font-mono font-semibold text-slate-800">{{ $logement->reference }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Localité</p>
                    <p class="text-slate-700">{{ $logement->localite?->libelle ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Quartier</p>
                    <p class="text-slate-700">{{ $logement->quartier }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Îlot</p>
                    <p class="text-slate-700">{{ $logement->ilot ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Lot</p>
                    <p class="text-slate-700">{{ $logement->lot ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Statut</p>
                    @if($logement->actif)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Actif</span>
                    @else
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Inactif</span>
                    @endif
                </div>
                @if($logement->adresse_complete)
                <div class="sm:col-span-2 md:col-span-3">
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Adresse complète</p>
                    <p class="text-slate-700">{{ $logement->adresse_complete }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Contrat actif --}}
        @if($contratActif)
        <div class="bg-white rounded-2xl shadow-sm border border-green-200 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-file-check mr-1.5 text-green-500"></i> Contrat de bail actif
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Policier bénéficiaire</p>
                    <p class="font-semibold text-slate-800">
                        {{ $contratActif->policier?->nom }} {{ $contratActif->policier?->prenoms }}
                    </p>
                    <p class="text-xs text-slate-500">{{ $contratActif->policier?->grade?->libelle ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Taux de bail</p>
                    <p class="text-2xl font-bold" style="color:#F77F00;">
                        {{ $contratActif->taux_bail ? number_format($contratActif->taux_bail, 0, ',', ' ').' F' : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Date début</p>
                    <p class="text-slate-700">{{ $contratActif->date_debut ? \Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y') : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Date fin prévue</p>
                    <p class="text-slate-700">{{ $contratActif->date_fin ? \Carbon\Carbon::parse($contratActif->date_fin)->format('d/m/Y') : 'Indéterminée' }}</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Documents --}}
        @include('documents._panel_entite', [
            'entiteType' => 'logement_civil',
            'entiteId'   => $logement->id,
            'liens'      => $logement->documentLiens,
        ])

        {{-- Historique contrats --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-history mr-1.5 text-slate-400"></i> Historique des contrats
            </h2>
            @if($logement->contratsBail->isEmpty())
            <p class="text-sm text-slate-400 py-4 text-center">Aucun contrat enregistré pour ce logement.</p>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-2 pr-4">Policier</th>
                            <th class="pb-2 pr-4">Début</th>
                            <th class="pb-2 pr-4">Taux</th>
                            <th class="pb-2">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($logement->contratsBail as $contrat)
                        <tr>
                            <td class="py-2.5 pr-4 font-medium text-slate-700">
                                {{ $contrat->policier?->nom }} {{ $contrat->policier?->prenoms }}
                                <div class="text-xs text-slate-400">{{ $contrat->policier?->grade?->libelle ?? '—' }}</div>
                            </td>
                            <td class="py-2.5 pr-4 text-slate-600 text-xs">
                                {{ $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : '—' }}
                            </td>
                            <td class="py-2.5 pr-4 text-slate-600 text-xs">
                                {{ $contrat->taux_bail ? number_format($contrat->taux_bail, 0, ',', ' ').' F' : '—' }}
                            </td>
                            <td class="py-2.5">
                                @if($contrat->statut === 'actif')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Actif</span>
                                @elseif($contrat->statut === 'resilie')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Résilié</span>
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

    {{-- Panneau latéral propriétaire --}}
    <div class="space-y-4">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-user-check mr-1.5 text-slate-400"></i> Propriétaire
            </h2>
            @if($logement->proprietaire)
            <div class="flex items-start gap-3 mb-4">
                <div class="h-10 w-10 rounded-xl flex items-center justify-center text-white text-sm font-bold flex-shrink-0"
                     style="background: {{ $logement->proprietaire->type_personne === 'morale' ? '#6366f1' : '#F77F00' }};">
                    {{ mb_strtoupper(mb_substr($logement->proprietaire->nom, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">{{ $logement->proprietaire->nom_complet }}</p>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $logement->proprietaire->type_personne === 'morale' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ $logement->proprietaire->type_personne === 'morale' ? 'Personne morale' : 'Personne physique' }}
                    </span>
                </div>
            </div>
            <div class="space-y-2 text-xs">
                <div class="flex items-center gap-2 text-slate-600">
                    <i class="ti ti-phone text-slate-400"></i>
                    {{ $logement->proprietaire->telephone }}
                </div>
                @if($logement->proprietaire->email)
                <div class="flex items-center gap-2 text-slate-600">
                    <i class="ti ti-mail text-slate-400"></i>
                    {{ $logement->proprietaire->email }}
                </div>
                @endif
                @if($logement->proprietaire->localite)
                <div class="flex items-center gap-2 text-slate-600">
                    <i class="ti ti-map-pin text-slate-400"></i>
                    {{ $logement->proprietaire->localite->libelle }}
                </div>
                @endif
            </div>
            <a href="{{ route('proprietaires.show', $logement->proprietaire) }}"
               class="mt-4 inline-flex items-center gap-1.5 text-xs font-medium transition"
               style="color:#F77F00;">
                Voir la fiche propriétaire <i class="ti ti-arrow-right text-xs"></i>
            </a>
            @else
            <p class="text-sm text-slate-400">Aucun propriétaire associé.</p>
            @endif
        </div>

        {{-- Actions rapides --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-3">Actions</h2>
            <div class="space-y-2">
                @if(!$contratActif)
                <a href="{{ route('contrats.create', ['logement_civil_id' => $logement->id]) }}"
                   class="flex items-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-medium text-white transition hover:opacity-90"
                   style="background:#009A44;">
                    <i class="ti ti-file-plus text-sm"></i> Créer un contrat de bail
                </a>
                @endif
                <a href="{{ route('logements.edit', $logement) }}"
                   class="flex items-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-medium text-white transition hover:opacity-90"
                   style="background:#F77F00;">
                    <i class="ti ti-pencil text-sm"></i> Modifier le logement
                </a>
                <form method="POST" action="{{ route('logements.destroy', $logement) }}"
                      onsubmit="return confirm('Supprimer ce logement ?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 transition">
                        <i class="ti ti-trash text-sm"></i> Supprimer le logement
                    </button>
                </form>
            </div>
        </div>

        {{-- Disponibilité --}}
        @if(!$contratActif)
        <div class="bg-green-50 rounded-2xl border border-green-200 p-5 flex items-start gap-3">
            <i class="ti ti-home-check text-xl text-green-600 flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-sm font-semibold text-green-800">Logement disponible</p>
                <p class="text-xs text-green-700 mt-0.5">Ce logement est libre et peut être attribué à un policier.</p>
                <a href="{{ route('contrats.create', ['logement_civil_id' => $logement->id]) }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 hover:text-green-900 mt-2 underline">
                    Attribuer ce logement <i class="ti ti-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
        @endif

    </div>
</div>

@endsection
