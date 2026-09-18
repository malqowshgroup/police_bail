<?php

namespace App\Http\Controllers;

use App\Enums\StatutContrat;
use App\Enums\StatutReglement;
use App\Enums\StatutVirement;
use App\Models\ContratBail;
use App\Models\Document;
use App\Models\Localite;
use App\Models\LogementCivil;
use App\Models\Policier;
use App\Models\Reglement;
use App\Models\Virement;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    private const MOIS = [
        1 => 'Jan', 2 => 'Fév', 3 => 'Mar', 4 => 'Avr', 5 => 'Mai', 6 => 'Juin',
        7 => 'Juil', 8 => 'Août', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Déc',
    ];

    public function index(Request $request)
    {
        return view('rapports.index', [
            'kpis'    => $this->kpis(),
            'charts'  => $this->charts(),
            'tables'  => $this->tables(),
            'genereLe' => now(),
        ]);
    }

    private function kpis(): array
    {
        $mois  = now()->month;
        $annee = now()->year;

        $contratsActifs = ContratBail::actifs()->count();
        $masseLoyers    = (float) ContratBail::actifs()->sum('taux_bail');
        $logementsTotal   = LogementCivil::count();
        $logementsLibres  = LogementCivil::whereDoesntHave('contratsBail', fn($q) => $q->where('statut', StatutContrat::Actif->value))->count();
        $logementsOccupes = $logementsTotal - $logementsLibres;
        $policiersSans    = Policier::where('statut', 'actif')
            ->whereDoesntHave('contratsBail', fn($q) => $q->where('statut', 'actif'))->count();
        $bailleursActifs = ContratBail::query()
            ->where('contrats_bail.statut', StatutContrat::Actif->value)
            ->join('logements_civils', 'logements_civils.id', '=', 'contrats_bail.logement_civil_id')
            ->distinct()->count('logements_civils.proprietaire_id');

        return [
            // Ligne 1
            'contrats_actifs'   => $contratsActifs,
            'contrats_total'    => ContratBail::count(),
            'loyers_mois'       => (float) Reglement::periode($mois, $annee)
                ->where('type_reglement', 'loyer_mensuel')->sum('montant'),
            'encaisse_total'    => (float) Virement::executes()->sum('montant_total'),
            'a_payer'           => (float) Reglement::aPayer()->sum('montant'),
            'attente_virement'  => (float) Reglement::enAttenteVirement()->sum('montant'),
            'docs_en_attente'   => Document::enAttente()->count(),
            'suspendus'         => ContratBail::suspendus()->count(),
            // Ligne 2 (nouveaux indicateurs)
            'masse_loyers'      => $masseLoyers,
            'projection_annuelle' => $masseLoyers * 12,
            'loyer_moyen'       => $contratsActifs > 0 ? $masseLoyers / $contratsActifs : 0.0,
            'logements_total'         => $logementsTotal,
            'logements_occupes'       => $logementsOccupes,
            'logements_libres'        => $logementsLibres,
            'policiers_sans_logement' => $policiersSans,
            'deficit_logement'        => max(0, $policiersSans - $logementsLibres),
            'taux_occupation'         => $logementsTotal > 0 ? round($logementsOccupes / $logementsTotal * 100, 1) : 0.0,
            'bailleurs_actifs'        => $bailleursActifs,
            'docs_total'        => Document::count(),
            'docs_valides'      => Document::valides()->count(),
            'reglements_total'  => Reglement::count(),
            'taux_recouvrement' => $this->tauxRecouvrement(),
        ];
    }

    /** Part des règlements virés sur l'ensemble (hors annulés), en %. */
    private function tauxRecouvrement(): float
    {
        $base = (float) Reglement::where('statut', '!=', StatutReglement::Annule->value)->sum('montant');
        if ($base <= 0) {
            return 0.0;
        }
        $vires = (float) Reglement::vires()->sum('montant');
        return round($vires / $base * 100, 1);
    }

    private function charts(): array
    {
        return [
            'contrats_statut'  => $this->contratsParStatut(),
            'reglements_mois'  => $this->reglementsParMois(),
            'loyers_localite'  => $this->loyersParLocalite(),
            'reglements_statut' => $this->reglementsParStatut(),
            'virements_statut' => $this->virementsParStatut(),
        ];
    }

    private function tables(): array
    {
        return [
            'par_grade'          => $this->contratsParGrade(),
            'top_bailleurs'      => $this->topBailleurs(),
            'synthese'           => $this->syntheseFinanciere(),
            'logements_localite' => $this->logementsParLocalite(),
        ];
    }

    // ── Graphiques ──────────────────────────────────────────────────

    private function contratsParStatut(): array
    {
        $counts = ContratBail::selectRaw('statut, COUNT(*) as nb')
            ->groupBy('statut')->pluck('nb', 'statut');

        $labels = [];
        $data   = [];
        $colors = [];
        $palette = [
            'en_attente' => '#94A3B8', 'actif' => '#F77F00', 'suspendu' => '#F59E0B',
            'en_resiliation' => '#FB923C', 'resilie' => '#EF4444',
        ];

        foreach (StatutContrat::cases() as $case) {
            $n = (int) ($counts[$case->value] ?? 0);
            if ($n === 0) {
                continue;
            }
            $labels[] = $case->label();
            $data[]   = $n;
            $colors[] = $palette[$case->value] ?? '#64748B';
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    private function reglementsParMois(): array
    {
        $rows = Reglement::selectRaw('periode_annee, periode_mois, SUM(montant) as total')
            ->groupBy('periode_annee', 'periode_mois')
            ->orderBy('periode_annee')->orderBy('periode_mois')
            ->get()
            ->take(-12);

        return [
            'labels' => $rows->map(fn($r) => self::MOIS[$r->periode_mois].' '.$r->periode_annee)->values(),
            'data'   => $rows->map(fn($r) => round($r->total / 1000))->values(), // en milliers F
        ];
    }

    private function loyersParLocalite(): array
    {
        $rows = ContratBail::query()
            ->where('contrats_bail.statut', StatutContrat::Actif->value)
            ->join('logements_civils', 'logements_civils.id', '=', 'contrats_bail.logement_civil_id')
            ->join('localites', 'localites.id', '=', 'logements_civils.localite_id')
            ->selectRaw('localites.libelle as libelle, SUM(contrats_bail.taux_bail) as total')
            ->groupBy('localites.libelle')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return [
            'labels' => $rows->pluck('libelle')->values(),
            'data'   => $rows->map(fn($r) => round($r->total / 1000))->values(), // en milliers F
        ];
    }

    private function reglementsParStatut(): array
    {
        $rows = Reglement::selectRaw('statut, COUNT(*) as nb, SUM(montant) as total')
            ->groupBy('statut')->get()->keyBy('statut');

        $labels = [];
        $data   = [];
        $colors = [];
        $palette = [
            'a_payer' => '#F59E0B', 'en_attente_virement' => '#3B82F6',
            'vire' => '#009A44', 'annule' => '#94A3B8',
        ];

        foreach (StatutReglement::cases() as $case) {
            $labels[] = $case->label();
            $data[]   = (int) ($rows[$case->value]->nb ?? 0);
            $colors[] = $palette[$case->value] ?? '#64748B';
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    private function virementsParStatut(): array
    {
        $rows = Virement::selectRaw('statut, COUNT(*) as nb')
            ->groupBy('statut')->pluck('nb', 'statut');

        $labels = [];
        $data   = [];
        $colors = [];
        $palette = [
            'en_preparation' => '#94A3B8', 'emis' => '#3B82F6',
            'execute' => '#009A44', 'annule' => '#EF4444',
        ];

        foreach (StatutVirement::cases() as $case) {
            $labels[] = $case->label();
            $data[]   = (int) ($rows[$case->value] ?? 0);
            $colors[] = $palette[$case->value] ?? '#64748B';
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    // ── Tableaux ────────────────────────────────────────────────────

    private function contratsParGrade()
    {
        return ContratBail::query()
            ->where('contrats_bail.statut', StatutContrat::Actif->value)
            ->join('grades', 'grades.id', '=', 'contrats_bail.grade_id')
            ->selectRaw('grades.libelle as libelle, COUNT(*) as nb, SUM(contrats_bail.taux_bail) as total')
            ->groupBy('grades.libelle')
            ->orderByDesc('total')
            ->get();
    }

    private function topBailleurs()
    {
        return Virement::query()
            ->where('virements.statut', StatutVirement::Execute->value)
            ->join('proprietaires', 'proprietaires.id', '=', 'virements.proprietaire_id')
            ->selectRaw("proprietaires.id, proprietaires.type_personne, proprietaires.nom, proprietaires.prenoms, proprietaires.raison_sociale, SUM(virements.montant_total) as total, COUNT(*) as nb")
            ->groupBy('proprietaires.id', 'proprietaires.type_personne', 'proprietaires.nom', 'proprietaires.prenoms', 'proprietaires.raison_sociale')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($r) {
                $r->nom_complet = $r->type_personne === 'morale'
                    ? ($r->raison_sociale ?: $r->nom)
                    : trim($r->nom.' '.($r->prenoms ?? ''));
                return $r;
            });
    }

    private function syntheseFinanciere(): array
    {
        return [
            'a_payer'          => (float) Reglement::aPayer()->sum('montant'),
            'attente_virement' => (float) Reglement::enAttenteVirement()->sum('montant'),
            'vire'             => (float) Reglement::vires()->sum('montant'),
            'annule'           => (float) Reglement::where('statut', StatutReglement::Annule->value)->sum('montant'),
        ];
    }

    /** Offre (logements) vs demande (policiers sans logement) par localité. */
    private function logementsParLocalite()
    {
        $offre = LogementCivil::join('localites', 'localites.id', '=', 'logements_civils.localite_id')
            ->selectRaw('localites.id as localite_id, localites.libelle,
                COUNT(*) as total,
                SUM(CASE WHEN EXISTS (
                    SELECT 1 FROM contrats_bail
                    WHERE contrats_bail.logement_civil_id = logements_civils.id
                    AND contrats_bail.statut = \'actif\'
                ) THEN 1 ELSE 0 END) as occupes')
            ->groupBy('localites.id', 'localites.libelle')
            ->orderByDesc('total')
            ->get()
            ->map(function ($r) {
                $r->libres = $r->total - $r->occupes;
                $r->taux   = $r->total > 0 ? round($r->occupes / $r->total * 100) : 0;
                return $r;
            });

        return $offre;
    }
}
