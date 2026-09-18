@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')

@php
    $fmt = fn($v) => number_format($v, 0, ',', ' ');
    $fmtMontant = function ($v) {
        if ($v >= 1_000_000_000) return number_format($v / 1_000_000_000, 2, ',', ' ').' Md';
        if ($v >= 1_000_000)     return number_format($v / 1_000_000, 1, ',', ' ').' M';
        if ($v >= 1_000)         return number_format($v / 1_000, 0, ',', ' ').' k';
        return number_format($v, 0, ',', ' ');
    };
    $moisFr = ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
@endphp

{{-- ═══════════════════ ALERTE DÉFICIT LOGEMENT ═══════════════════ --}}
@if($kpis['deficit_logement'] > 0)
<div class="mb-5 rounded-2xl border-2 border-red-300 p-4 flex flex-col sm:flex-row sm:items-center gap-4"
     style="background:linear-gradient(135deg,#fff5f5 0%,#fff1ee 100%);">
    <div class="flex-shrink-0 h-12 w-12 rounded-xl bg-red-100 flex items-center justify-center">
        <i class="ti ti-home-exclamation text-2xl text-red-600"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-red-800">
            <i class="ti ti-alert-triangle mr-1"></i>
            Déficit de logements — {{ $fmt($kpis['deficit_logement']) }} logement{{ $kpis['deficit_logement'] > 1 ? 's' : '' }} manquant{{ $kpis['deficit_logement'] > 1 ? 's' : '' }}
        </p>
        <p class="text-xs text-red-700 mt-0.5">
            {{ $fmt($kpis['policiers_sans_logement']) }} policiers actifs sans logement attribué
            pour seulement <strong>{{ $fmt($kpis['logements_libres']) }}</strong> logement{{ $kpis['logements_libres'] > 1 ? 's' : '' }} disponible{{ $kpis['logements_libres'] > 1 ? 's' : '' }}.
        </p>
    </div>
    <a href="{{ route('logements.index', ['statut' => 'libre']) }}"
       class="flex-shrink-0 inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition">
        <i class="ti ti-building text-sm"></i> Voir les logements libres
    </a>
</div>
@elseif($kpis['logements_libres'] > 0 && $kpis['logements_libres'] <= 3)
<div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-3.5 flex items-center gap-3">
    <i class="ti ti-alert-circle text-lg text-amber-500 flex-shrink-0"></i>
    <p class="text-sm text-amber-800">
        <strong>Attention :</strong> seulement {{ $fmt($kpis['logements_libres']) }} logement{{ $kpis['logements_libres'] > 1 ? 's' : '' }} disponible{{ $kpis['logements_libres'] > 1 ? 's' : '' }}
        pour {{ $fmt($kpis['policiers_sans_logement']) }} policiers sans logement.
        <a href="{{ route('logements.index') }}" class="underline font-medium">Gérer les logements</a>
    </p>
</div>
@endif

{{-- ═══════════════════════════════ KPI ROW ═══════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">

    {{-- Contrats actifs --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(247,127,0,.12);">
            <i class="ti ti-file-text text-2xl" style="color:#F77F00;"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Contrats actifs</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $fmt($kpis['contrats_actifs']) }}</p>
            @if($kpis['contrats_nouveaux'] > 0)
            <p class="text-xs mt-1 font-medium" style="color:#009A44;"><i class="ti ti-trending-up text-xs"></i> +{{ $kpis['contrats_nouveaux'] }} ce mois</p>
            @else
            <p class="text-xs mt-1 text-slate-400">Aucun nouveau ce mois</p>
            @endif
        </div>
    </div>

    {{-- Loyers du mois --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(0,154,68,.12);">
            <i class="ti ti-cash text-2xl" style="color:#009A44;"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Loyers du mois</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $fmtMontant($kpis['loyers_mois']) }} F</p>
            @if($kpis['loyers_evolution'] !== null && $kpis['loyers_evolution'] != 0)
            <p class="text-xs mt-1 font-medium {{ $kpis['loyers_evolution'] >= 0 ? '' : 'text-red-500' }}" style="{{ $kpis['loyers_evolution'] >= 0 ? 'color:#009A44;' : '' }}">
                <i class="ti ti-trending-{{ $kpis['loyers_evolution'] >= 0 ? 'up' : 'down' }} text-xs"></i>
                {{ $kpis['loyers_evolution'] >= 0 ? '+' : '' }}{{ $kpis['loyers_evolution'] }}% vs mois préc.
            </p>
            @else
            <p class="text-xs mt-1 text-slate-400">{{ $moisFr[now()->month] }} {{ now()->year }}</p>
            @endif
        </div>
    </div>

    {{-- Règlements à payer --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(239,68,68,.10);">
            <i class="ti ti-alert-circle text-2xl text-red-500"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Règlements à payer</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $fmt($kpis['a_payer']) }}</p>
            <p class="text-xs mt-1 text-slate-400">{{ $fmtMontant($kpis['a_payer_montant']) }} F en attente</p>
        </div>
    </div>

    {{-- Baux suspendus --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(245,158,11,.12);">
            <i class="ti ti-clock-pause text-2xl text-amber-500"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Baux suspendus</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $fmt($kpis['suspendus']) }}</p>
            <p class="text-xs mt-1 text-slate-400">En cours de traitement</p>
        </div>
    </div>

    {{-- Logements disponibles --}}
    @php $defLogement = $kpis['deficit_logement'] > 0; @endphp
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border {{ $defLogement ? 'border-red-200' : 'border-gray-100' }}"
         style="{{ $defLogement ? 'background:rgba(239,68,68,.04);' : '' }}">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0"
             style="background:{{ $defLogement ? 'rgba(239,68,68,.12)' : 'rgba(26,36,64,.10)' }};">
            <i class="ti ti-building text-2xl {{ $defLogement ? 'text-red-500' : '' }}" style="{{ $defLogement ? '' : 'color:#1a2440;' }}"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Logements libres</p>
            <p class="text-2xl font-bold mt-0.5 {{ $defLogement ? 'text-red-700' : 'text-slate-800' }}">{{ $fmt($kpis['logements_libres']) }}</p>
            @if($defLogement)
            <p class="text-xs mt-1 font-semibold text-red-600">
                <i class="ti ti-arrow-down-right text-xs"></i> Déficit {{ $fmt($kpis['deficit_logement']) }}
            </p>
            @else
            <p class="text-xs mt-1 text-slate-400">{{ $kpis['taux_occupation'] }}% occupation</p>
            @endif
        </div>
    </div>

</div>

{{-- ═══════════════════════════════ CHARTS ═══════════════════════════════ --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">

    {{-- Loyers par localité --}}
    <div class="md:col-span-2 xl:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-800">Loyers actifs par localité</h2>
            <span class="text-xs text-slate-400">{{ $moisFr[now()->month] }} {{ now()->year }} · milliers F</span>
        </div>
        <div style="position:relative; height:208px;"><canvas id="loyersChart"></canvas></div>
    </div>

    {{-- Répartition des contrats --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Répartition des contrats</h2>
        <div class="flex flex-col items-center">
            <div style="position:relative;height:160px;" class="w-full max-w-[200px]"><canvas id="statutsChart"></canvas></div>
            <div class="mt-4 space-y-2 w-full">
                @foreach($charts['statut']['labels'] as $i => $label)
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-1.5"><span class="inline-block h-2.5 w-2.5 rounded-sm" style="background:{{ $charts['statut']['colors'][$i] }};"></span>{{ $label }}</span>
                    <span class="font-semibold text-slate-700">{{ $fmt($charts['statut']['data'][$i]) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════ BOTTOM ROW ═══════════════════════════════ --}}
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

    {{-- Taux par grade --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Taux de bail par grade</h2>
        @php $maxTaux = max(1, $grades->max('taux_bail')); @endphp
        <div class="space-y-3">
            @foreach($grades as $grade)
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-600 truncate pr-2">{{ $grade->libelle }}</span>
                    <span class="font-semibold text-slate-700 flex-shrink-0">{{ $fmt($grade->taux_bail) }} F</span>
                </div>
                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" style="width:{{ round($grade->taux_bail / $maxTaux * 100) }}%; background:#F77F00;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Alertes --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Alertes & files d'attente</h2>
        <div class="space-y-2.5">
            <a href="{{ route('documents.index', ['statut' => 'en_attente']) }}" class="flex items-center justify-between py-2 border-b border-gray-50 hover:bg-slate-50 -mx-2 px-2 rounded-lg transition">
                <span class="text-xs text-slate-700">Documents en attente</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $alertes['docs_attente'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}">{{ $fmt($alertes['docs_attente']) }}</span>
            </a>
            <a href="{{ route('contrats.index', ['statut' => 'en_attente']) }}" class="flex items-center justify-between py-2 border-b border-gray-50 hover:bg-slate-50 -mx-2 px-2 rounded-lg transition">
                <span class="text-xs text-slate-700">Contrats en attente</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $alertes['contrats_attente'] > 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }}">{{ $fmt($alertes['contrats_attente']) }}</span>
            </a>
            <a href="{{ route('reglements.index', ['statut' => 'a_payer']) }}" class="flex items-center justify-between py-2 border-b border-gray-50 hover:bg-slate-50 -mx-2 px-2 rounded-lg transition">
                <span class="text-xs text-slate-700">Règlements à payer</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $alertes['reglements_payer'] > 0 ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-500' }}">{{ $fmt($alertes['reglements_payer']) }}</span>
            </a>
            <a href="{{ route('virements.index') }}" class="flex items-center justify-between py-2 border-b border-gray-50 hover:bg-slate-50 -mx-2 px-2 rounded-lg transition">
                <span class="text-xs text-slate-700">Virements en cours</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $alertes['virements_cours'] > 0 ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-500' }}">{{ $fmt($alertes['virements_cours']) }}</span>
            </a>
            <a href="{{ route('bordereaux.index') }}" class="flex items-center justify-between py-2 border-b border-gray-50 hover:bg-slate-50 -mx-2 px-2 rounded-lg transition">
                <span class="text-xs text-slate-700">Bordereaux en cours</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $alertes['bordereaux_cours'] > 0 ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-500' }}">{{ $fmt($alertes['bordereaux_cours']) }}</span>
            </a>
            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                <span class="text-xs text-slate-700">Documents validés ce mois</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full" style="background:rgba(0,154,68,.1);color:#009A44;">{{ $fmt($alertes['docs_valides_mois']) }}</span>
            </div>
            <a href="{{ route('logements.index', ['statut' => 'libre']) }}"
               class="flex items-center justify-between py-2 hover:bg-slate-50 -mx-2 px-2 rounded-lg transition {{ $alertes['deficit_logement'] > 0 ? 'bg-red-50/50' : '' }}">
                <span class="text-xs {{ $alertes['deficit_logement'] > 0 ? 'text-red-700 font-semibold' : 'text-slate-700' }} flex items-center gap-1">
                    @if($alertes['deficit_logement'] > 0)<i class="ti ti-alert-triangle text-red-500"></i>@else<i class="ti ti-building text-slate-400"></i>@endif
                    Déficit logements
                </span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $alertes['deficit_logement'] > 0 ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $alertes['deficit_logement'] > 0 ? '+' : '' }}{{ $fmt($alertes['deficit_logement']) }}
                </span>
            </a>
        </div>
    </div>

    {{-- Activité récente --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Activité récente</h2>
        @if($activites->isEmpty())
        <p class="text-sm text-slate-400 py-6 text-center">Aucune activité récente.</p>
        @else
        <div class="space-y-0">
            @foreach($activites as $index => $activity)
            <div class="flex gap-3">
                <div class="flex flex-col items-center flex-shrink-0">
                    <div class="h-2.5 w-2.5 rounded-full mt-1.5 flex-shrink-0" style="background:{{ $activity['color'] }};"></div>
                    @if($index < $activites->count() - 1)
                    <div class="w-px flex-1 mt-1 bg-gray-100"></div>
                    @endif
                </div>
                <div class="flex-1 {{ $index < $activites->count() - 1 ? 'pb-3' : '' }}">
                    <p class="text-xs font-medium text-slate-800">{{ $activity['label'] }}</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ $activity['sub'] }}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">{{ $activity['at']->diffForHumans() }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fr = v => v.toLocaleString('fr-FR');

    const loyersCtx = document.getElementById('loyersChart');
    if (loyersCtx) new Chart(loyersCtx, {
        type: 'bar',
        data: {
            labels: @json($charts['localite']['labels']),
            datasets: [{
                label: 'Loyers (k F)', data: @json($charts['localite']['data']),
                backgroundColor: 'rgba(247,127,0,0.85)', borderRadius: 6, borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + fr(c.parsed.y) + ' k F' } } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#94A3B8' } },
                y: { grid: { color: '#F1F5F9' }, ticks: { font: { size: 11 }, color: '#94A3B8', callback: v => fr(v) + ' k' } }
            }
        }
    });

    const statutsCtx = document.getElementById('statutsChart');
    if (statutsCtx) new Chart(statutsCtx, {
        type: 'doughnut',
        data: {
            labels: @json($charts['statut']['labels']),
            datasets: [{
                data: @json($charts['statut']['data']),
                backgroundColor: @json($charts['statut']['colors']),
                borderWidth: 2, borderColor: '#ffffff', hoverOffset: 4,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '70%',
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + c.label + ': ' + fr(c.parsed) } } }
        }
    });
});
</script>
@endpush
