@extends('layouts.app')
@section('title', 'Bordereau ' . $bordereau->numero)

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
        ['statut' => 'en_saisie',     'label' => 'Saisie',     'icon' => 'ti-edit'],
        ['statut' => 'en_controle',   'label' => 'Contrôle',   'icon' => 'ti-checkup-list'],
        ['statut' => 'en_validation', 'label' => 'Validation', 'icon' => 'ti-gavel'],
        ['statut' => 'valide',        'label' => 'Validé',     'icon' => 'ti-checkbox'],
    ];
    $ordre = array_search($bordereau->statut, array_column($etapes, 'statut'));
    $rejete = $bordereau->statut === 'rejete';
@endphp

<div x-data="{ rejeterModal: false }">

{{-- Header --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5">
        <div class="h-14 w-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(247,127,0,.12);">
            <i class="ti ti-clipboard-list text-2xl" style="color:#F77F00;"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-bold text-slate-800 font-mono">{{ $bordereau->numero }}</h1>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $bordereau->statutEnum->badge() }}">
                    <span class="h-1.5 w-1.5 rounded-full {{ $bordereau->statutEnum->dot() }} inline-block"></span>
                    {{ $bordereau->statutEnum->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">
                Année {{ $bordereau->annee }}
                &bull; {{ $bordereau->contrats->count() }} contrat(s)
                &bull; <span class="font-semibold">{{ number_format($bordereau->montant_total, 0, ',', ' ') }} F</span>/mois
            </p>
        </div>
        <div class="flex flex-wrap gap-2 flex-shrink-0">
            @if($bordereau->peutEtreSoumis())
            <form method="POST" action="{{ route('bordereaux.soumettre', $bordereau) }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#3b82f6;">
                    <i class="ti ti-send text-sm"></i> Transmettre au contrôle
                </button>
            </form>
            @endif
            @if($bordereau->peutEtreControle())
            <form method="POST" action="{{ route('bordereaux.controler', $bordereau) }}">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#6366f1;">
                    <i class="ti ti-checkup-list text-sm"></i> Valider le contrôle
                </button>
            </form>
            @endif
            @if($bordereau->peutEtreValide())
            <form method="POST" action="{{ route('bordereaux.valider', $bordereau) }}"
                  onsubmit="return confirm('Valider ce bordereau ? Tous les contrats « en attente » rattachés deviendront actifs.')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#009A44;">
                    <i class="ti ti-checkbox text-sm"></i> Valider
                </button>
            </form>
            @endif
            @if($bordereau->peutEtreRejete())
            <button type="button" @click="rejeterModal = true" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#dc2626;">
                <i class="ti ti-x text-sm"></i> Rejeter
            </button>
            @endif
            @if($bordereau->isEnSaisie())
            <a href="{{ route('bordereaux.edit', $bordereau) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#F77F00;">
                <i class="ti ti-pencil text-sm"></i><span class="hidden sm:inline">Modifier</span>
            </a>
            @endif
            <a href="{{ route('bordereaux.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                <i class="ti ti-arrow-left text-sm"></i><span class="hidden sm:inline">Retour</span>
            </a>
        </div>
    </div>
</div>

{{-- Circuit (stepper) --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    @if($rejete)
    <div class="flex items-center gap-3 text-red-700 bg-red-50 border border-red-200 rounded-xl px-4 py-3 text-sm">
        <i class="ti ti-circle-x text-lg"></i> Ce bordereau a été <strong>rejeté</strong>. Il ne suit plus le circuit de validation.
    </div>
    @else
    <div class="flex items-center justify-between">
        @foreach($etapes as $i => $etape)
        <div class="flex items-center flex-1 {{ $loop->last ? 'flex-none' : '' }}">
            <div class="flex flex-col items-center">
                <div class="h-10 w-10 rounded-full flex items-center justify-center text-white transition
                            {{ $i <= $ordre ? '' : 'bg-slate-200' }}"
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

    {{-- Contrats --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between" style="background:#f8f9fb;">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Contrats rattachés ({{ $bordereau->contrats->count() }})</span>
            </div>
            @if($bordereau->contrats->isEmpty())
            <p class="text-sm text-slate-400 py-12 text-center">Aucun contrat rattaché à ce bordereau.</p>
            @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-5 py-3">N° / Policier</th>
                            <th class="px-5 py-3 hidden sm:table-cell">Logement</th>
                            <th class="px-5 py-3 text-right">Taux</th>
                            <th class="px-5 py-3 text-center">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($bordereau->contrats as $contrat)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3">
                                <a href="{{ route('contrats.show', $contrat) }}" class="font-mono text-xs font-semibold text-blue-600 hover:underline">{{ $contrat->numero_contrat }}</a>
                                <div class="text-xs text-slate-500">{{ $contrat->policier?->nom }} {{ $contrat->policier?->prenoms }}</div>
                            </td>
                            <td class="px-5 py-3 hidden sm:table-cell text-xs text-slate-500">{{ $contrat->logementCivil?->reference ?? '—' }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-slate-700">{{ number_format($contrat->taux_bail, 0, ',', ' ') }} F</td>
                            <td class="px-5 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $contrat->statutEnum->badge() }}">{{ $contrat->statutEnum->label() }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-gray-100" style="background:#f8f9fb;">
                            <td class="px-5 py-3 font-semibold text-slate-700" colspan="2">Total mensuel</td>
                            <td class="px-5 py-3 text-right font-bold" style="color:#F77F00;">{{ number_format($bordereau->montant_total, 0, ',', ' ') }} F</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Suivi --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-route mr-1.5 text-slate-400"></i> Suivi
            </h2>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Saisi par</p>
                    <p class="text-slate-700">{{ $bordereau->saisiPar?->name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $bordereau->date_saisie?->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Contrôlé par</p>
                    <p class="text-slate-700">{{ $bordereau->controlePar?->name ?? '—' }}</p>
                    @if($bordereau->date_controle)<p class="text-xs text-slate-400">{{ $bordereau->date_controle->format('d/m/Y H:i') }}</p>@endif
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Validé par</p>
                    <p class="text-slate-700">{{ $bordereau->validePar?->name ?? '—' }}</p>
                    @if($bordereau->date_validation)<p class="text-xs text-slate-400">{{ $bordereau->date_validation->format('d/m/Y H:i') }}</p>@endif
                </div>
            </div>
            @if($bordereau->observations)
            <div class="mt-4 pt-3 border-t border-gray-100">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-0.5">Observations</p>
                <p class="text-sm text-slate-700 whitespace-pre-line">{{ $bordereau->observations }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal rejeter --}}
<div x-show="rejeterModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.4);" @click.self="rejeterModal = false">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6" @click.stop>
        <h3 class="text-lg font-bold text-slate-800 mb-1">Rejeter le bordereau</h3>
        <p class="text-sm text-slate-500 mb-4">Indiquez le motif de rejet de {{ $bordereau->numero }}.</p>
        <form method="POST" action="{{ route('bordereaux.rejeter', $bordereau) }}">
            @csrf
            <textarea name="motif_rejet" rows="3" required placeholder="Motif de rejet…"
                      class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 mb-4"></textarea>
            <div class="flex justify-end gap-2">
                <button type="button" @click="rejeterModal = false" class="px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200">Annuler</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#dc2626;">Rejeter</button>
            </div>
        </form>
    </div>
</div>

</div>

@endsection
