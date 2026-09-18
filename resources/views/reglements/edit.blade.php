@extends('layouts.app')
@section('title', 'Modifier le règlement')

@section('content')

@php
    $moisLabels = [1=>'Janvier',2=>'Février',3=>'Mars',4=>'Avril',5=>'Mai',6=>'Juin',7=>'Juillet',8=>'Août',9=>'Septembre',10=>'Octobre',11=>'Novembre',12=>'Décembre'];
@endphp

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Modifier le règlement</h1>
        <p class="text-sm text-slate-500 mt-0.5">Contrat {{ $reglement->contratBail?->numero_contrat }} — {{ $reglement->periode_label }}</p>
    </div>
    <a href="{{ route('reglements.show', $reglement) }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour
    </a>
</div>

<form method="POST" action="{{ route('reglements.update', $reglement) }}">
@csrf @method('PUT')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-3xl">
    <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #F77F00;">
        <h2 class="text-sm font-semibold text-slate-800">Détails du règlement</h2>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Contrat (lecture seule) --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Contrat</label>
            <div class="w-full px-3 py-2.5 text-sm border border-gray-100 rounded-xl bg-slate-50 text-slate-700">
                {{ $reglement->contratBail?->numero_contrat }} — {{ $reglement->contratBail?->policier?->nom }} {{ $reglement->contratBail?->policier?->prenoms }}
            </div>
        </div>

        {{-- Type --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Type <span class="text-red-500">*</span></label>
            <select name="type_reglement" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('type_reglement') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @foreach($types as $t)
                <option value="{{ $t['value'] }}" {{ old('type_reglement', $reglement->type_reglement) === $t['value'] ? 'selected' : '' }}>{{ $t['label'] }}</option>
                @endforeach
            </select>
            @error('type_reglement')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Montant --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Montant (F CFA) <span class="text-red-500">*</span></label>
            <input type="number" name="montant" value="{{ old('montant', (int) $reglement->montant) }}" min="0" step="1"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('montant') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('montant')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Mois --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Mois <span class="text-red-500">*</span></label>
            <select name="periode_mois" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('periode_mois') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                @foreach($moisLabels as $num => $lib)
                <option value="{{ $num }}" {{ old('periode_mois', $reglement->periode_mois) == $num ? 'selected' : '' }}>{{ $lib }}</option>
                @endforeach
            </select>
            @error('periode_mois')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Année --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Année <span class="text-red-500">*</span></label>
            <input type="number" name="periode_annee" value="{{ old('periode_annee', $reglement->periode_annee) }}" min="2020" max="2100"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('periode_annee') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('periode_annee')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <a href="{{ route('reglements.show', $reglement) }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                style="background:#F77F00;">
            <i class="ti ti-check mr-1.5"></i> Enregistrer
        </button>
    </div>
</div>

</form>
@endsection
