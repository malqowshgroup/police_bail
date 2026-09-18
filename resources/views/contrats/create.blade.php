@extends('layouts.app')
@section('title', 'Nouveau contrat de bail')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Nouveau contrat de bail</h1>
        <p class="text-sm text-slate-500 mt-0.5">Rattacher un policier à un logement civil</p>
    </div>
    <a href="{{ route('contrats.index') }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour à la liste
    </a>
</div>

@if($policiers->isEmpty() || $logements->isEmpty())
<div class="mb-4 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-alert-triangle text-lg text-amber-500 mt-0.5"></i>
    <div>
        @if($policiers->isEmpty())<p>Aucun policier éligible disponible (tous ont déjà un contrat en cours).</p>@endif
        @if($logements->isEmpty())<p>Aucun logement libre disponible.</p>@endif
    </div>
</div>
@endif

<form method="POST" action="{{ route('contrats.store') }}"
      x-data="contratForm()">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Colonne formulaire --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Parties --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #F77F00;">
                <h2 class="text-sm font-semibold text-slate-800">Parties au contrat</h2>
                <p class="text-xs text-slate-500 mt-0.5">Les champs marqués <span class="text-red-500">*</span> sont obligatoires.</p>
            </div>
            <div class="p-6 grid grid-cols-1 gap-5">

                {{-- Policier --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Policier bénéficiaire <span class="text-red-500">*</span></label>
                    <select name="policier_id" x-model="policierId" @change="majTaux"
                            class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('policier_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                        <option value="">— Sélectionner un policier —</option>
                        @foreach($policiers as $p)
                        <option value="{{ $p->id }}"
                                data-taux="{{ $p->grade?->taux_bail ?? 0 }}"
                                data-grade="{{ $p->grade?->libelle ?? '—' }}"
                                {{ old('policier_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nom }} {{ $p->prenoms }} — {{ $p->matricule }} ({{ $p->grade?->libelle ?? '—' }})
                        </option>
                        @endforeach
                    </select>
                    @error('policier_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <p class="text-xs text-slate-400 mt-1">Seuls les policiers éligibles sans contrat en cours sont listés.</p>
                </div>

                {{-- Logement --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Logement civil <span class="text-red-500">*</span></label>
                    <select name="logement_civil_id"
                            class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none bg-white {{ $errors->has('logement_civil_id') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                        <option value="">— Sélectionner un logement —</option>
                        @foreach($logements as $l)
                        <option value="{{ $l->id }}" {{ old('logement_civil_id') == $l->id ? 'selected' : '' }}>
                            {{ $l->reference }} — {{ $l->quartier }}{{ $l->localite ? ' ('.$l->localite->libelle.')' : '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('logement_civil_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <p class="text-xs text-slate-400 mt-1">
                        Logement absent ?
                        <a href="{{ route('logements.create') }}" target="_blank" class="underline" style="color:#F77F00;">Ajouter un logement</a>
                    </p>
                </div>

            </div>
        </div>

        {{-- Période --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100" style="border-left: 4px solid #1a2440;">
                <h2 class="text-sm font-semibold text-slate-800">Période & conditions</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Date de début <span class="text-red-500">*</span></label>
                    <input type="date" name="date_debut" value="{{ old('date_debut', now()->format('Y-m-d')) }}"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('date_debut') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                    @error('date_debut')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Date de fin prévue</label>
                    <input type="date" name="date_fin" value="{{ old('date_fin') }}"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('date_fin') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                    @error('date_fin')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    <p class="text-xs text-slate-400 mt-1">Laisser vide pour une durée indéterminée.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Préavis (mois)</label>
                    <input type="number" name="preavis_mois" value="{{ old('preavis_mois', 3) }}" min="0" max="24"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>

            </div>

            {{-- Arriérés --}}
            <div class="px-6 pb-6">
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="avec_arrieres" value="0">
                    <input type="checkbox" name="avec_arrieres" value="1" x-model="avecArrieres"
                           {{ old('avec_arrieres') ? 'checked' : '' }}
                           class="w-4 h-4 rounded accent-orange-500">
                    <span class="text-sm font-medium text-slate-700">Le contrat comporte des arriérés</span>
                </label>

                <div x-show="avecArrieres" x-cloak class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Nombre de mois d'arriérés</label>
                        <input type="number" name="nb_mois_arrieres" value="{{ old('nb_mois_arrieres', 0) }}" min="0" max="120"
                               class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Date de début des arriérés</label>
                        <input type="date" name="date_debut_arrieres" value="{{ old('date_debut_arrieres') }}"
                               class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('date_debut_arrieres') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                        @error('date_debut_arrieres')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Observations --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <label class="block text-xs font-semibold text-slate-600 mb-1.5">Observations</label>
            <textarea name="observations" rows="3" placeholder="Notes éventuelles…"
                      class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">{{ old('observations') }}</textarea>
        </div>

    </div>

    {{-- Colonne récap --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-4">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-receipt mr-1.5 text-slate-400"></i> Récapitulatif
            </h2>

            <div class="text-center py-4">
                <p class="text-xs font-medium text-slate-400 uppercase tracking-wider mb-1">Taux mensuel du bail</p>
                <p class="text-3xl font-bold" style="color:#F77F00;">
                    <span x-text="tauxFormate"></span> <span class="text-lg">F</span>
                </p>
                <p class="text-xs text-slate-500 mt-1" x-show="gradeLabel" x-cloak>
                    Grade : <span class="font-medium" x-text="gradeLabel"></span>
                </p>
            </div>

            <div class="mt-2 rounded-xl bg-slate-50 p-4 text-xs text-slate-500 space-y-2">
                <div class="flex items-start gap-2">
                    <i class="ti ti-info-circle text-slate-400 mt-0.5"></i>
                    <span>Le taux est déterminé automatiquement par le grade du policier sélectionné.</span>
                </div>
                <div class="flex items-start gap-2">
                    <i class="ti ti-clock text-slate-400 mt-0.5"></i>
                    <span>Le contrat sera créé <strong>en attente</strong> et devra être activé après contrôle.</span>
                </div>
            </div>

            <button type="submit"
                    class="mt-5 w-full px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                    style="background:#F77F00;">
                <i class="ti ti-check mr-1.5"></i> Créer le contrat
            </button>
            <a href="{{ route('contrats.index') }}"
               class="mt-2 block text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
                Annuler
            </a>
        </div>
    </div>

</div>

</form>

@push('scripts')
<script>
function contratForm() {
    return {
        policierId: '{{ old('policier_id') }}',
        avecArrieres: {{ old('avec_arrieres') ? 'true' : 'false' }},
        taux: 0,
        gradeLabel: '',
        get tauxFormate() {
            return new Intl.NumberFormat('fr-FR').format(this.taux || 0);
        },
        majTaux(e) {
            const opt = e?.target?.selectedOptions?.[0];
            this.taux = opt ? parseInt(opt.dataset.taux || 0, 10) : 0;
            this.gradeLabel = opt ? (opt.dataset.grade || '') : '';
        },
        init() {
            const sel = this.$el.querySelector('select[name="policier_id"]');
            if (sel && sel.value) {
                const opt = sel.selectedOptions[0];
                this.taux = parseInt(opt.dataset.taux || 0, 10);
                this.gradeLabel = opt.dataset.grade || '';
            }
        },
    };
}
</script>
@endpush

@endsection
