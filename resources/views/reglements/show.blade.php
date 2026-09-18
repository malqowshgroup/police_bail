@extends('layouts.app')
@section('title', 'Règlement #' . $reglement->id)

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
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <div class="h-14 w-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(247,127,0,.12);">
            <i class="ti ti-cash text-2xl" style="color:#F77F00;"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-slate-800">{{ $reglement->periode_label }}</h1>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $reglement->typeEnum->badge() }}">{{ $reglement->typeEnum->label() }}</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $reglement->statutEnum->badge() }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $reglement->statutEnum->dot() }} inline-block"></span>
                    {{ $reglement->statutEnum->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Contrat {{ $reglement->contratBail?->numero_contrat }}
                &bull; {{ $reglement->contratBail?->policier?->nom }} {{ $reglement->contratBail?->policier?->prenoms }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2 flex-shrink-0">
            @if($reglement->peutPasserEnAttenteVirement())
            <form method="POST" action="{{ route('reglements.en_attente', $reglement) }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#3b82f6;">
                    <i class="ti ti-clock-check text-sm"></i> En attente de virement
                </button>
            </form>
            @endif
            @if($reglement->peutEtreModifie())
            <a href="{{ route('reglements.edit', $reglement) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#F77F00;">
                <i class="ti ti-pencil text-sm"></i><span class="hidden sm:inline">Modifier</span>
            </a>
            @endif
            @if($reglement->peutEtreAnnule())
            <form method="POST" action="{{ route('reglements.annuler', $reglement) }}" onsubmit="return confirm('Annuler ce règlement ?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#dc2626;">
                    <i class="ti ti-ban text-sm"></i> Annuler
                </button>
            </form>
            @endif
            <a href="{{ route('reglements.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                <i class="ti ti-arrow-left text-sm"></i><span class="hidden sm:inline">Retour</span>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Détails --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="rounded-xl p-5 mb-5" style="background:rgba(247,127,0,.08);">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Montant</p>
                <p class="text-3xl font-bold" style="color:#F77F00;">{{ number_format($reglement->montant, 0, ',', ' ') }} <span class="text-lg">F</span></p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Période</p>
                    <p class="text-slate-700">{{ $reglement->periode_label }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Type</p>
                    <p class="text-slate-700">{{ $reglement->typeEnum->label() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Généré par</p>
                    <p class="text-slate-700">{{ $reglement->generePar?->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Créé le</p>
                    <p class="text-slate-700">{{ $reglement->created_at?->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        {{-- Infos virement --}}
        @if($reglement->isVire() || $reglement->numero_virement)
        <div class="bg-white rounded-2xl shadow-sm border border-green-200 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-building-bank mr-1.5 text-green-500"></i> Virement
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">N° de virement</p>
                    <p class="text-slate-700 font-mono">{{ $reglement->numero_virement ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Date de virement</p>
                    <p class="text-slate-700">{{ $reglement->date_virement?->format('d/m/Y') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Banque bailleur</p>
                    <p class="text-slate-700">{{ $reglement->banque_bailleur ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Réf. fichier</p>
                    <p class="text-slate-700 font-mono text-xs">{{ $reglement->reference_fichier_virement ?? '—' }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Contrat --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-file-text mr-1.5 text-slate-400"></i> Contrat
            </h2>
            @if($reglement->contratBail)
            <p class="font-mono font-semibold text-slate-800 text-sm">{{ $reglement->contratBail->numero_contrat }}</p>
            <div class="space-y-2 text-xs mt-3">
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-user text-slate-400"></i> {{ $reglement->contratBail->policier?->nom }} {{ $reglement->contratBail->policier?->prenoms }}</div>
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-award text-slate-400"></i> {{ $reglement->contratBail->policier?->grade?->libelle ?? '—' }}</div>
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-building text-slate-400"></i> {{ $reglement->contratBail->logementCivil?->reference ?? '—' }}</div>
                @if($reglement->contratBail->logementCivil?->proprietaire)
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-user-check text-slate-400"></i> {{ $reglement->contratBail->logementCivil->proprietaire->nom_complet }}</div>
                @endif
            </div>
            <a href="{{ route('contrats.show', $reglement->contratBail) }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-medium transition" style="color:#F77F00;">
                Voir le contrat <i class="ti ti-arrow-right text-xs"></i>
            </a>
            @else
            <p class="text-sm text-slate-400">Contrat introuvable.</p>
            @endif
        </div>
    </div>
</div>

@endsection
