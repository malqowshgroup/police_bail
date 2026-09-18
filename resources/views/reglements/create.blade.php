@extends('layouts.app')
@section('title', 'Ajouter un règlement')

@section('content')

@php
    $moisLabels = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Ajouter un règlement</h1>
        <p class="text-sm text-slate-500 mt-0.5">Saisie manuelle d'une échéance pour un contrat actif</p>
    </div>
    <a href="{{ route('reglements.index') }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour à la liste
    </a>
</div>

@if($contrats->isEmpty())
<div class="mb-4 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-alert-triangle text-lg text-amber-500 mt-0.5"></i>
    <span>Aucun contrat actif. Validez d'abord des contrats (via un bordereau ou individuellement).</span>
</div>
@endif

<form method="POST" action="{{ route('reglements.store') }}" x-data="reglementForm()">
@csrf

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #F77F00;">
        <h2 class="text-sm font-semibold text-slate-800">Détails du règlement</h2>
        <p class="text-xs text-slate-500 mt-0.5">Les champs marqués <span class="text-red-500">*</span> sont obligatoires.</p>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Contrat --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Contrat actif <span class="text-red-500">*</span></label>
            <select name="contrat_bail_id" x-model="contratId" @change="majMontant"
                    class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('contrat_bail_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Sélectionner un contrat —</option>
                @foreach($contrats as $c)
                <option value="{{ $c->id }}" data-taux="{{ $c->taux_bail }}" {{ old('contrat_bail_id') == $c->id ? 'selected' : '' }}>
                    {{ $c->numero_contrat }} — {{ $c->policier?->nom }} {{ $c->policier?->prenoms }} ({{ number_format($c->taux_bail, 0, ',', ' ') }} F)
                </option>
                @endforeach
            </select>
            @error('contrat_bail_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Type --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Type <span class="text-red-500">*</span></label>
            <select name="type_reglement" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('type_reglement') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @foreach($types as $t)
                <option value="{{ $t['value'] }}" {{ old('type_reglement', 'loyer_mensuel') === $t['value'] ? 'selected' : '' }}>{{ $t['label'] }}</option>
                @endforeach
            </select>
            @error('type_reglement')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Montant --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Montant (F CFA) <span class="text-red-500">*</span></label>
            <input type="number" name="montant" x-model="montant" min="0" step="1"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('montant') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('montant')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Mois --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mois <span class="text-red-500">*</span></label>
            <select name="periode_mois" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('periode_mois') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @foreach($moisLabels as $num => $lib)
                <option value="{{ $num }}" {{ old('periode_mois', $mois) == $num ? 'selected' : '' }}>{{ $lib }}</option>
                @endforeach
            </select>
            @error('periode_mois')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Année --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Année <span class="text-red-500">*</span></label>
            <input type="number" name="periode_annee" value="{{ old('periode_annee', $annee) }}" min="2020" max="2100"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('periode_annee') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('periode_annee')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <a href="{{ route('reglements.index') }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                style="background:#F77F00;">
            <i class="ti ti-check mr-1.5"></i> Enregistrer le règlement
        </button>
    </div>
</div>

</form>

@push('scripts')
<script>
function reglementForm() {
    return {
        contratId: '{{ old('contrat_bail_id') }}',
        montant: '{{ old('montant') }}',
        majMontant(e) {
            const opt = e?.target?.selectedOptions?.[0];
            if (opt && opt.dataset.taux && !this.montant) {
                this.montant = opt.dataset.taux;
            } else if (opt && opt.dataset.taux) {
                this.montant = opt.dataset.taux;
            }
        },
    };
}
</script>
@endpush

@endsection
