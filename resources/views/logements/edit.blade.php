@extends('layouts.app')
@section('title', 'Modifier — ' . $logement->reference)

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="min-w-0">
        <h1 class="text-2xl font-bold text-slate-800">Modifier le logement</h1>
        <p class="text-sm text-slate-500 mt-0.5 truncate">
            <span class="font-mono">{{ $logement->reference }}</span> &bull; {{ $logement->quartier }}
        </p>
    </div>
    <a href="{{ route('logements.show', $logement) }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour à la fiche
    </a>
</div>

<form method="POST" action="{{ route('logements.update', $logement) }}">
@csrf
@method('PUT')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #1a2440;">
        <h2 class="text-sm font-semibold text-slate-800">Informations du logement</h2>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Référence <span class="text-red-500">*</span></label>
            <input type="text" name="reference" value="{{ old('reference', $logement->reference) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('reference') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('reference')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Localité <span class="text-red-500">*</span></label>
            <select name="localite_id" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('localite_id') ? 'border-red-400' : 'border-gray-200' }}">
                <option value="">— Sélectionner —</option>
                @foreach($localites as $loc)
                <option value="{{ $loc->id }}" {{ old('localite_id', $logement->localite_id) == $loc->id ? 'selected' : '' }}>{{ $loc->libelle }}</option>
                @endforeach
            </select>
            @error('localite_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Propriétaire <span class="text-red-500">*</span></label>
            <select name="proprietaire_id" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('proprietaire_id') ? 'border-red-400' : 'border-gray-200' }}">
                <option value="">— Sélectionner —</option>
                @foreach($proprietaires as $prop)
                <option value="{{ $prop->id }}" {{ old('proprietaire_id', $logement->proprietaire_id) == $prop->id ? 'selected' : '' }}>
                    {{ $prop->nom_complet }}
                </option>
                @endforeach
            </select>
            @error('proprietaire_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Quartier <span class="text-red-500">*</span></label>
            <input type="text" name="quartier" value="{{ old('quartier', $logement->quartier) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('quartier') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('quartier')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Îlot</label>
            <input type="text" name="ilot" value="{{ old('ilot', $logement->ilot) }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Lot</label>
            <input type="text" name="lot" value="{{ old('lot', $logement->lot) }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Adresse complète</label>
            <input type="text" name="adresse_complete" value="{{ old('adresse_complete', $logement->adresse_complete) }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
        </div>

        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="actif" value="0">
                <input type="checkbox" name="actif" value="1"
                       {{ old('actif', $logement->actif) ? 'checked' : '' }}
                       class="w-4 h-4 rounded accent-orange-500">
                <span class="text-sm font-medium text-slate-700">Logement actif</span>
            </label>
        </div>

    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <a href="{{ route('logements.show', $logement) }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                style="background:#1a2440;">
            <i class="ti ti-check mr-1.5"></i> Mettre à jour
        </button>
    </div>
</div>

</form>
@endsection
