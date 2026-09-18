@extends('layouts.app')
@section('title', 'Modifier ' . $contrat->numero_contrat)

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Modifier le contrat</h1>
        <p class="text-sm text-slate-500 mt-0.5 font-mono">{{ $contrat->numero_contrat }}</p>
    </div>
    <a href="{{ route('contrats.show', $contrat) }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour au contrat
    </a>
</div>

<form method="POST" action="{{ route('contrats.update', $contrat) }}" x-data="{ avecArrieres: {{ old('avec_arrieres', $contrat->avec_arrieres) ? 'true' : 'false' }} }">
@csrf @method('PUT')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #F77F00;">
        <h2 class="text-sm font-semibold text-slate-800">Informations du contrat</h2>
        <p class="text-xs text-slate-500 mt-0.5">Le taux est recalculé si le policier change de grade.</p>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Policier --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Policier bénéficiaire <span class="text-red-500">*</span></label>
            <select name="policier_id" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('policier_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Sélectionner un policier —</option>
                @foreach($policiers as $p)
                <option value="{{ $p->id }}" {{ old('policier_id', $contrat->policier_id) == $p->id ? 'selected' : '' }}>
                    {{ $p->nom }} {{ $p->prenoms }} — {{ $p->matricule }} ({{ $p->grade?->libelle ?? '—' }})
                </option>
                @endforeach
            </select>
            @error('policier_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Logement --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Logement civil <span class="text-red-500">*</span></label>
            <select name="logement_civil_id" class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('logement_civil_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Sélectionner un logement —</option>
                @foreach($logements as $l)
                <option value="{{ $l->id }}" {{ old('logement_civil_id', $contrat->logement_civil_id) == $l->id ? 'selected' : '' }}>
                    {{ $l->reference }} — {{ $l->quartier }}{{ $l->localite ? ' ('.$l->localite->libelle.')' : '' }}
                </option>
                @endforeach
            </select>
            @error('logement_civil_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Date début --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Date de début <span class="text-red-500">*</span></label>
            <input type="date" name="date_debut" value="{{ old('date_debut', $contrat->date_debut?->format('Y-m-d')) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('date_debut') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('date_debut')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Date fin --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Date de fin prévue</label>
            <input type="date" name="date_fin" value="{{ old('date_fin', $contrat->date_fin?->format('Y-m-d')) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('date_fin') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('date_fin')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- Préavis --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Préavis (mois)</label>
            <input type="number" name="preavis_mois" value="{{ old('preavis_mois', $contrat->preavis_mois) }}" min="0" max="24"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
        </div>

        {{-- Bordereau (lecture du taux courant) --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Taux mensuel actuel</label>
            <div class="w-full px-3 py-2.5 text-sm border border-gray-100 rounded-xl bg-slate-50 text-slate-700 font-semibold">
                {{ number_format($contrat->taux_bail, 0, ',', ' ') }} F
            </div>
        </div>

        {{-- Arriérés --}}
        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="avec_arrieres" value="0">
                <input type="checkbox" name="avec_arrieres" value="1" x-model="avecArrieres"
                       {{ old('avec_arrieres', $contrat->avec_arrieres) ? 'checked' : '' }}
                       class="w-4 h-4 rounded accent-orange-500">
                <span class="text-sm font-medium text-slate-700">Le contrat comporte des arriérés</span>
            </label>

            <div x-show="avecArrieres" x-cloak class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nombre de mois d'arriérés</label>
                    <input type="number" name="nb_mois_arrieres" value="{{ old('nb_mois_arrieres', $contrat->nb_mois_arrieres) }}" min="0" max="120"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Date de début des arriérés</label>
                    <input type="date" name="date_debut_arrieres" value="{{ old('date_debut_arrieres', $contrat->date_debut_arrieres?->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('date_debut_arrieres') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                    @error('date_debut_arrieres')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- Observations --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Observations</label>
            <textarea name="observations" rows="3"
                      class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">{{ old('observations', $contrat->observations) }}</textarea>
        </div>

    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
        <a href="{{ route('contrats.show', $contrat) }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                style="background:#F77F00;">
            <i class="ti ti-check mr-1.5"></i> Enregistrer les modifications
        </button>
    </div>
</div>

</form>
@endsection
