@extends('layouts.app')
@section('title', 'Contrat ' . $contrat->numero_contrat)

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

<div x-data="{ suspendModal: false, resilierModal: false }">

{{-- Header card --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <div class="h-14 w-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(247,127,0,.12);">
            <i class="ti ti-file-text text-2xl" style="color:#F77F00;"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-slate-800 font-mono">{{ $contrat->numero_contrat }}</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $contrat->statutEnum->badge() }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $contrat->statutEnum->dot() }} inline-block"></span>
                    {{ $contrat->statutEnum->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                {{ $contrat->policier?->nom }} {{ $contrat->policier?->prenoms }}
                &bull; <i class="ti ti-building text-xs"></i> {{ $contrat->logementCivil?->reference }}
                &bull; Créé le {{ $contrat->created_at?->format('d/m/Y') }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2 flex-shrink-0">
            @if($contrat->peutEtreActive())
            <form method="POST" action="{{ route('contrats.activer', $contrat) }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                        style="background:#009A44;"
                        onclick="return confirm('Activer ce contrat ?')">
                    <i class="ti ti-player-play text-sm"></i> Activer
                </button>
            </form>
            @endif
            @if($contrat->peutEtreSuspendu())
            <button type="button" @click="suspendModal = true"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                    style="background:#f59e0b;">
                <i class="ti ti-player-pause text-sm"></i> Suspendre
            </button>
            @endif
            @if($contrat->peutEtreResilie())
            <button type="button" @click="resilierModal = true"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                    style="background:#dc2626;">
                <i class="ti ti-file-off text-sm"></i> Résilier
            </button>
            @endif
            <a href="{{ route('contrats.edit', $contrat) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
               style="background:#F77F00;">
                <i class="ti ti-pencil text-sm"></i><span class="hidden sm:inline">Modifier</span>
            </a>
            <a href="{{ route('contrats.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                <i class="ti ti-arrow-left text-sm"></i><span class="hidden sm:inline">Retour</span>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Colonne principale --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Taux + période --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-1 rounded-xl p-4" style="background:rgba(247,127,0,.08);">
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Taux mensuel</p>
                    <p class="text-2xl font-bold" style="color:#F77F00;">{{ number_format($contrat->taux_bail, 0, ',', ' ') }} F</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $contrat->grade?->libelle ?? '—' }}</p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50">
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Date de début</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $contrat->date_debut?->format('d/m/Y') ?? '—' }}</p>
                </div>
                <div class="rounded-xl p-4 bg-slate-50">
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Date de fin</p>
                    <p class="text-lg font-semibold text-slate-800">{{ $contrat->date_fin?->format('d/m/Y') ?? 'Indéterminée' }}</p>
                </div>
            </div>
        </div>

        {{-- Détails --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-info-circle mr-1.5 text-slate-400"></i> Détails du contrat
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Préavis</p>
                    <p class="text-slate-700">{{ $contrat->preavis_mois }} mois</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Bordereau</p>
                    <p class="text-slate-700">{{ $contrat->bordereau?->numero ?? $contrat->bordereau_id ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Saisi par</p>
                    <p class="text-slate-700">{{ $contrat->saisiPar?->name ?? '—' }}</p>
                </div>
                @if($contrat->date_validation)
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Validé le</p>
                    <p class="text-slate-700">{{ $contrat->date_validation->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Validé par</p>
                    <p class="text-slate-700">{{ $contrat->validePar?->name ?? '—' }}</p>
                </div>
                @endif
            </div>

            @if($contrat->avec_arrieres)
            <div class="mt-4 rounded-xl bg-amber-50 border border-amber-200 p-4">
                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-1"><i class="ti ti-alert-triangle mr-1"></i> Arriérés</p>
                <p class="text-sm text-amber-800">
                    {{ $contrat->nb_mois_arrieres }} mois d'arriérés
                    @if($contrat->date_debut_arrieres) depuis le {{ $contrat->date_debut_arrieres->format('d/m/Y') }} @endif
                    — soit {{ number_format($contrat->nb_mois_arrieres * $contrat->taux_bail, 0, ',', ' ') }} F.
                </p>
            </div>
            @endif

            @if($contrat->observations)
            <div class="mt-4">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Observations</p>
                <p class="text-sm text-slate-700">{{ $contrat->observations }}</p>
            </div>
            @endif
        </div>

        {{-- Suspension / résiliation --}}
        @if($contrat->motif_suspension || $contrat->motif_resiliation)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-history mr-1.5 text-slate-400"></i> Événements
            </h2>
            <div class="space-y-3 text-sm">
                @if($contrat->motif_suspension)
                <div class="flex items-start gap-3">
                    <span class="h-2 w-2 rounded-full bg-amber-500 mt-1.5"></span>
                    <div>
                        <p class="font-medium text-slate-700">Suspension @if($contrat->date_suspension) — {{ $contrat->date_suspension->format('d/m/Y') }} @endif</p>
                        <p class="text-slate-500">{{ $contrat->motif_suspension }}</p>
                        @if($contrat->date_levee_suspension)
                        <p class="text-xs text-green-600 mt-0.5">Levée le {{ $contrat->date_levee_suspension->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>
                @endif
                @if($contrat->motif_resiliation)
                <div class="flex items-start gap-3">
                    <span class="h-2 w-2 rounded-full bg-red-500 mt-1.5"></span>
                    <div>
                        <p class="font-medium text-slate-700">Résiliation @if($contrat->date_resiliation) — {{ $contrat->date_resiliation->format('d/m/Y') }} @endif</p>
                        <p class="text-slate-500">{{ $contrat->motif_resiliation }}</p>
                        @if($contrat->date_fin_preavis)
                        <p class="text-xs text-slate-500 mt-0.5">Fin du préavis : {{ $contrat->date_fin_preavis->format('d/m/Y') }}</p>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

    </div>

    {{-- Colonne latérale --}}
    <div class="space-y-4">

        {{-- Policier --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-user mr-1.5 text-slate-400"></i> Policier
            </h2>
            @if($contrat->policier)
            <div class="flex items-start gap-3 mb-3">
                <div class="h-10 w-10 rounded-xl flex items-center justify-center text-white text-sm font-bold flex-shrink-0" style="background:#1a2440;">
                    {{ mb_strtoupper(mb_substr($contrat->policier->nom, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-800 text-sm">{{ $contrat->policier->nom }} {{ $contrat->policier->prenoms }}</p>
                    <p class="text-xs text-slate-500">{{ $contrat->policier->grade?->libelle ?? '—' }}</p>
                </div>
            </div>
            <div class="space-y-2 text-xs">
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-id text-slate-400"></i> {{ $contrat->policier->matricule }}</div>
                @if($contrat->policier->service)
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-briefcase text-slate-400"></i> {{ $contrat->policier->service->libelle ?? $contrat->policier->service->nom ?? '—' }}</div>
                @endif
            </div>
            <a href="{{ route('policiers.show', $contrat->policier) }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-medium transition" style="color:#F77F00;">
                Voir la fiche policier <i class="ti ti-arrow-right text-xs"></i>
            </a>
            @else
            <p class="text-sm text-slate-400">Policier introuvable.</p>
            @endif
        </div>

        {{-- Logement --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-building mr-1.5 text-slate-400"></i> Logement
            </h2>
            @if($contrat->logementCivil)
            <p class="font-mono font-semibold text-slate-800 text-sm">{{ $contrat->logementCivil->reference }}</p>
            <div class="space-y-2 text-xs mt-3">
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-map-pin text-slate-400"></i> {{ $contrat->logementCivil->quartier }}{{ $contrat->logementCivil->localite ? ', '.$contrat->logementCivil->localite->libelle : '' }}</div>
                @if($contrat->logementCivil->proprietaire)
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-user-check text-slate-400"></i> {{ $contrat->logementCivil->proprietaire->nom_complet }}</div>
                @endif
            </div>
            <a href="{{ route('logements.show', $contrat->logementCivil) }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-medium transition" style="color:#F77F00;">
                Voir le logement <i class="ti ti-arrow-right text-xs"></i>
            </a>
            @else
            <p class="text-sm text-slate-400">Logement introuvable.</p>
            @endif
        </div>

        {{-- Suppression --}}
        @if($contrat->statut === 'en_attente')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-3">Zone de gestion</h2>
            <form method="POST" action="{{ route('contrats.destroy', $contrat) }}" onsubmit="return confirm('Supprimer définitivement ce contrat en attente ?')">
                @csrf @method('DELETE')
                <button type="submit" class="flex items-center gap-2 w-full px-4 py-2.5 rounded-xl text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 transition">
                    <i class="ti ti-trash text-sm"></i> Supprimer le contrat
                </button>
            </form>
        </div>
        @endif

    </div>
</div>

{{-- Modal suspendre --}}
<div x-show="suspendModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);" @click.self="suspendModal = false">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
        <h3 class="text-lg font-bold text-slate-800 mb-1">Suspendre le contrat</h3>
        <p class="text-sm text-slate-500 mb-4">Indiquez le motif de la suspension de {{ $contrat->numero_contrat }}.</p>
        <form method="POST" action="{{ route('contrats.suspendre', $contrat) }}">
            @csrf
            <textarea name="motif_suspension" rows="3" required placeholder="Motif de suspension…"
                      class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" @click="suspendModal = false" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#f59e0b;">Suspendre</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal résilier --}}
<div x-show="resilierModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);" @click.self="resilierModal = false">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
        <h3 class="text-lg font-bold text-slate-800 mb-1">Résilier le contrat</h3>
        <p class="text-sm text-slate-500 mb-4">Cette action clôt définitivement {{ $contrat->numero_contrat }}.</p>
        <form method="POST" action="{{ route('contrats.resilier', $contrat) }}">
            @csrf
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Préavis (mois)</label>
            <input type="number" name="preavis_mois" value="{{ $contrat->preavis_mois ?? 3 }}" min="0" max="24"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 mb-3">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Motif de résiliation</label>
            <textarea name="motif_resiliation" rows="3" required placeholder="Motif de résiliation…"
                      class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" @click="resilierModal = false" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#dc2626;">Résilier</button>
            </div>
        </form>
    </div>
</div>

</div>

@endsection
