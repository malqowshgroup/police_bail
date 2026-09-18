@extends('layouts.app')
@section('title', 'Nouveau bordereau')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Nouveau bordereau</h1>
        <p class="text-sm text-slate-500 mt-0.5">Regrouper des contrats « en attente » pour validation</p>
    </div>
    <a href="{{ route('bordereaux.index') }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour à la liste
    </a>
</div>

<form method="POST" action="{{ route('bordereaux.store') }}" x-data="bordereauForm()">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Colonne contrats --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between" style="border-left: 4px solid #F77F00;">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Contrats à inclure</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $contrats->count() }} contrat(s) en attente disponible(s)</p>
                </div>
                @if($contrats->isNotEmpty())
                <button type="button" @click="toggleAll" class="text-xs font-medium" style="color:#F77F00;">
                    <span x-text="allSelected ? 'Tout désélectionner' : 'Tout sélectionner'"></span>
                </button>
                @endif
            </div>

            @error('contrats')<p class="px-6 pt-3 text-xs text-red-600">{{ $message }}</p>@enderror

            @if($contrats->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                <i class="ti ti-file-off text-3xl text-slate-300 mb-3"></i>
                <p class="text-sm font-semibold text-slate-600">Aucun contrat en attente</p>
                <p class="text-xs mt-1">Créez d'abord des contrats ou validez-les individuellement.</p>
                <a href="{{ route('contrats.create') }}" target="_blank" class="mt-3 text-xs underline" style="color:#F77F00;">Créer un contrat</a>
            </div>
            @else
            <div class="overflow-x-auto max-h-[28rem] overflow-y-auto">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0" style="background:#f8f9fb;">
                        <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-4 py-3 w-10"></th>
                            <th class="px-4 py-3">N° / Policier</th>
                            <th class="px-4 py-3 hidden sm:table-cell">Logement</th>
                            <th class="px-4 py-3 text-right">Taux</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($contrats as $contrat)
                        <tr class="hover:bg-slate-50/70 cursor-pointer" @click="toggle({{ $contrat->id }})">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="contrats[]" value="{{ $contrat->id }}"
                                       :checked="selected.includes({{ $contrat->id }})"
                                       @click.stop="toggle({{ $contrat->id }})"
                                       data-taux="{{ $contrat->taux_bail }}"
                                       class="w-4 h-4 rounded accent-orange-500">
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-mono text-xs font-semibold text-slate-600">{{ $contrat->numero_contrat }}</div>
                                <div class="text-xs text-slate-500">{{ $contrat->policier?->nom }} {{ $contrat->policier?->prenoms }} — {{ $contrat->policier?->grade?->libelle ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell text-xs text-slate-500">{{ $contrat->logementCivil?->reference ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-700">{{ number_format($contrat->taux_bail, 0, ',', ' ') }} F</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Colonne récap --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-4">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-clipboard-list mr-1.5 text-slate-400"></i> Bordereau
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Numéro</label>
                    <div class="w-full px-3 py-2.5 text-sm border border-gray-100 rounded-xl bg-slate-50 text-slate-700 font-mono font-semibold">{{ $numero }}</div>
                    <p class="text-xs text-slate-400 mt-1">Généré automatiquement.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Année <span class="text-red-500">*</span></label>
                    <input type="number" name="annee" value="{{ old('annee', $annee) }}" min="2020" max="2100"
                           class="w-full px-3 py-2.5 text-sm border rounded-xl focus:outline-none focus:ring-2 {{ $errors->has('annee') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                    @error('annee')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Observations</label>
                    <textarea name="observations" rows="2" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">{{ old('observations') }}</textarea>
                </div>
            </div>

            <div class="mt-4 rounded-xl bg-slate-50 p-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Contrats sélectionnés</span>
                    <span class="font-semibold text-slate-800" x-text="selected.length"></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Montant mensuel</span>
                    <span class="font-bold" style="color:#F77F00;"><span x-text="totalFormate"></span> F</span>
                </div>
            </div>

            <button type="submit"
                    class="mt-5 w-full px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                    style="background:#F77F00;">
                <i class="ti ti-check mr-1.5"></i> Créer le bordereau
            </button>
            <a href="{{ route('bordereaux.index') }}"
               class="mt-2 block text-center px-5 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
                Annuler
            </a>
        </div>
    </div>

</div>

</form>

@push('scripts')
<script>
function bordereauForm() {
    return {
        selected: @json(collect(old('contrats', []))->map(fn($i) => (int) $i)->values()),
        tauxMap: @json($contrats->mapWithKeys(fn($c) => [$c->id => (float) $c->taux_bail])),
        get total() {
            return this.selected.reduce((s, id) => s + (this.tauxMap[id] || 0), 0);
        },
        get totalFormate() {
            return new Intl.NumberFormat('fr-FR').format(this.total);
        },
        get allSelected() {
            const ids = Object.keys(this.tauxMap).map(Number);
            return ids.length > 0 && this.selected.length === ids.length;
        },
        toggle(id) {
            const i = this.selected.indexOf(id);
            if (i === -1) this.selected.push(id);
            else this.selected.splice(i, 1);
        },
        toggleAll() {
            const ids = Object.keys(this.tauxMap).map(Number);
            this.selected = this.allSelected ? [] : ids;
        },
    };
}
</script>
@endpush

@endsection
