@extends('layouts.app')
@section('title', 'Logements civils')

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
        <h1 class="text-2xl font-bold text-slate-800">Logements civils</h1>
        <p class="text-sm text-slate-500 mt-0.5">Parc immobilier géré — {{ number_format($stats['total']) }} logements</p>
    </div>
    <a href="{{ route('logements.create') }}"
       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0"
       style="background:#F77F00;">
        <i class="ti ti-plus text-base"></i> Ajouter un logement
    </a>
</div>

{{-- Alerte déficit logement --}}
@if($stats['deficit'] > 0)
<div class="mb-5 rounded-2xl border-2 border-red-300 p-4 flex flex-col sm:flex-row sm:items-center gap-4"
     style="background:linear-gradient(135deg,#fff5f5 0%,#fff1ee 100%);">
    <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-red-100 flex items-center justify-center">
        <i class="ti ti-home-exclamation text-2xl text-red-600"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-red-800">
            <i class="ti ti-alert-triangle mr-1"></i>
            Déficit de {{ number_format($stats['deficit']) }} logement{{ $stats['deficit'] > 1 ? 's' : '' }}
        </p>
        <p class="text-xs text-red-700 mt-0.5">
            <strong>{{ number_format($stats['policiers_sans_logement']) }} policiers actifs</strong> sans logement attribué
            pour seulement <strong>{{ number_format($stats['libres']) }}</strong> logement{{ $stats['libres'] > 1 ? 's' : '' }} disponible{{ $stats['libres'] > 1 ? 's' : '' }}.
            Envisagez d'intégrer de nouveaux logements au parc.
        </p>
    </div>
    <a href="{{ route('logements.create') }}"
       class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition">
        <i class="ti ti-plus text-sm"></i> Ajouter un logement
    </a>
</div>
@elseif($stats['libres'] > 0 && $stats['libres'] <= 3)
<div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-3.5 flex items-center gap-3">
    <i class="ti ti-alert-circle text-lg text-amber-500 flex-shrink-0"></i>
    <p class="text-sm text-amber-800">
        <strong>Stock faible :</strong> seulement {{ number_format($stats['libres']) }} logement{{ $stats['libres'] > 1 ? 's' : '' }}  disponible{{ $stats['libres'] > 1 ? 's' : '' }}
        pour {{ number_format($stats['policiers_sans_logement']) }} policiers sans logement.
    </p>
</div>
@elseif($stats['deficit'] === 0 && $stats['libres'] > 0)
<div class="mb-5 rounded-2xl border border-green-200 bg-green-50 p-3.5 flex items-center gap-3">
    <i class="ti ti-circle-check text-lg text-green-500 flex-shrink-0"></i>
    <p class="text-sm text-green-800">
        Situation satisfaisante — {{ number_format($stats['libres']) }} logement{{ $stats['libres'] > 1 ? 's' : '' }} disponible{{ $stats['libres'] > 1 ? 's' : '' }}
        pour {{ number_format($stats['policiers_sans_logement']) }} policiers sans logement (surplus de {{ number_format($stats['libres'] - $stats['policiers_sans_logement']) }}).
    </p>
</div>
@endif

{{-- Stat cards --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-3">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(26,36,64,.10);">
            <i class="ti ti-building text-xl" style="color:#1a2440;"></i>
        </div>
        <div>
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Total</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['total']) }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">logements</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(0,154,68,.12);">
            <i class="ti ti-home-check text-xl" style="color:#009A44;"></i>
        </div>
        <div>
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Occupés</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['occupes']) }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">{{ $stats['taux_occupation'] }}% du parc</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-3
        {{ $stats['deficit'] > 0 ? 'border-2 border-red-300' : ($stats['libres'] <= 3 && $stats['libres'] > 0 ? 'border border-amber-200' : 'border border-gray-100') }}"
        style="{{ $stats['deficit'] > 0 ? 'background:rgba(239,68,68,.04);' : '' }}">
        <div class="h-11 w-11 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:{{ $stats['deficit'] > 0 ? 'rgba(239,68,68,.12)' : 'rgba(247,127,0,.12)' }};">
            <i class="ti ti-home text-xl {{ $stats['deficit'] > 0 ? 'text-red-500' : '' }}" style="{{ $stats['deficit'] > 0 ? '' : 'color:#F77F00;' }}"></i>
        </div>
        <div>
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Disponibles</p>
            <p class="text-2xl font-bold mt-0.5 {{ $stats['deficit'] > 0 ? 'text-red-700' : 'text-slate-800' }}">{{ number_format($stats['libres']) }}</p>
            <p class="text-[10px] {{ $stats['deficit'] > 0 ? 'text-red-500 font-semibold' : 'text-slate-400' }} mt-0.5">
                {{ $stats['deficit'] > 0 ? 'Déficit '.$stats['deficit'] : 'logements libres' }}
            </p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(99,102,241,.12);">
            <i class="ti ti-users text-xl text-indigo-500"></i>
        </div>
        <div>
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Propriétaires</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['proprietaires']) }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">bailleurs</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-3">
        <div class="h-11 w-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(59,130,246,.12);">
            <i class="ti ti-user-exclamation text-xl text-blue-500"></i>
        </div>
        <div>
            <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider">Sans logement</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ number_format($stats['policiers_sans_logement']) }}</p>
            <p class="text-[10px] text-slate-400 mt-0.5">policiers actifs</p>
        </div>
    </div>
</div>

{{-- Barre de taux d'occupation --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-3.5 mb-4 flex items-center gap-4">
    <span class="text-xs font-semibold text-slate-500 whitespace-nowrap">Taux d'occupation</span>
    <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
        <div class="h-full rounded-full transition-all duration-500"
             style="width:{{ $stats['taux_occupation'] }}%;
                    background:{{ $stats['taux_occupation'] >= 90 ? '#009A44' : ($stats['taux_occupation'] >= 70 ? '#F77F00' : '#94A3B8') }};"></div>
    </div>
    <span class="text-sm font-bold text-slate-700 whitespace-nowrap">{{ $stats['taux_occupation'] }}%</span>
    <span class="text-xs text-slate-400 whitespace-nowrap hidden sm:block">{{ number_format($stats['occupes']) }} / {{ number_format($stats['total']) }}</span>
</div>

{{-- Filtres --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('logements.index') }}">
        <div class="flex flex-col md:flex-row gap-3">
            <div class="flex-1 min-w-0">
                <div class="relative">
                    <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Référence, quartier, adresse…"
                           class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                </div>
            </div>
            <div class="w-full md:w-44">
                <select name="localite_id" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                    <option value="">Toutes les localités</option>
                    @foreach($localites as $loc)
                    <option value="{{ $loc->id }}" {{ request('localite_id') == $loc->id ? 'selected' : '' }}>{{ $loc->libelle }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-52">
                <select name="proprietaire_id" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                    <option value="">Tous les propriétaires</option>
                    @foreach($proprietaires as $prop)
                    <option value="{{ $prop->id }}" {{ request('proprietaire_id') == $prop->id ? 'selected' : '' }}>{{ $prop->nom_complet }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full md:w-36">
                <select name="statut" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                    <option value="">Tous statuts</option>
                    <option value="occupe" {{ request('statut') === 'occupe' ? 'selected' : '' }}>Occupé</option>
                    <option value="libre" {{ request('statut') === 'libre' ? 'selected' : '' }}>Libre</option>
                </select>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90" style="background:#1a2440;">
                    <i class="ti ti-search text-sm"></i><span class="hidden sm:inline ml-1">Filtrer</span>
                </button>
                @if(request()->hasAny(['search','localite_id','proprietaire_id','statut']))
                <a href="{{ route('logements.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
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
            @if($logements->total() > 0)
                {{ $logements->firstItem() }}–{{ $logements->lastItem() }} sur {{ number_format($logements->total()) }} résultats
            @else Aucun résultat @endif
        </span>
        @if(request()->hasAny(['search','localite_id','proprietaire_id','statut']))
        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
            <i class="ti ti-filter text-xs"></i> Filtre actif
        </span>
        @endif
    </div>

    @if($logements->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <i class="ti ti-building text-3xl text-slate-300"></i>
        </div>
        <p class="text-base font-semibold text-slate-600">Aucun logement trouvé</p>
        <p class="text-sm mt-1">Modifiez vos filtres ou ajoutez un logement.</p>
        <a href="{{ route('logements.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white" style="background:#F77F00;">
            <i class="ti ti-plus text-sm"></i> Ajouter un logement
        </a>
    </div>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100" style="background:#f8f9fb;">
                    <th class="px-5 py-3">Référence</th>
                    <th class="px-5 py-3">Quartier / Adresse</th>
                    <th class="px-5 py-3 hidden md:table-cell">Propriétaire</th>
                    <th class="px-5 py-3 hidden lg:table-cell">Localité</th>
                    <th class="px-5 py-3 text-center">Statut</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($logements as $logement)
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    <td class="px-5 py-3.5">
                        <span class="font-mono text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg">
                            {{ $logement->reference }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:rgba(247,127,0,.12);">
                                <i class="ti ti-building text-sm" style="color:#F77F00;"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-slate-800">{{ $logement->quartier }}</div>
                                <div class="text-xs text-slate-500">
                                    @if($logement->ilot || $logement->lot)
                                        {{ $logement->ilot ? 'Îlot '.$logement->ilot : '' }}{{ ($logement->ilot && $logement->lot) ? ' — ' : '' }}{{ $logement->lot ? 'Lot '.$logement->lot : '' }}
                                    @else
                                        {{ Str::limit($logement->adresse_complete ?? '—', 40) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 hidden md:table-cell">
                        <span class="text-slate-700 text-xs font-medium">{{ $logement->proprietaire?->nom_complet ?? '—' }}</span>
                    </td>
                    <td class="px-5 py-3.5 hidden lg:table-cell">
                        @if($logement->localite)
                        <span class="inline-flex items-center gap-1 text-xs text-slate-600">
                            <i class="ti ti-map-pin text-slate-400"></i> {{ $logement->localite->libelle }}
                        </span>
                        @else <span class="text-slate-300">—</span> @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @if($logement->contratsBail->where('statut','actif')->isNotEmpty())
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-500 inline-block"></span> Occupé
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">
                                Libre
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('logements.show', $logement) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition" title="Voir">
                                <i class="ti ti-eye text-base"></i>
                            </a>
                            <a href="{{ route('logements.edit', $logement) }}" class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition" title="Modifier">
                                <i class="ti ti-pencil text-base"></i>
                            </a>
                            <form method="POST" action="{{ route('logements.destroy', $logement) }}"
                                  onsubmit="return confirm('Supprimer le logement {{ addslashes($logement->reference) }} ?')">
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
    @if($logements->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3" style="background:#f8f9fb;">
        <p class="text-xs text-slate-500">Page {{ $logements->currentPage() }} sur {{ $logements->lastPage() }}</p>
        {{ $logements->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
