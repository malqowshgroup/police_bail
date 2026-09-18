@extends('layouts.app')
@section('title', 'Ajouter un logement')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Ajouter un logement</h1>
        <p class="text-sm text-slate-500 mt-0.5">Enregistrer un nouveau logement civil</p>
    </div>
    <a href="{{ route('logements.index') }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour à la liste
    </a>
</div>

<form method="POST" action="{{ route('logements.store') }}">
@csrf

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #F77F00;">
        <h2 class="text-sm font-semibold text-slate-800">Informations du logement</h2>
        <p class="text-xs text-slate-500 mt-0.5">Les champs marqués <span class="text-red-500">*</span> sont obligatoires.</p>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Référence --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Référence <span class="text-red-500">*</span></label>
            <input type="text" name="reference" value="{{ old('reference') }}"
                   placeholder="Ex: ABJ-2026-0001"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('reference') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('reference')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Localité --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Localité <span class="text-red-500">*</span></label>
            <select name="localite_id" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('localite_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Sélectionner une localité —</option>
                @foreach($localites as $loc)
                <option value="{{ $loc->id }}" {{ old('localite_id') == $loc->id ? 'selected' : '' }}>{{ $loc->libelle }}</option>
                @endforeach
            </select>
            @error('localite_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Propriétaire --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Propriétaire <span class="text-red-500">*</span></label>
            <select name="proprietaire_id" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('proprietaire_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Sélectionner un propriétaire —</option>
                @foreach($proprietaires as $prop)
                <option value="{{ $prop->id }}" {{ old('proprietaire_id') == $prop->id ? 'selected' : '' }}>
                    {{ $prop->nom_complet }}
                    @if($prop->type_personne === 'morale') (Entreprise) @endif
                </option>
                @endforeach
            </select>
            @error('proprietaire_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            <p class="text-xs text-slate-400 mt-1">
                Propriétaire absent ?
                <a href="{{ route('proprietaires.create') }}" target="_blank" class="underline" style="color:#F77F00;">Ajouter un propriétaire</a>
            </p>
        </div>

        {{-- Quartier --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Quartier <span class="text-red-500">*</span></label>
            <input type="text" name="quartier" value="{{ old('quartier') }}"
                   placeholder="Ex: Cocody, Riviera…"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('quartier') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('quartier')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Îlot --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Îlot</label>
            <input type="text" name="ilot" value="{{ old('ilot') }}" placeholder="N° d'îlot"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
        </div>

        {{-- Lot --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Lot</label>
            <input type="text" name="lot" value="{{ old('lot') }}" placeholder="N° de lot"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
        </div>

        {{-- Adresse complète --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Adresse complète</label>
            <input type="text" name="adresse_complete" value="{{ old('adresse_complete') }}"
                   placeholder="Adresse détaillée (facultatif)"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
        </div>

        {{-- Actif --}}
        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="actif" value="0">
                <input type="checkbox" name="actif" value="1" {{ old('actif', '1') ? 'checked' : '' }}
                       class="w-4 h-4 rounded accent-orange-500">
                <span class="text-sm font-medium text-slate-700">Logement actif</span>
            </label>
        </div>

    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <a href="{{ route('logements.index') }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                style="background:#F77F00;">
            <i class="ti ti-check mr-1.5"></i> Enregistrer le logement
        </button>
    </div>
</div>

</form>
@endsection
