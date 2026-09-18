@extends('layouts.app')
@section('title', 'Nouveau virement')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Nouveau virement</h1>
        <p class="text-sm text-slate-500 mt-0.5">Regrouper les règlements en attente d'un bailleur</p>
    </div>
    <a href="{{ route('virements.index') }}"
       class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition flex-shrink-0">
        <i class="ti ti-arrow-left text-sm"></i> Retour à la liste
    </a>
</div>

{{-- Étape 1 : choix du bailleur (recharge la page) --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-4">
    <form method="GET" action="{{ route('virements.create') }}">
        <label class="block text-xs font-semibold text-slate-600 mb-1.5">Propriétaire bailleur <span class="text-red-500">*</span></label>
        <div class="flex flex-col sm:flex-row gap-3">
            <select name="proprietaire_id" onchange="this.form.submit()"
                    class="flex-1 px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">— Sélectionner un bailleur ({{ $proprietaires->count() }} avec règlements en attente) —</option>
                @foreach($proprietaires as $prop)
                <option value="{{ $prop->id }}" {{ $proprietaire?->id === $prop->id ? 'selected' : '' }}>
                    {{ $prop->nom_complet }} — {{ $prop->reglements_count }} règlt(s) · {{ number_format($prop->reglements_total, 0, ',', ' ') }} F
                </option>
                @endforeach
            </select>
            <noscript><button type="submit" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-white" style="background:#1a2440;">Charger</button></noscript>
        </div>
        @if($proprietaires->isEmpty())
        <p class="text-xs text-amber-600 mt-2"><i class="ti ti-alert-triangle"></i> Aucun bailleur n'a de règlement « en attente de virement ». Placez d'abord des règlements en attente.</p>
        @endif
    </form>
</div>

@if($proprietaire)
<form method="POST" action="{{ route('virements.store') }}" x-data="virementForm()">
@csrf
<input type="hidden" name="proprietaire_id" value="{{ $proprietaire->id }}">

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

    {{-- Règlements --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between" style="border-left: 4px solid #F77F00;">
                <div>
                    <h2 class="text-sm font-semibold text-slate-800">Règlements à virer</h2>
                    <p class="text-xs text-slate-500 mt-0.5">{{ $reglements->count() }} règlement(s) en attente pour {{ $proprietaire->nom_complet }}</p>
                </div>
                @if($reglements->isNotEmpty())
                <button type="button" @click="toggleAll" class="text-xs font-medium" style="color:#F77F00;">
                    <span x-text="allSelected ? 'Tout désélectionner' : 'Tout sélectionner'"></span>
                </button>
                @endif
            </div>

            @error('reglements')<p class="px-6 pt-3 text-xs text-red-600">{{ $message }}</p>@enderror

            @if($reglements->isEmpty())
            <p class="text-sm text-slate-400 py-12 text-center">Aucun règlement en attente pour ce bailleur.</p>
            @else
            <div class="overflow-x-auto max-h-[26rem] overflow-y-auto">
                <table class="min-w-full text-sm">
                    <thead class="sticky top-0" style="background:#f8f9fb;">
                        <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                            <th class="px-4 py-3 w-10"></th>
                            <th class="px-4 py-3">Contrat / Policier</th>
                            <th class="px-4 py-3 hidden sm:table-cell">Période</th>
                            <th class="px-4 py-3 text-right">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($reglements as $r)
                        <tr class="hover:bg-slate-50/70 cursor-pointer" @click="toggle({{ $r->id }})">
                            <td class="px-4 py-3">
                                <input type="checkbox" name="reglements[]" value="{{ $r->id }}"
                                       :checked="selected.includes({{ $r->id }})" @click.stop="toggle({{ $r->id }})"
                                       class="w-4 h-4 rounded accent-orange-500">
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-mono text-xs font-semibold text-slate-600">{{ $r->contratBail?->numero_contrat }}</div>
                                <div class="text-xs text-slate-500">{{ $r->contratBail?->policier?->nom }} {{ $r->contratBail?->policier?->prenoms }}</div>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell text-xs text-slate-500">{{ $r->periode_label }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-slate-700">{{ number_format($r->montant, 0, ',', ' ') }} F</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    {{-- Récap --}}
    <div class="space-y-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-4">
            <h2 class="text-sm font-semibold text-slate-700 mb-4 pb-2 border-b border-gray-100">
                <i class="ti ti-arrows-transfer-down mr-1.5 text-slate-400"></i> Virement
            </h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Numéro</label>
                    <div class="w-full px-3 py-2.5 text-sm border border-gray-100 rounded-xl bg-slate-50 text-slate-700 font-mono font-semibold">{{ $numero }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Bailleur</label>
                    <div class="w-full px-3 py-2.5 text-sm border border-gray-100 rounded-xl bg-slate-50 text-slate-700">{{ $proprietaire->nom_complet }}</div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Banque du bailleur</label>
                    <input type="text" name="banque" value="{{ old('banque') }}" placeholder="Ex: SGBCI, NSIA…"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Date prévue</label>
                    <input type="date" name="date_virement" value="{{ old('date_virement') }}"
                           class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1.5">Observations</label>
                    <textarea name="observations" rows="2" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">{{ old('observations') }}</textarea>
                </div>
            </div>

            <div class="mt-4 rounded-xl bg-slate-50 p-4 space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Règlements</span>
                    <span class="font-semibold text-slate-800" x-text="selected.length"></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Montant total</span>
                    <span class="font-bold" style="color:#F77F00;"><span x-text="totalFormate"></span> F</span>
                </div>
            </div>

            <button type="submit" :disabled="selected.length === 0"
                    class="mt-5 w-full px-6 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90 disabled:opacity-40 disabled:cursor-not-allowed"
                    style="background:#F77F00;">
                <i class="ti ti-check mr-1.5"></i> Créer le virement
            </button>
        </div>
    </div>

</div>

</form>

@push('scripts')
<script>
function virementForm() {
    return {
        selected: @json(collect(old('reglements', []))->map(fn($i) => (int) $i)->values()),
        tauxMap: @json($reglements->mapWithKeys(fn($r) => [$r->id => (float) $r->montant])),
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
@endif

@endsection
