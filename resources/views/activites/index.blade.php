@extends('layouts.app')
@section('title', 'Journal d\'activités')

@section('content')

@php
    $actionCfg = \App\Support\ActivityLogger::ACTIONS;
    $modulesMap = \App\Support\ActivityLogger::MODULES;
    $badgeAction = function(string $a) use ($actionCfg): string {
        $cfg = $actionCfg[$a] ?? ['Inconnu', 'bg-slate-100 text-slate-600', 'ti-help'];
        return sprintf('<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold %s"><i class="ti %s text-xs"></i>%s</span>', $cfg[1], $cfg[2], $cfg[0]);
    };
@endphp

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Journal d'activités</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ number_format($total) }} événements enregistrés — toutes actions confondues</p>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 text-slate-600">
            <i class="ti ti-database text-xs"></i> {{ number_format($activites->total()) }} résultats
        </span>
    </div>
</div>

{{-- Filtres --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-4">
    <form method="GET" action="{{ route('parametres.activites.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- Recherche --}}
            <div class="lg:col-span-2 relative">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Entité, utilisateur…"
                       class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
            </div>

            {{-- Action --}}
            <select name="action" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Toutes les actions</option>
                @foreach($actions as $key => [$label])
                <option value="{{ $key }}" {{ request('action') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            {{-- Module --}}
            <select name="module" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Tous les modules</option>
                @foreach($modules as $entiteType => $nb)
                <option value="{{ $entiteType }}" {{ request('module') === $entiteType ? 'selected' : '' }}>
                    {{ $modulesMap[$entiteType] ?? $entiteType }} ({{ $nb }})
                </option>
                @endforeach
            </select>

            {{-- Utilisateur --}}
            <select name="user_id" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-xl bg-white focus:outline-none">
                <option value="">Tous les utilisateurs</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- Date range --}}
        <div class="mt-3 flex flex-col sm:flex-row gap-3 items-center">
            <div class="flex items-center gap-2 flex-1">
                <span class="text-xs text-slate-500 whitespace-nowrap">Du</span>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
                <span class="text-xs text-slate-500 whitespace-nowrap">au</span>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="flex-1 px-3 py-2 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2">
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-white transition hover:opacity-90"
                        style="background:#1a2440;">
                    <i class="ti ti-search text-sm"></i> Filtrer
                </button>
                @if(request()->hasAny(['search','action','module','user_id','date_debut','date_fin']))
                <a href="{{ route('parametres.activites.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 bg-gray-100 hover:bg-gray-200 transition">
                    <i class="ti ti-x text-sm"></i> Effacer
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Compteurs actions --}}
<div class="flex flex-wrap gap-2 mb-4">
    @foreach($actions as $key => [$label, $badge, $icon])
    <a href="{{ route('parametres.activites.index', array_merge(request()->except('page'), ['action' => $key])) }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition
              {{ request('action') === $key ? 'ring-2 ring-offset-1 ring-slate-400 ' : 'hover:brightness-95 ' }}{{ $badge }}">
        <i class="ti {{ $icon }} text-xs"></i> {{ $label }}
    </a>
    @endforeach
</div>

{{-- Journal --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between" style="background:#f8f9fb;">
        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
            @if($activites->total() > 0)
                {{ $activites->firstItem() }}–{{ $activites->lastItem() }} sur {{ number_format($activites->total()) }} événements
            @else Aucun événement @endif
        </span>
        @if(request()->hasAny(['search','action','module','user_id','date_debut','date_fin']))
        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">
            <i class="ti ti-filter text-xs"></i> Filtre actif
        </span>
        @endif
    </div>

    @if($activites->isEmpty())
    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
        <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
            <i class="ti ti-activity text-3xl text-slate-300"></i>
        </div>
        <p class="text-base font-semibold text-slate-600">Aucune activité enregistrée</p>
        <p class="text-sm mt-1">Les actions apparaîtront ici au fil de l'utilisation.</p>
    </div>
    @else
    <div class="divide-y divide-gray-50">
        @foreach($activites as $activite)
        @php
            $cfg  = $actionCfg[$activite->action] ?? ['Inconnu', 'bg-slate-100 text-slate-600', 'ti-help'];
            $avnt = $activite->avant;
            $aprs = $activite->apres;
            $hasDiff = !empty($avnt) || !empty($aprs);
            $initiales = mb_strtoupper(mb_substr($activite->user_nom, 0, 2));
        @endphp
        <div class="px-5 py-4 hover:bg-slate-50/60 transition-colors group" x-data="{{ $hasDiff ? '{ open: false }' : '{}' }}">
            <div class="flex items-start gap-4">

                {{-- Avatar utilisateur --}}
                <div class="h-9 w-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 mt-0.5"
                     style="background:{{ ['#F77F00','#1a2440','#009A44','#3B82F6','#8B5CF6'][crc32($activite->user_nom) % 5] }};">
                    {{ $initiales }}
                </div>

                {{-- Contenu --}}
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        {{-- Badge action --}}
                        {!! sprintf('<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold %s"><i class="ti %s text-xs"></i>%s</span>', $cfg[1], $cfg[2], $cfg[0]) !!}

                        {{-- Module --}}
                        <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">
                            {{ $modulesMap[$activite->entite_type] ?? $activite->entite_type }}
                        </span>

                        {{-- Entité --}}
                        @if($activite->entite_label)
                        <span class="text-xs text-slate-500 font-mono">{{ $activite->entite_label }}</span>
                        @endif
                    </div>

                    {{-- Auteur + date --}}
                    <p class="text-xs text-slate-500">
                        <span class="font-medium text-slate-700">{{ $activite->user_nom }}</span>
                        &bull;
                        <span title="{{ $activite->created_at?->format('d/m/Y H:i:s') }}">
                            {{ $activite->created_at?->diffForHumans() }}
                        </span>
                        @if($activite->ip_address)
                        <span class="text-slate-300 mx-1">·</span>
                        <span class="text-slate-400 font-mono text-[10px]">{{ $activite->ip_address }}</span>
                        @endif
                    </p>

                    {{-- Diff avant/après --}}
                    @if($hasDiff)
                    <div class="mt-2">
                        @php $nbChamps = count($aprs); @endphp
                        <button type="button" @click="open = !open"
                                class="text-xs text-blue-600 hover:text-blue-800 flex items-center gap-1 font-medium">
                            <i class="ti ti-arrows-diff text-xs"></i>
                            <span x-show="!open">Voir le détail ({{ $nbChamps }} champ{{ $nbChamps > 1 ? 's' : '' }})</span>
                            <span x-show="open" x-cloak>Masquer le détail</span>
                            <i class="ti ti-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak class="mt-2 rounded-xl border border-gray-100 overflow-hidden text-xs">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-50 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                                        <th class="px-3 py-1.5 text-left">Champ</th>
                                        <th class="px-3 py-1.5 text-left">Avant</th>
                                        <th class="px-3 py-1.5 text-left">Après</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($aprs as $champ => $valApres)
                                    <tr>
                                        <td class="px-3 py-1.5 font-mono text-slate-500 whitespace-nowrap">{{ $champ }}</td>
                                        <td class="px-3 py-1.5 text-red-600 line-through max-w-[200px] truncate">
                                            {{ is_array($avnt[$champ] ?? null) ? json_encode($avnt[$champ]) : ($avnt[$champ] ?? '—') }}
                                        </td>
                                        <td class="px-3 py-1.5 text-green-700 font-medium max-w-[200px] truncate">
                                            {{ is_array($valApres) ? json_encode($valApres) : $valApres }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Date heure compacte --}}
                <div class="text-right flex-shrink-0 text-[10px] text-slate-400 whitespace-nowrap hidden sm:block">
                    <div>{{ $activite->created_at?->format('d/m/Y') }}</div>
                    <div class="font-mono">{{ $activite->created_at?->format('H:i:s') }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($activites->hasPages())
    <div class="px-5 py-3.5 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3" style="background:#f8f9fb;">
        <p class="text-xs text-slate-500">Page {{ $activites->currentPage() }} sur {{ $activites->lastPage() }}</p>
        {{ $activites->links() }}
    </div>
    @endif
    @endif
</div>

@endsection
