@extends('layouts.app')

@section('title', 'Modifier — ' . $policier->nom . ' ' . $policier->prenoms)

@section('content')

{{-- ── PAGE HEADER ─────────────────────────────────────────────────── --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="min-w-0">
        <h1 class="text-xl font-bold text-slate-800">Modifier un policier</h1>
        <p class="text-sm text-slate-500 mt-0.5 truncate">
            {{ $policier->nom }} {{ $policier->prenoms }} &bull;
            <span class="font-mono text-xs">{{ $policier->matricule }}</span>
        </p>
    </div>
    <a href="{{ route('policiers.show', $policier) }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour à la fiche
    </a>
</div>

<form method="POST" action="{{ route('policiers.update', $policier) }}">
@csrf
@method('PUT')

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Section title --}}
    <div class="px-6 py-4 border-l-4 border-b border-gray-100" style="border-left-color:#1a2440;">
        <h2 class="text-sm font-semibold text-slate-800">Informations du policier</h2>
        <p class="text-xs text-slate-500 mt-0.5">Les champs marqués d'un <span class="text-red-500">*</span> sont obligatoires.</p>
    </div>

    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Matricule --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                Matricule <span class="text-red-500">*</span>
            </label>
            <input type="text" name="matricule" value="{{ old('matricule', $policier->matricule) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 {{ $errors->has('matricule') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                   placeholder="Ex: CI12ABC34">
            @error('matricule')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Sexe --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                Sexe <span class="text-red-500">*</span>
            </label>
            <div class="flex gap-6 mt-1">
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="sexe" value="M"
                           {{ old('sexe', $policier->sexe) === 'M' ? 'checked' : '' }}
                           class="accent-orange-500">
                    <span class="text-sm text-slate-700">Masculin</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="sexe" value="F"
                           {{ old('sexe', $policier->sexe) === 'F' ? 'checked' : '' }}
                           class="accent-orange-500">
                    <span class="text-sm text-slate-700">Féminin</span>
                </label>
            </div>
            @error('sexe')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nom --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                Nom <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nom" value="{{ old('nom', $policier->nom) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 {{ $errors->has('nom') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                   placeholder="Nom de famille">
            @error('nom')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Prénoms --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                Prénoms <span class="text-red-500">*</span>
            </label>
            <input type="text" name="prenoms" value="{{ old('prenoms', $policier->prenoms) }}"
                   class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 {{ $errors->has('prenoms') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}"
                   placeholder="Prénoms">
            @error('prenoms')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Grade --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                Grade <span class="text-red-500">*</span>
            </label>
            <select name="grade_id"
                    class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 {{ $errors->has('grade_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Sélectionner un grade —</option>
                @foreach($grades as $grade)
                <option value="{{ $grade->id }}" {{ old('grade_id', $policier->grade_id) == $grade->id ? 'selected' : '' }}>
                    {{ $grade->libelle }}
                </option>
                @endforeach
            </select>
            @error('grade_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Statut --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                Statut <span class="text-red-500">*</span>
            </label>
            <select name="statut"
                    class="w-full px-3 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 {{ $errors->has('statut') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <option value="">— Sélectionner un statut —</option>
                @foreach(\App\Enums\StatutPolicier::cases() as $case)
                <option value="{{ $case->value }}"
                    {{ old('statut', $policier->statut->value) === $case->value ? 'selected' : '' }}>
                    {{ $case->label() }}
                </option>
                @endforeach
            </select>
            @error('statut')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Service --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Service</label>
            <select name="service_id"
                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2">
                <option value="">— Sélectionner un service —</option>
                @foreach($services as $service)
                <option value="{{ $service->id }}" {{ old('service_id', $policier->service_id) == $service->id ? 'selected' : '' }}>
                    {{ $service->libelle }}
                </option>
                @endforeach
            </select>
            @error('service_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Localité --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Localité</label>
            <select name="localite_id"
                    class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2">
                <option value="">— Sélectionner une localité —</option>
                @foreach($localites as $localite)
                <option value="{{ $localite->id }}" {{ old('localite_id', $policier->localite_id) == $localite->id ? 'selected' : '' }}>
                    {{ $localite->libelle }}
                </option>
                @endforeach
            </select>
            @error('localite_id')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Date naissance --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Date de naissance</label>
            <input type="date" name="date_naissance"
                   value="{{ old('date_naissance', $policier->date_naissance ? \Carbon\Carbon::parse($policier->date_naissance)->format('Y-m-d') : '') }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2">
            @error('date_naissance')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Date prise de service --}}
        <div>
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Date de prise de service</label>
            <input type="date" name="date_prise_service"
                   value="{{ old('date_prise_service', $policier->date_prise_service ? \Carbon\Carbon::parse($policier->date_prise_service)->format('Y-m-d') : '') }}"
                   class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2">
            @error('date_prise_service')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Propriétaire du logement --}}
        <div class="md:col-span-2">
            <label class="inline-flex items-center gap-3 cursor-pointer">
                <input type="hidden" name="proprietaire_logement" value="0">
                <input type="checkbox" name="proprietaire_logement" value="1"
                       {{ old('proprietaire_logement', $policier->proprietaire_logement) ? 'checked' : '' }}
                       class="w-4 h-4 rounded accent-orange-500">
                <span class="text-sm font-medium text-slate-700">Propriétaire de son logement</span>
            </label>
        </div>

    </div>

    {{-- Actions --}}
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">
        <a href="{{ route('policiers.show', $policier) }}"
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
