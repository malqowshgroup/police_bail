@extends('layouts.app')

@section('title', $proprietaire->nom_complet)

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
             style="background: {{ $proprietaire->type_personne === 'physique' ? '#F77F00' : '#1a2440' }};">
            {{ mb_strtoupper(mb_substr($proprietaire->nom, 0, 1)) }}
        </div>

        {{-- Identity --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-slate-800">
                {{ $proprietaire->nom_complet }}
            </h1>
            <p class="text-sm text-slate-500 mt-0.5">
                {{ $proprietaire->localite?->libelle ?? 'Localité non renseignée' }}
            </p>
        </div>

        {{-- Badges --}}
        <div class="flex items-center gap-2 flex-shrink-0 flex-wrap">
            @if($proprietaire->type_personne === 'physique')
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                <i class="ti ti-user mr-1"></i> Physique
            </span>
            @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                <i class="ti ti-building mr-1"></i> Morale
            </span>
            @endif

            @if($proprietaire->actif)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                <span class="h-1.5 w-1.5 rounded-full bg-green-500 mr-1.5 inline-block"></span> Actif
            </span>
            @else
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                <span class="h-1.5 w-1.5 rounded-full bg-red-500 mr-1.5 inline-block"></span> Inactif
            </span>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex gap-2 flex-shrink-0">
            <a href="{{ route('proprietaires.edit', $proprietaire) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
               style="background:#F77F00;">
                <i class="ti ti-pencil text-sm"></i>
                <span class="hidden sm:inline">Modifier</span>
            </a>
            <a href="{{ route('proprietaires.index') }}"
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
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Type</p>
                    <p class="text-slate-700">{{ $proprietaire->type_personne === 'physique' ? 'Personne physique' : 'Personne morale' }}</p>
                </div>

                @if($proprietaire->raison_sociale)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Raison sociale</p>
                    <p class="font-semibold text-slate-800">{{ $proprietaire->raison_sociale }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Nom</p>
                    <p class="text-slate-700">{{ $proprietaire->nom }}</p>
                </div>

                @if($proprietaire->prenoms)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Prénoms</p>
                    <p class="text-slate-700">{{ $proprietaire->prenoms }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Type de pièce</p>
                    <p class="text-slate-700">{{ $proprietaire->type_piece_libelle }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">N° Pièce</p>
                    <p class="font-mono text-slate-800">{{ $proprietaire->num_piece_identite }}</p>
                </div>

                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Téléphone</p>
                    <p class="text-slate-700">{{ $proprietaire->telephone }}</p>
                </div>

                @if($proprietaire->telephone2)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Téléphone 2</p>
                    <p class="text-slate-700">{{ $proprietaire->telephone2 }}</p>
                </div>
                @endif

                @if($proprietaire->email)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Email</p>
                    <p class="text-slate-700">{{ $proprietaire->email }}</p>
                </div>
                @endif

                @if($proprietaire->adresse_postale)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Adresse postale</p>
                    <p class="text-slate-700">{{ $proprietaire->adresse_postale }}</p>
                </div>
                @endif

                @if($proprietaire->num_compte_contribuable)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">N° Contribuable</p>
                    <p class="font-mono text-slate-800">{{ $proprietaire->num_compte_contribuable }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Localité</p>
                    <p class="text-slate-700">{{ $proprietaire->localite?->libelle ?? '—' }}</p>
                </div>

            </div>
        </div>

        {{-- Documents --}}
        @include('documents._panel_entite', [
            'entiteType' => 'proprietaire',
            'entiteId'   => $proprietaire->id,
            'liens'      => $proprietaire->documentLiens,
        ])

        {{-- Logements gérés --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-home mr-1.5 text-slate-400"></i> Logements gérés
                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                    {{ $proprietaire->logementsCivils->count() }}
                </span>
            </h2>
            @if($proprietaire->logementsCivils->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                <i class="ti ti-home-off text-3xl mb-2"></i>
                <p class="text-sm text-center">Aucun logement enregistré pour ce propriétaire.</p>
            </div>
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-500 uppercase tracking-wider border-b border-gray-100">
                            <th class="pb-2 pr-4">Référence</th>
                            <th class="pb-2 pr-4">Adresse / Quartier</th>
                            <th class="pb-2 pr-4">Localité</th>
                            <th class="pb-2 text-center">Bail</th>
                            <th class="pb-2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($proprietaire->logementsCivils as $logement)
                        <tr>
                            <td class="py-2.5 pr-4">
                                <span class="font-mono text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded">
                                    {{ $logement->reference ?? 'LOG-' . $logement->id }}
                                </span>
                            </td>
                            <td class="py-2.5 pr-4 text-slate-700">
                                {{ $logement->adresse ?? $logement->quartier ?? '—' }}
                            </td>
                            <td class="py-2.5 pr-4 text-slate-600 text-xs">
                                {{ $logement->localite?->libelle ?? '—' }}
                            </td>
                            <td class="py-2.5 pr-4 text-center">
                                @php $bailActif = isset($logement->statut_bail) && $logement->statut_bail === 'actif'; @endphp
                                @if($bailActif)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-green-700">
                                    <span class="h-2 w-2 rounded-full bg-green-500 inline-block"></span> Actif
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 text-xs text-slate-400">
                                    <span class="h-2 w-2 rounded-full bg-gray-300 inline-block"></span> —
                                </span>
                                @endif
                            </td>
                            <td class="py-2.5 text-right">
                                <a href="{{ route('logements.index') }}"
                                   class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 transition">
                                    <i class="ti ti-eye text-xs"></i> Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

    </div>

    {{-- ── SIDEBAR ──────────────────────────────────────────────────── --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-info-circle mr-1.5 text-slate-400"></i> Résumé
            </h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Total logements</span>
                    <span class="font-bold text-slate-800">{{ $proprietaire->logementsCivils->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Statut</span>
                    @if($proprietaire->actif)
                    <span class="text-green-700 font-medium">Actif</span>
                    @else
                    <span class="text-red-700 font-medium">Inactif</span>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-500">Enregistré le</span>
                    <span class="text-slate-700">{{ $proprietaire->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection
