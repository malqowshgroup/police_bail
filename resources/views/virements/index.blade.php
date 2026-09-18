@extends('layouts.app')
@section('title', 'Virements')

@section('content')

@if(session('success'))
<div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-circle-check text-lg text-green-500"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
    <i class="ti ti-alert-circle text-lg text-red-500"></i> {{ session('error') }}
</div>
@endif

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Virements</h1>
        <p class="text-sm text-slate-500 mt-0.5">Ordres de virement vers les bailleurs — {{ number_format($stats['total']) }} virements</p>
    </div>
    <a href="{{ route('virements.create') }}"
       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0"
       style="background:#F77F00;">
        <i class="ti ti-plus text-base"></i> Nouveau virement
    </a>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(26,36,64,.10);">
            <i class="ti ti-arrows-transfer-down text-2xl" style="color:#1a2440;"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['total']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(59,130,246,.12);">
            <i class="ti ti-progress text-2xl text-blue-500"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">En cours</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['en_cours']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(0,154,68,.12);">
            <i class="ti ti-circle-check text-2xl" style="color:#009A44;"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total exécuté</p>
            <p class="text-xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['montant_execute'], 0, ',', ' ') }} F</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,.14);">
            <i class="ti ti-clock text-2xl text-amber-500"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Règlts en attente</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['reglements_attente']) }}</p>
        </div>
    </div>
</div>

{{-- Filtres --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('virements.index') }}">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 min-w-0">
                <div class="relative">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="N° de virement, bailleur…"
                           class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
            </div>
            <div class="w-full md:w-48">
                <select name="statut" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $st)
                    <option value="{{ $st['value'] }}" {{ request('statut') === $st['value'] ? 'selected' : '' }}>{{ $st['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#1a2440;">
                    <i class="ti ti-search text-sm"></i><span class="hidden sm:inline ml-1">Filtrer</span>
                </button>
                @if(request()->hasAny(['search','statut']))
                <a href="{{ route('virements.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                    <i class="ti ti-x text-sm"></i>
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between" style="background:#f8f9fb;">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            @if($virements->total() > 0)
                {{ $virements->firstItem() }}–{{ $virements->lastItem() }} sur {{ number_format($virements->total()) }} résultats
            @else Aucun résultat @endif
        </span>
    </div>

    @if($virements->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <i class="ti ti-arrows-transfer-down text-3xl text-slate-300"></i>
        </div>
        <p class="text-base font-semibold text-slate-600">Aucun virement trouvé</p>
        <p class="text-sm mt-1">Regroupez des règlements en attente pour générer un virement.</p>
        <a href="{{ route('virements.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white" style="background:#F77F00;">
            <i class="ti ti-plus text-sm"></i> Nouveau virement
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100" style="background:#f8f9fb;">
                    <th class="px-5 py-3">N° Virement</th>
                    <th class="px-5 py-3">Bailleur</th>
                    <th class="px-5 py-3 hidden md:table-cell">Banque</th>
                    <th class="px-5 py-3 text-center">Règlts</th>
                    <th class="px-5 py-3 text-right">Montant</th>
                    <th class="px-5 py-3 text-center">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($virements as $virement)
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">{{ $virement->numero }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="font-semibold text-slate-800">{{ $virement->proprietaire?->nom_complet ?? '—' }}</div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell text-slate-600 text-xs">{{ $virement->banque ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center justify-center min-w-7 px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">{{ $virement->reglements_count }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-right font-semibold text-slate-700">{{ number_format($virement->montant_total, 0, ',', ' ') }} F</td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $virement->statutEnum->badge() }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $virement->statutEnum->dot() }} inline-block"></span>
                            {{ $virement->statutEnum->label() }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('virements.show', $virement) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Voir">
                                <i class="ti ti-eye text-base"></i>
                            </a>
                            @if($virement->isEnPreparation())
                            <a href="{{ route('virements.edit', $virement) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Modifier">
                                <i class="ti ti-pencil text-base"></i>
                            </a>
                            <form method="POST" action="{{ route('virements.destroy', $virement) }}"
                                  onsubmit="return confirm('Supprimer le virement {{ addslashes($virement->numero) }} ? Les règlements seront libérés.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 transition" title="Supprimer">
                                    <i class="ti ti-trash text-base"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($virements->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3" style="background:#f8f9fb;">
        <p class="text-xs text-slate-500">Page {{ $virements->currentPage() }} sur {{ $virements->lastPage() }}</p>
        {{ $virements->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
