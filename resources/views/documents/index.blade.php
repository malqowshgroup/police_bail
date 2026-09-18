@extends('layouts.app')
@section('title', 'Documents (GED)')

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
        <h1 class="text-2xl font-bold text-slate-800">Documents</h1>
        <p class="text-sm text-slate-500 mt-0.5">Gestion électronique des documents — {{ number_format($stats['total']) }} fichiers</p>
    </div>
    <a href="{{ route('documents.create') }}"
       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0"
       style="background:#F77F00;">
        <i class="ti ti-upload text-base"></i> Téléverser
    </a>
</div>

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(26,36,64,.10);">
            <i class="ti ti-files text-2xl" style="color:#1a2440;"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['total']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,.14);">
            <i class="ti ti-clock text-2xl text-amber-500"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">En attente</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['en_attente']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(0,154,68,.12);">
            <i class="ti ti-circle-check text-2xl" style="color:#009A44;"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Validés</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['valides']) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(99,102,241,.12);">
            <i class="ti ti-database text-2xl text-indigo-500"></i>
        </div>
        <div>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Volume</p>
            <p class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['volume_ko'] >= 1024 ? number_format($stats['volume_ko']/1024, 1, ',', ' ').' Mo' : $stats['volume_ko'].' Ko' }}</p>
        </div>
    </div>
</div>

{{-- Filtres --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4"
     x-data="{ entiteType: '{{ request('entite_type') }}' }">
    <form method="GET" action="{{ route('documents.index') }}">
        <div class="flex flex-col md:flex-row gap-3 flex-wrap">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Nom du fichier…"
                           class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
            </div>
            <select name="type" class="w-full md:w-56 px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Tous les types</option>
                @foreach($types as $t)
                <option value="{{ $t['value'] }}" {{ request('type') === $t['value'] ? 'selected' : '' }}>{{ $t['label'] }}</option>
                @endforeach
            </select>
            <select name="entite_type" x-model="entiteType"
                    class="w-full md:w-44 px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Tout rattachement</option>
                @foreach($entites as $key => $lib)
                <option value="{{ $key }}" {{ request('entite_type') === $key ? 'selected' : '' }}>{{ $lib }}</option>
                @endforeach
            </select>
            <select name="statut" class="w-full md:w-40 px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Tous les statuts</option>
                @foreach($statuts as $st)
                <option value="{{ $st['value'] }}" {{ request('statut') === $st['value'] ? 'selected' : '' }}>{{ $st['label'] }}</option>
                @endforeach
            </select>
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#1a2440;">
                    <i class="ti ti-search text-sm"></i>
                </button>
                @if(request()->hasAny(['search','type','entite_type','entite_id','statut']))
                <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                    <i class="ti ti-x text-sm"></i>
                </a>
                @endif
            </div>
        </div>

        {{-- Sélecteur d'entité spécifique (conditionnel) --}}
        <div class="mt-3" x-show="entiteType === 'policier' || entiteType === 'logement_civil' || entiteType === 'proprietaire'" x-cloak>
            <div class="flex items-center gap-2">
                <i class="ti ti-filter-search text-slate-400 text-sm flex-shrink-0"></i>
                <span class="text-xs text-slate-500 whitespace-nowrap">Filtrer par :</span>

                {{-- Policier --}}
                <select name="entite_id"
                        x-show="entiteType === 'policier'" x-cloak
                        :disabled="entiteType !== 'policier'"
                        class="flex-1 max-w-xs px-3 py-2 text-sm border border-blue-200 rounded-xl bg-blue-50 focus:outline-none focus:ring-2">
                    <option value="">Tous les policiers</option>
                    @foreach($policiers as $p)
                    <option value="{{ $p->id }}" {{ request('entite_id') == $p->id && request('entite_type') === 'policier' ? 'selected' : '' }}>
                        {{ $p->nom }} {{ $p->prenoms }} — {{ $p->matricule }}
                    </option>
                    @endforeach
                </select>

                {{-- Logement --}}
                <select name="entite_id"
                        x-show="entiteType === 'logement_civil'" x-cloak
                        :disabled="entiteType !== 'logement_civil'"
                        class="flex-1 max-w-xs px-3 py-2 text-sm border border-orange-200 rounded-xl bg-orange-50 focus:outline-none focus:ring-2">
                    <option value="">Tous les logements</option>
                    @foreach($logements as $l)
                    <option value="{{ $l->id }}" {{ request('entite_id') == $l->id && request('entite_type') === 'logement_civil' ? 'selected' : '' }}>
                        {{ $l->reference }} — {{ $l->quartier }}
                    </option>
                    @endforeach
                </select>

                {{-- Propriétaire --}}
                <select name="entite_id"
                        x-show="entiteType === 'proprietaire'" x-cloak
                        :disabled="entiteType !== 'proprietaire'"
                        class="flex-1 max-w-xs px-3 py-2 text-sm border border-purple-200 rounded-xl bg-purple-50 focus:outline-none focus:ring-2">
                    <option value="">Tous les propriétaires</option>
                    @foreach($proprietaires as $pr)
                    <option value="{{ $pr->id }}" {{ request('entite_id') == $pr->id && request('entite_type') === 'proprietaire' ? 'selected' : '' }}>
                        {{ $pr->raison_sociale ?? trim($pr->nom.' '.($pr->prenoms ?? '')) }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between" style="background:#f8f9fb;">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            @if($documents->total() > 0)
                {{ $documents->firstItem() }}–{{ $documents->lastItem() }} sur {{ number_format($documents->total()) }} résultats
            @else Aucun résultat @endif
        </span>
    </div>

    @if($documents->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <i class="ti ti-files-off text-3xl text-slate-300"></i>
        </div>
        <p class="text-base font-semibold text-slate-600">Aucun document trouvé</p>
        <p class="text-sm mt-1">Téléversez un premier document.</p>
        <a href="{{ route('documents.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white" style="background:#F77F00;">
            <i class="ti ti-upload text-sm"></i> Téléverser
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100" style="background:#f8f9fb;">
                    <th class="px-5 py-3">Fichier</th>
                    <th class="px-5 py-3 hidden md:table-cell">Type</th>
                    <th class="px-5 py-3 text-center hidden sm:table-cell">Rattachements</th>
                    <th class="px-5 py-3 text-right hidden lg:table-cell">Taille</th>
                    <th class="px-5 py-3 text-center">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($documents as $document)
                @php
                    $previewData = json_encode([
                        'url'         => route('documents.preview', $document),
                        'nom'         => $document->nom_original,
                        'format'      => strtolower($document->format ?? ''),
                        'statutLabel' => $document->statutEnum->label(),
                        'statutBadge' => $document->statutEnum->badge(),
                        'downloadUrl' => route('documents.download', $document),
                        'ficheUrl'    => route('documents.show', $document),
                    ]);
                @endphp
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg flex items-center justify-center flex-shrink-0 bg-slate-100">
                                <i class="ti {{ $document->icone }} text-lg text-slate-500"></i>
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 truncate max-w-[220px]">{{ $document->nom_original }}</div>
                                <div class="text-xs text-slate-400">{{ $document->format }} &bull; {{ $document->created_at?->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $document->typeEnum->badge() }}">{{ $document->typeEnum->label() }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-center hidden sm:table-cell">
                        @if($document->liens_count > 0)
                        <span class="inline-flex items-center gap-1 text-xs text-slate-600"><i class="ti ti-link text-slate-400"></i> {{ $document->liens_count }}</span>
                        @else <span class="text-slate-300">—</span> @endif
                    </td>
                    <td class="px-5 py-3.5 text-right hidden lg:table-cell text-slate-600 text-xs">{{ $document->taille_humaine }}</td>
                    <td class="px-5 py-3.5 text-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $document->statutEnum->badge() }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $document->statutEnum->dot() }} inline-block"></span>
                            {{ $document->statutEnum->label() }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            {{-- Voir la fiche --}}
                            <a href="{{ route('documents.show', $document) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Voir la fiche">
                                <i class="ti ti-eye text-base"></i>
                            </a>
                            {{-- Prévisualiser --}}
                            <button type="button"
                                    data-preview="{{ $previewData }}"
                                    onclick="window.dispatchEvent(new CustomEvent('open-preview',{detail:JSON.parse(this.dataset.preview),bubbles:true}))"
                                    class="p-1.5 rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition" title="Prévisualiser">
                                <i class="ti ti-maximize text-base"></i>
                            </button>
                            {{-- Télécharger --}}
                            <a href="{{ route('documents.download', $document) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-green-600 hover:bg-green-50 transition" title="Télécharger">
                                <i class="ti ti-download text-base"></i>
                            </a>
                            {{-- Modifier --}}
                            <a href="{{ route('documents.edit', $document) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Modifier">
                                <i class="ti ti-pencil text-base"></i>
                            </a>
                            {{-- Supprimer --}}
                            <form method="POST" action="{{ route('documents.destroy', $document) }}"
                                  onsubmit="return confirm('Supprimer définitivement ce document ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-red-600 hover:bg-red-50 transition" title="Supprimer">
                                    <i class="ti ti-trash text-base"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if($documents->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3" style="background:#f8f9fb;">
        <p class="text-xs text-slate-500">Page {{ $documents->currentPage() }} sur {{ $documents->lastPage() }}</p>
        {{ $documents->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
