<?php

namespace App\Http\Controllers;

use App\Enums\StatutContrat;
use App\Models\Bordereau;
use App\Models\ContratBail;
use App\Models\Document;
use App\Models\Grade;
use App\Models\LogementCivil;
use App\Models\Policier;
use App\Models\Reglement;
use App\Models\Virement;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'kpis'       => $this->kpis(),
            'charts'     => $this->charts(),
            'grades'     => $this->tauxParGrade(),
            'alertes'    => $this->alertes(),
            'activites'  => $this->activiteRecente(),
        ]);
    }

    private function kpis(): array
    {
        $debutMois = now()->startOfMonth();
        $loyersMois  = (float) Reglement::periode(now()->month, now()->year)
            ->where('type_reglement', 'loyer_mensuel')->sum('montant');
        $moisPrec    = now()->copy()->subMonthNoOverflow();
        $loyersPrec  = (float) Reglement::periode($moisPrec->month, $moisPrec->year)
            ->where('type_reglement', 'loyer_mensuel')->sum('montant');

        $logementsTotal  = LogementCivil::count();
        $logementsLibres = LogementCivil::whereDoesntHave('contratsBail', fn($q) => $q->where('statut', 'actif'))->count();
        $policiersSans   = Policier::where('statut', 'actif')
            ->whereDoesntHave('contratsBail', fn($q) => $q->where('statut', 'actif'))->count();

        return [
            'contrats_actifs'         => ContratBail::actifs()->count(),
            'contrats_nouveaux'       => ContratBail::where('created_at', '>=', $debutMois)->count(),
            'loyers_mois'             => $loyersMois,
            'loyers_evolution'        => $loyersPrec > 0 ? round(($loyersMois - $loyersPrec) / $loyersPrec * 100, 1) : null,
            'a_payer'                 => Reglement::aPayer()->count(),
            'a_payer_montant'         => (float) Reglement::aPayer()->sum('montant'),
            'suspendus'               => ContratBail::suspendus()->count(),
            'logements_total'         => $logementsTotal,
            'logements_libres'        => $logementsLibres,
            'logements_occupes'       => $logementsTotal - $logementsLibres,
            'policiers_sans_logement' => $policiersSans,
            'deficit_logement'        => max(0, $policiersSans - $logementsLibres),
            'taux_occupation'         => $logementsTotal > 0 ? round(($logementsTotal - $logementsLibres) / $logementsTotal * 100) : 0,
        ];
    }

    private function charts(): array
    {
        // Loyers actifs par localité
        $loc = ContratBail::query()
            ->where('contrats_bail.statut', StatutContrat::Actif->value)
            ->join('logements_civils', 'logements_civils.id', '=', 'contrats_bail.logement_civil_id')
            ->join('localites', 'localites.id', '=', 'logements_civils.localite_id')
            ->selectRaw('localites.libelle as libelle, SUM(contrats_bail.taux_bail) as total')
            ->groupBy('localites.libelle')->orderByDesc('total')->limit(8)->get();

        // Contrats par statut
        $counts = ContratBail::selectRaw('statut, COUNT(*) as nb')->groupBy('statut')->pluck('nb', 'statut');
        $palette = [
            'en_attente' => '#94A3B8', 'actif' => '#F77F00', 'suspendu' => '#F59E0B',
            'en_resiliation' => '#FB923C', 'resilie' => '#009A44',
        ];
        $sLabels = $sData = $sColors = [];
        foreach (StatutContrat::cases() as $case) {
            $n = (int) ($counts[$case->value] ?? 0);
            if ($n === 0) continue;
            $sLabels[] = $case->label();
            $sData[]   = $n;
            $sColors[] = $palette[$case->value] ?? '#64748B';
        }

        return [
            'localite' => [
                'labels' => $loc->pluck('libelle')->values(),
                'data'   => $loc->map(fn($r) => round($r->total / 1000))->values(),
            ],
            'statut' => ['labels' => $sLabels, 'data' => $sData, 'colors' => $sColors],
        ];
    }

    private function tauxParGrade()
    {
        return Grade::orderByDesc('taux_bail')->take(8)->get();
    }

    private function alertes(): array
    {
        $logementsLibres = LogementCivil::whereDoesntHave('contratsBail', fn($q) => $q->where('statut', 'actif'))->count();
        $policiersSans   = Policier::where('statut', 'actif')
            ->whereDoesntHave('contratsBail', fn($q) => $q->where('statut', 'actif'))->count();

        return [
            'docs_attente'            => Document::enAttente()->count(),
            'contrats_attente'        => ContratBail::enAttente()->count(),
            'reglements_payer'        => Reglement::aPayer()->count(),
            'virements_cours'         => Virement::enCours()->count(),
            'bordereaux_cours'        => Bordereau::enCours()->count(),
            'docs_valides_mois'       => Document::valides()
                ->where('date_validation', '>=', now()->startOfMonth())->count(),
            'logements_libres'        => $logementsLibres,
            'policiers_sans_logement' => $policiersSans,
            'deficit_logement'        => max(0, $policiersSans - $logementsLibres),
        ];
    }

    /** Flux d'activité réel : derniers enregistrements de plusieurs modules, fusionnés. */
    private function activiteRecente()
    {
        $items = collect();

        ContratBail::with('policier')->latest()->take(5)->get()->each(function ($c) use ($items) {
            $items->push([
                'color' => '#009A44',
                'label' => 'Contrat '.$c->numero_contrat,
                'sub'   => trim(($c->policier?->nom ?? '').' '.($c->policier?->prenoms ?? '')) ?: 'Nouveau contrat',
                'at'    => $c->created_at,
            ]);
        });

        Reglement::with('contratBail.policier')->latest()->take(5)->get()->each(function ($r) use ($items) {
            $items->push([
                'color' => '#F77F00',
                'label' => 'Règlement '.number_format($r->montant, 0, ',', ' ').' F',
                'sub'   => $r->contratBail?->policier?->nom.' — '.$r->periode_label,
                'at'    => $r->created_at,
            ]);
        });

        Document::latest()->take(5)->get()->each(function ($d) use ($items) {
            $items->push([
                'color' => '#3B82F6',
                'label' => 'Document déposé',
                'sub'   => \Illuminate\Support\Str::limit($d->nom_original, 32),
                'at'    => $d->created_at,
            ]);
        });

        Virement::with('proprietaire')->latest()->take(5)->get()->each(function ($v) use ($items) {
            $items->push([
                'color' => '#6366F1',
                'label' => 'Virement '.$v->numero,
                'sub'   => $v->proprietaire?->nom_complet.' — '.number_format($v->montant_total, 0, ',', ' ').' F',
                'at'    => $v->created_at,
            ]);
        });

        return $items
            ->filter(fn($i) => $i['at'] instanceof Carbon)
            ->sortByDesc('at')
            ->take(7)
            ->values();
    }
}
