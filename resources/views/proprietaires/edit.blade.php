@extends('layouts.app')

@section('title', 'Modifier ' . $proprietaire->nom_complet)

@section('content')

{{-- ── PAGE HEADER ─────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-800">Modifier le propriétaire</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ $proprietaire->nom_complet }}</p>
    </div>
    <a href="{{ route('proprietaires.show', $proprietaire) }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour à la fiche
    </a>
</div>

<form method="POST" action="{{ route('proprietaires.update', $proprietaire) }}"
      x-data="{ type: '{{ old('type_personne', $proprietaire->type_personne) }}' }">
@csrf
@method('PUT')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Section title --}}
    <div class="px-6 py-4 border-l-4 border-b border-gray-100" style="border-left-color:#F77F00;">
        <h2 class="text-sm font-semibold text-slate-800">Informations du propriétaire</h2>
        <p class="text-xs text-slate-500 mt-0.5">Les champs marqués d'un <span class="text-red-500">*</span> sont obligatoires.</p>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Type de personne --}}
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-slate-600 mb-2">
                Type de personne <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6">
                <label class="inline-flex items-center gap-2.5 cursor-pointer">
                    <input type="radio" name="type_personne" value="physique"
                           x-model="type"
                           {{ old('type_personne', $proprietaire->type_personne) === 'physique' ? 'checked' : '' }}
                           class="accent-orange-500 w-4 h-4">
                    <span class="text-sm text-slate-700 font-medium">Personne physique</span>
                </label>
                <label class="inline-flex items-center gap-2.5 cursor-pointer">
                    <input type="radio" name="type_personne" value="morale"
                           x-model="type"
                           {{ old('type_personne', $proprietaire->type_personne) === 'morale' ? 'checked' : '' }}
                           class="accent-orange-500 w-4 h-4">
                    <span class="text-sm text-slate-700 font-medium">Personne morale</span>
                </label>
            </div>
            @error('type_personne')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Raison sociale (morale seulement) --}}
        <div class="md:col-span-2" x-show="type === 'morale'" x-transition>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                Raison sociale <span class="text-red-500">*</span>
            </label>
            <input type="text" name="raison_sociale" value="{{ old('raison_sociale', $proprietaire->raison_sociale) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 {{ $errors->has('raison_sociale') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                   placeholder="Dénomination sociale de l'entreprise">
            @error('raison_sociale')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nom --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                <span x-text="type === 'morale' ? 'Représentant légal (Nom)' : 'Nom'"></span>
                <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nom" value="{{ old('nom', $proprietaire->nom) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 {{ $errors->has('nom') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                   placeholder="Nom de famille">
            @error('nom')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Prénoms (physique seulement) --}}
        <div x-show="type === 'physique'" x-transition>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Prénoms</label>
            <input type="text" name="prenoms" value="{{ old('prenoms', $proprietaire->prenoms) }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2"
                   placeholder="Prénoms">
            @error('prenoms')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- N° Pièce d'identité --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                N° Pièce d'identité <span class="text-red-500">*</span>
            </label>
            <input type="text" name="num_piece_identite" value="{{ old('num_piece_identite', $proprietaire->num_piece_identite) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 font-mono {{ $errors->has('num_piece_identite') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                   placeholder="Numéro de la pièce">
            @error('num_piece_identite')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Type pièce --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                Type de pièce <span class="text-red-500">*</span>
            </label>
            <select name="type_piece"
                    class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 bg-white {{ $errors->has('type_piece') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Sélectionner —</option>
                <option value="cni" {{ old('type_piece', $proprietaire->type_piece) === 'cni' ? 'selected' : '' }}>Carte Nationale d'Identité</option>
                <option value="passeport" {{ old('type_piece', $proprietaire->type_piece) === 'passeport' ? 'selected' : '' }}>Passeport</option>
                <option value="sejour" {{ old('type_piece', $proprietaire->type_piece) === 'sejour' ? 'selected' : '' }}>Titre de séjour</option>
            </select>
            @error('type_piece')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Téléphone --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                Téléphone <span class="text-red-500">*</span>
            </label>
            <input type="text" name="telephone" value="{{ old('telephone', $proprietaire->telephone) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 {{ $errors->has('telephone') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                   placeholder="+225 0X XX XX XX XX">
            @error('telephone')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Téléphone 2 --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Téléphone 2</label>
            <input type="text" name="telephone2" value="{{ old('telephone2', $proprietaire->telephone2) }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2"
                   placeholder="+225 0X XX XX XX XX">
            @error('telephone2')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Email</label>
            <input type="email" name="email" value="{{ old('email', $proprietaire->email) }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2"
                   placeholder="adresse@email.com">
            @error('email')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Adresse postale --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Adresse postale</label>
            <input type="text" name="adresse_postale" value="{{ old('adresse_postale', $proprietaire->adresse_postale) }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2"
                   placeholder="BP XXXX Abidjan">
            @error('adresse_postale')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- N° Compte contribuable --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">N° Compte contribuable</label>
            <input type="text" name="num_compte_contribuable" value="{{ old('num_compte_contribuable', $proprietaire->num_compte_contribuable) }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 font-mono"
                   placeholder="Numéro de compte contribuable">
            @error('num_compte_contribuable')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Localité --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Localité</label>
            <select name="localite_id"
                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 bg-white">
                <option value="">— Sélectionner une localité —</option>
                @foreach($localites as $localite)
                <option value="{{ $localite->id }}" {{ old('localite_id', $proprietaire->localite_id) == $localite->id ? 'selected' : '' }}>
                    {{ $localite->libelle }}
                </option>
                @endforeach
            </select>
            @error('localite_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Actif --}}
        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="actif" value="0">
                <input type="checkbox" name="actif" value="1"
                       {{ old('actif', $proprietaire->actif) ? 'checked' : '' }}
                       class="w-4 h-4 rounded accent-orange-500">
                <span class="text-sm font-medium text-slate-700">Propriétaire actif</span>
            </label>
        </div>

    </div>

    {{-- Actions --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
        <a href="{{ route('proprietaires.show', $proprietaire) }}"
           class="w-full sm:w-auto text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
            Annuler
        </a>
        <button type="submit"
                class="w-full sm:w-auto px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                style="background:#F77F00;">
            <i class="ti ti-device-floppy mr-1"></i> Mettre à jour
        </button>
    </div>

</div>
</form>

@endsection
