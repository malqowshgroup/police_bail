@extends('layouts.app')
@section('title', 'Virement ' . $virement->numero)

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
    $etapes = [
        ['statut' => 'en_preparation', 'label' => 'Préparation', 'icon' => 'ti-edit'],
        ['statut' => 'emis',           'label' => 'Émis',        'icon' => 'ti-send'],
        ['statut' => 'execute',        'label' => 'Exécuté',     'icon' => 'ti-circle-check'],
    ];
    $ordre = array_search($virement->statut, array_column($etapes, 'statut'));
    $annule = $virement->statut === 'annule';
@endphp

{{-- Header --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <div class="h-14 w-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(247,127,0,.12);">
            <i class="ti ti-arrows-transfer-down text-2xl" style="color:#F77F00;"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-slate-800 font-mono">{{ $virement->numero }}</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $virement->statutEnum->badge() }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $virement->statutEnum->dot() }} inline-block"></span>
                    {{ $virement->statutEnum->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                {{ $virement->proprietaire?->nom_complet }}
                &bull; {{ $virement->reglements->count() }} règlement(s)
                &bull; <span class="font-semibold">{{ number_format($virement->montant_total, 0, ',', ' ') }} F</span>
            </p>
        </div>
        <div class="flex flex-wrap gap-2 flex-shrink-0">
            @if($virement->peutEtreEmis())
            <form method="POST" action="{{ route('virements.emettre', $virement) }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#3b82f6;">
                    <i class="ti ti-send text-sm"></i> Émettre
                </button>
            </form>
            @endif
            @if($virement->peutEtreExecute())
            <form method="POST" action="{{ route('virements.executer', $virement) }}"
                  onsubmit="return confirm('Confirmer l\'exécution ? Les règlements seront marqués « virés ».')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#009A44;">
                    <i class="ti ti-circle-check text-sm"></i> Marquer exécuté
                </button>
            </form>
            @endif
            @if($virement->peutEtreAnnule())
            <form method="POST" action="{{ route('virements.annuler', $virement) }}"
                  onsubmit="return confirm('Annuler ce virement ? Les règlements seront libérés.')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#dc2626;">
                    <i class="ti ti-x text-sm"></i> Annuler
                </button>
            </form>
            @endif
            @if($virement->isEnPreparation())
            <a href="{{ route('virements.edit', $virement) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#F77F00;">
                <i class="ti ti-pencil text-sm"></i><span class="hidden sm:inline">Modifier</span>
            </a>
            @endif
            <a href="{{ route('virements.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                <i class="ti ti-arrow-left text-sm"></i><span class="hidden sm:inline">Retour</span>
            </a>
        </div>
    </div>
</div>

{{-- Circuit --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    @if($annule)
    <div class="flex items-center gap-3 text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm">
        <i class="ti ti-circle-x text-lg"></i> Ce virement a été <strong>annulé</strong>. Ses règlements ont été libérés.
    </div>
    @else
    <div class="flex items-center justify-between max-w-2xl mx-auto">
        @foreach($etapes as $i => $etape)
        <div class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
            <div class="flex flex-col items-center">
                <div class="h-10 w-10 rounded-full flex items-center justify-center text-white {{ $i <= $ordre ? '' : 'bg-slate-200' }}"
                     style="{{ $i <= $ordre ? 'background:#009A44;' : '' }}">
                    <i class="ti {{ $etape['icon'] }} text-lg {{ $i <= $ordre ? '' : 'text-slate-400' }}"></i>
                </div>
                <span class="mt-1.5 text-xs font-medium {{ $i <= $ordre ? 'text-slate-700' : 'text-slate-400' }}">{{ $etape['label'] }}</span>
            </div>
            @if(! $loop->last)
            <div class="flex-1 h-0.5 mx-2 {{ $i < $ordre ? '' : 'bg-slate-200' }}" style="{{ $i < $ordre ? 'background:#009A44;' : '' }}"></div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Règlements --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100" style="background:#f8f9fb;">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Règlements ({{ $virement->reglements->count() }})</span>
            </div>
            @if($virement->reglements->isEmpty())
            <p class="text-sm text-slate-400 py-12 text-center">Aucun règlement rattaché.</p>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-5 py-3">Contrat / Policier</th>
                            <th class="px-5 py-3 hidden sm:table-cell">Période</th>
                            <th class="px-5 py-3 text-right">Montant</th>
                            <th class="px-5 py-3 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($virement->reglements as $r)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3">
                                <a href="{{ route('reglements.show', $r) }}" class="font-mono text-xs font-semibold text-blue-600 hover:underline">{{ $r->contratBail?->numero_contrat }}</a>
                                <div class="text-xs text-slate-500">{{ $r->contratBail?->policier?->nom }} {{ $r->contratBail?->policier?->prenoms }}</div>
                            </td>
                            <td class="px-5 py-3 hidden sm:table-cell text-xs text-slate-500">{{ $r->periode_label }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-700">{{ number_format($r->montant, 0, ',', ' ') }} F</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $r->statutEnum->badge() }}">{{ $r->statutEnum->label() }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-100" style="background:#f8f9fb;">
                            <td class="px-5 py-3 font-semibold text-slate-700" colspan="2">Total</td>
                            <td class="px-5 py-3 text-right font-bold" style="color:#F77F00;">{{ number_format($virement->montant_total, 0, ',', ' ') }} F</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Infos --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-user-check mr-1.5 text-slate-400"></i> Bailleur
            </h2>
            @if($virement->proprietaire)
            <p class="font-semibold text-slate-800 text-sm">{{ $virement->proprietaire->nom_complet }}</p>
            <div class="space-y-2 text-xs mt-3">
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-phone text-slate-400"></i> {{ $virement->proprietaire->telephone ?? '—' }}</div>
                @if($virement->proprietaire->localite)
                <div class="flex items-center gap-2 text-slate-600"><i class="ti ti-map-pin text-slate-400"></i> {{ $virement->proprietaire->localite->libelle }}</div>
                @endif
            </div>
            <a href="{{ route('proprietaires.show', $virement->proprietaire) }}" class="mt-4 inline-flex items-center gap-1.5 text-xs font-medium transition" style="color:#F77F00;">
                Voir la fiche bailleur <i class="ti ti-arrow-right text-xs"></i>
            </a>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-building-bank mr-1.5 text-slate-400"></i> Virement
            </h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Banque</p>
                    <p class="text-slate-700">{{ $virement->banque ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Fichier de virement</p>
                    <p class="text-slate-700 font-mono text-xs">{{ $virement->reference_fichier ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Émis par</p>
                    <p class="text-slate-700">{{ $virement->emisPar?->name ?? '—' }}</p>
                    @if($virement->date_emission)<p class="text-xs text-slate-400">{{ $virement->date_emission->format('d/m/Y H:i') }}</p>@endif
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Exécuté par</p>
                    <p class="text-slate-700">{{ $virement->executePar?->name ?? '—' }}</p>
                    @if($virement->date_execution)<p class="text-xs text-slate-400">{{ $virement->date_execution->format('d/m/Y H:i') }}</p>@endif
                </div>
            </div>
            @if($virement->observations)
            <div class="mt-4 pt-3 border-t border-gray-100">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Observations</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $virement->observations }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
