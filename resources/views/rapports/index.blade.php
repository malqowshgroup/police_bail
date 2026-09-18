@extends('layouts.app')
@section('title', 'États & Statistiques')

@section('content')

@php
    $fmt = fn($v) => number_format($v, 0, ',', ' ');
    $fmtMontant = function ($v) {
        if ($v >= 1_000_000_000) return number_format($v / 1_000_000_000, 2, ',', ' ').' Md';
        if ($v >= 1_000_000)     return number_format($v / 1_000_000, 1, ',', ' ').' M';
        if ($v >= 1_000)         return number_format($v / 1_000, 0, ',', ' ').' k';
        return number_format($v, 0, ',', ' ');
    };
@endphp

{{-- Alerte déficit --}}
@if($kpis['deficit_logement'] > 0)
<div class="mb-5 rounded-2xl border-2 border-red-300 p-4 flex flex-col sm:flex-row sm:items-center gap-4"
     style="background:linear-gradient(135deg,#fff5f5 0%,#fff1ee 100%);">
    <div class="flex-shrink-0 h-11 w-11 rounded-xl bg-red-100 flex items-center justify-center">
        <i class="ti ti-home-exclamation text-2xl text-red-600"></i>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-sm font-bold text-red-800"><i class="ti ti-alert-triangle mr-1"></i> Déficit de logements détecté</p>
        <p class="text-xs text-red-700 mt-0.5">
            {{ number_format($kpis['policiers_sans_logement']) }} policiers actifs sans logement attribué pour
            <strong>{{ number_format($kpis['logements_libres']) }}</strong> logement{{ $kpis['logements_libres'] > 1 ? 's' : '' }} disponible{{ $kpis['logements_libres'] > 1 ? 's' : '' }}
            — déficit de <strong>{{ number_format($kpis['deficit_logement']) }}</strong>.
        </p>
    </div>
    <a href="{{ route('logements.index') }}"
       class="flex-shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition">
        <i class="ti ti-building text-sm"></i> Gérer le parc
    </a>
</div>
@endif

{{-- Header --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">États & Statistiques</h1>
        <p class="text-sm text-slate-500 mt-0.5">Synthèse temps réel — généré le {{ $genereLe->format('d/m/Y à H:i') }}</p>
    </div>
    <button type="button" onclick="window.print()"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-sm transition hover:opacity-90 flex-shrink-0"
            style="background:#1a2440;">
        <i class="ti ti-printer text-base"></i> Imprimer
    </button>
</div>

{{-- KPI ROW --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(247,127,0,.12);">
            <i class="ti ti-file-text text-2xl" style="color:#F77F00;"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Contrats actifs</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $fmt($kpis['contrats_actifs']) }}</p>
            <p class="text-xs mt-1 text-slate-400">sur {{ $fmt($kpis['contrats_total']) }} au total</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(0,154,68,.12);">
            <i class="ti ti-cash text-2xl" style="color:#009A44;"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Loyers du mois</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $fmtMontant($kpis['loyers_mois']) }} F</p>
            <p class="text-xs mt-1 text-slate-400">{{ ['','Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'][now()->month] }} {{ now()->year }}</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(59,130,246,.12);">
            <i class="ti ti-clock-dollar text-2xl text-blue-500"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">En attente virement</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $fmtMontant($kpis['attente_virement']) }} F</p>
            <p class="text-xs mt-1 text-slate-400">à virer aux bailleurs</p>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4 border border-gray-100">
        <div class="h-12 w-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:rgba(16,185,129,.12);">
            <i class="ti ti-circle-check text-2xl" style="color:#009A44;"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Total viré</p>
            <p class="text-2xl font-bold text-slate-800 mt-0.5">{{ $fmtMontant($kpis['encaisse_total']) }} F</p>
            <p class="text-xs mt-1 text-slate-400">virements exécutés</p>
        </div>
    </div>
</div>

{{-- KPI ROW 2 (indicateurs complémentaires) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
            <i class="ti ti-home-check text-lg" style="color:#1a2440;"></i>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Taux d'occupation</p>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ str_replace('.', ',', (string) $kpis['taux_occupation']) }} %</p>
        <p class="text-xs mt-1 text-slate-400">{{ $fmt($kpis['logements_occupes']) }} / {{ $fmt($kpis['logements_total']) }} logements</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
            <i class="ti ti-receipt text-lg" style="color:#F77F00;"></i>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Loyer moyen</p>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $fmtMontant($kpis['loyer_moyen']) }} F</p>
        <p class="text-xs mt-1 text-slate-400">par contrat actif</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
            <i class="ti ti-calendar-dollar text-lg text-indigo-500"></i>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Projection annuelle</p>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $fmtMontant($kpis['projection_annuelle']) }} F</p>
        <p class="text-xs mt-1 text-slate-400">masse loyers × 12</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm p-5 border border-gray-100">
        <div class="flex items-center gap-2 mb-2">
            <i class="ti ti-trending-up text-lg" style="color:#009A44;"></i>
            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Taux de recouvrement</p>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ str_replace('.', ',', (string) $kpis['taux_recouvrement']) }} %</p>
        <p class="text-xs mt-1 text-slate-400">règlements virés / facturés</p>
    </div>
</div>

{{-- Mini-indicateurs (bandeau) --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
    @php
        $mini = [
            ['ti-users', 'Bailleurs actifs', $fmt($kpis['bailleurs_actifs']), null],
            ['ti-file-text', 'Contrats (total)', $fmt($kpis['contrats_total']), null],
            ['ti-player-pause', 'Suspendus', $fmt($kpis['suspendus']), null],
            ['ti-cash', 'Règlements', $fmt($kpis['reglements_total']), null],
            ['ti-files', 'Documents', $fmt($kpis['docs_valides']).' / '.$fmt($kpis['docs_total']), null],
            ['ti-clock', 'Docs en attente', $fmt($kpis['docs_en_attente']), null],
        ];
    @endphp
    @foreach($mini as $m)
    <div class="bg-white rounded-xl shadow-sm p-3.5 border border-gray-100 flex items-center gap-3">
        <i class="ti {{ $m[0] }} text-lg text-slate-400 flex-shrink-0"></i>
        <div class="min-w-0">
            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider truncate">{{ $m[1] }}</p>
            <p class="text-sm font-bold text-slate-800 truncate">{{ $m[2] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- Bandeau logements offre/demande --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mb-6">
    @php
        $deficit = $kpis['deficit_logement'];
        $miniLog = [
            ['ti-building',          'Parc total',           $fmt($kpis['logements_total']),         null,       'rgba(26,36,64,.10)',   '#1a2440'],
            ['ti-home-check',        'Logements occupés',    $fmt($kpis['logements_occupes']),        null,       'rgba(0,154,68,.12)',   '#009A44'],
            ['ti-home',              'Logements libres',     $fmt($kpis['logements_libres']),         null,
                $deficit > 0 ? 'rgba(239,68,68,.12)' : 'rgba(247,127,0,.12)',
                $deficit > 0 ? '#dc2626' : '#F77F00'],
            ['ti-user-exclamation',  'Sans logement',        $fmt($kpis['policiers_sans_logement']),  null,       'rgba(59,130,246,.12)', '#3B82F6'],
            ['ti-arrows-diff',       $deficit > 0 ? 'Déficit' : 'Excédent',
                ($deficit > 0 ? '-' : '+').$fmt(abs($deficit > 0 ? $deficit : $kpis['logements_libres'] - $kpis['policiers_sans_logement'])), null,
                $deficit > 0 ? 'rgba(239,68,68,.12)' : 'rgba(0,154,68,.12)',
                $deficit > 0 ? '#dc2626' : '#009A44'],
        ];
    @endphp
    @foreach($miniLog as $m)
    <div class="bg-white rounded-xl shadow-sm p-3.5 border {{ $loop->index === 2 && $deficit > 0 ? 'border-red-200' : ($loop->index === 4 && $deficit > 0 ? 'border-red-200' : 'border-gray-100') }} flex items-center gap-3">
        <div class="h-9 w-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background:{{ $m[4] }};">
            <i class="ti {{ $m[0] }} text-base" style="color:{{ $m[5] }};"></i>
        </div>
        <div class="min-w-0">
            <p class="text-[10px] text-slate-400 font-medium uppercase tracking-wider truncate">{{ $m[1] }}</p>
            <p class="text-sm font-bold truncate" style="color:{{ $m[5] }};">{{ $m[2] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- CHARTS ROW 1 --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
    {{-- Règlements par mois (line) --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-800">Règlements facturés par mois</h2>
            <span class="text-xs text-slate-400">en milliers F CFA</span>
        </div>
        <div style="position:relative; height:240px;"><canvas id="moisChart"></canvas></div>
    </div>
    {{-- Contrats par statut (doughnut) --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Répartition des contrats</h2>
        <div class="flex flex-col items-center">
            <div style="position:relative;height:180px;" class="w-full max-w-[220px]"><canvas id="contratsChart"></canvas></div>
            <div class="mt-4 space-y-2 w-full">
                @foreach($charts['contrats_statut']['labels'] as $i => $label)
                <div class="flex items-center justify-between text-xs">
                    <span class="flex items-center gap-1.5"><span class="inline-block h-2.5 w-2.5 rounded-sm" style="background:{{ $charts['contrats_statut']['colors'][$i] }};"></span>{{ $label }}</span>
                    <span class="font-semibold text-slate-700">{{ $fmt($charts['contrats_statut']['data'][$i]) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- CHARTS ROW 2 --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
    {{-- Loyers par localité (bar) --}}
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-800">Loyers actifs par localité</h2>
            <span class="text-xs text-slate-400">milliers F / mois</span>
        </div>
        <div style="position:relative; height:240px;"><canvas id="localiteChart"></canvas></div>
    </div>
    {{-- Synthèse financière --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Synthèse financière (règlements)</h2>
        @php
            $synth = $tables['synthese'];
            $totalSynth = max(1, array_sum($synth));
            $synthRows = [
                ['À payer', $synth['a_payer'], '#F59E0B'],
                ['En attente virement', $synth['attente_virement'], '#3B82F6'],
                ['Viré', $synth['vire'], '#009A44'],
                ['Annulé', $synth['annule'], '#94A3B8'],
            ];
        @endphp
        <div class="space-y-4">
            @foreach($synthRows as $row)
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-600">{{ $row[0] }}</span>
                    <span class="font-semibold text-slate-700">{{ $fmt($row[1]) }} F</span>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" style="width:{{ round($row[1] / $totalSynth * 100) }}%; background:{{ $row[2] }};"></div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-5 pt-4 border-t border-gray-100 flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total facturé</span>
            <span class="text-lg font-bold" style="color:#F77F00;">{{ $fmt(array_sum($synth)) }} F</span>
        </div>
    </div>
</div>

{{-- CHARTS ROW 3 + TABLES --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
    {{-- Règlements par statut (bar) --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Règlements par statut</h2>
        <div style="position:relative; height:200px;"><canvas id="reglStatutChart"></canvas></div>
    </div>
    {{-- Virements par statut (bar) --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Virements par statut</h2>
        <div style="position:relative; height:200px;"><canvas id="virStatutChart"></canvas></div>
    </div>
    {{-- Contrats par grade --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Contrats actifs par grade</h2>
        @if($tables['par_grade']->isEmpty())
        <p class="text-sm text-slate-400 py-6 text-center">Aucun contrat actif.</p>
        @else
        @php $maxGrade = max(1, $tables['par_grade']->max('total')); @endphp
        <div class="space-y-3 max-h-[200px] overflow-y-auto">
            @foreach($tables['par_grade'] as $g)
            <div>
                <div class="flex justify-between text-xs mb-1">
                    <span class="text-slate-600 truncate pr-2">{{ $g->libelle }} <span class="text-slate-400">({{ $g->nb }})</span></span>
                    <span class="font-semibold text-slate-700 flex-shrink-0">{{ $fmtMontant($g->total) }} F</span>
                </div>
                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" style="width:{{ round($g->total / $maxGrade * 100) }}%; background:#F77F00;"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- PARC IMMOBILIER PAR LOCALITÉ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-4 mb-4">
    <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between" style="background:#f8f9fb;">
        <h2 class="text-sm font-semibold text-slate-800">
            <i class="ti ti-map-pin mr-1.5 text-slate-400"></i> Parc immobilier par localité
        </h2>
        <span class="text-xs text-slate-400">offre vs taux d'occupation</span>
    </div>
    @if($tables['logements_localite']->isEmpty())
    <p class="text-sm text-slate-400 py-10 text-center">Aucun logement enregistré.</p>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                    <th class="px-5 py-3">Localité</th>
                    <th class="px-5 py-3 text-center">Total</th>
                    <th class="px-5 py-3 text-center">Occupés</th>
                    <th class="px-5 py-3 text-center">Libres</th>
                    <th class="px-5 py-3 w-48">Taux occupation</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($tables['logements_localite'] as $loc)
                <tr class="hover:bg-slate-50/70">
                    <td class="px-5 py-3">
                        <span class="flex items-center gap-1.5 font-medium text-slate-700">
                            <i class="ti ti-map-pin text-slate-300 text-xs"></i>
                            {{ $loc->libelle }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center font-semibold text-slate-700">{{ $loc->total }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                            {{ $loc->occupes }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-center">
                        @if($loc->libres > 0)
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-orange-700 bg-orange-50 px-2 py-0.5 rounded-full">
                            {{ $loc->libres }}
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full">0</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full"
                                     style="width:{{ $loc->taux }}%;
                                            background:{{ $loc->taux >= 90 ? '#009A44' : ($loc->taux >= 70 ? '#F77F00' : '#94A3B8') }};"></div>
                            </div>
                            <span class="text-xs font-semibold text-slate-600 w-9 text-right">{{ $loc->taux }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- TOP BAILLEURS --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-4">
    <div class="px-5 py-3.5 border-b border-gray-100" style="background:#f8f9fb;">
        <h2 class="text-sm font-semibold text-slate-800">Top 10 bailleurs (montants virés)</h2>
    </div>
    @if($tables['top_bailleurs']->isEmpty())
    <p class="text-sm text-slate-400 py-10 text-center">Aucun virement exécuté pour l'instant.</p>
    @else
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-gray-100">
                    <th class="px-5 py-3 w-10">#</th>
                    <th class="px-5 py-3">Bailleur</th>
                    <th class="px-5 py-3 text-center">Virements</th>
                    <th class="px-5 py-3 text-right">Montant total viré</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($tables['top_bailleurs'] as $i => $b)
                <tr class="hover:bg-slate-50/70">
                    <td class="px-5 py-3 text-slate-400 font-semibold">{{ $i + 1 }}</td>
                    <td class="px-5 py-3">
                        <a href="{{ route('proprietaires.show', $b->id) }}" class="font-medium text-slate-800 hover:text-blue-600 hover:underline">{{ $b->nom_complet }}</a>
                    </td>
                    <td class="px-5 py-3 text-center text-slate-600">{{ $b->nb }}</td>
                    <td class="px-5 py-3 text-right font-semibold" style="color:#009A44;">{{ $fmt($b->total) }} F</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fr = v => v.toLocaleString('fr-FR');
    const baseAxes = (suffix = '') => ({
        x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#94A3B8' } },
        y: { grid: { color: '#F1F5F9' }, ticks: { font: { size: 10 }, color: '#94A3B8', callback: v => fr(v) + suffix } },
    });

    // Règlements par mois (line)
    const mois = document.getElementById('moisChart');
    if (mois) new Chart(mois, {
        type: 'line',
        data: {
            labels: @json($charts['reglements_mois']['labels']),
            datasets: [{
                label: 'Règlements (k F)',
                data: @json($charts['reglements_mois']['data']),
                borderColor: '#F77F00', backgroundColor: 'rgba(247,127,0,.12)',
                fill: true, tension: .35, borderWidth: 2, pointRadius: 3, pointBackgroundColor: '#F77F00',
            }]
        },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + fr(c.parsed.y) + ' k F' } } },
            scales: baseAxes(' k') }
    });

    // Contrats par statut (doughnut)
    const contrats = document.getElementById('contratsChart');
    if (contrats) new Chart(contrats, {
        type: 'doughnut',
        data: { labels: @json($charts['contrats_statut']['labels']),
            datasets: [{ data: @json($charts['contrats_statut']['data']),
                backgroundColor: @json($charts['contrats_statut']['colors']),
                borderWidth: 2, borderColor: '#fff', hoverOffset: 4 }] },
        options: { responsive: true, maintainAspectRatio: false, cutout: '68%',
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + c.label + ': ' + fr(c.parsed) } } } }
    });

    // Loyers par localité (bar)
    const loc = document.getElementById('localiteChart');
    if (loc) new Chart(loc, {
        type: 'bar',
        data: { labels: @json($charts['loyers_localite']['labels']),
            datasets: [{ data: @json($charts['loyers_localite']['data']),
                backgroundColor: 'rgba(26,36,64,.85)', borderRadius: 6, borderSkipped: false }] },
        options: { responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: c => ' ' + fr(c.parsed.y) + ' k F' } } },
            scales: baseAxes(' k') }
    });

    // Règlements par statut (bar)
    const rs = document.getElementById('reglStatutChart');
    if (rs) new Chart(rs, {
        type: 'bar',
        data: { labels: @json($charts['reglements_statut']['labels']),
            datasets: [{ data: @json($charts['reglements_statut']['data']),
                backgroundColor: @json($charts['reglements_statut']['colors']), borderRadius: 6, borderSkipped: false }] },
        options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { grid: { color: '#F1F5F9' }, ticks: { font: { size: 10 }, color: '#94A3B8' } }, y: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#64748B' } } } }
    });

    // Virements par statut (bar)
    const vs = document.getElementById('virStatutChart');
    if (vs) new Chart(vs, {
        type: 'bar',
        data: { labels: @json($charts['virements_statut']['labels']),
            datasets: [{ data: @json($charts['virements_statut']['data']),
                backgroundColor: @json($charts['virements_statut']['colors']), borderRadius: 6, borderSkipped: false }] },
        options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y',
            plugins: { legend: { display: false } },
            scales: { x: { grid: { color: '#F1F5F9' }, ticks: { font: { size: 10 }, color: '#94A3B8', precision: 0 } }, y: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#64748B' } } } }
    });
});
</script>
@endpush
