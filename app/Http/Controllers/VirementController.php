<?php

namespace App\Http\Controllers;

use App\Enums\StatutReglement;
use App\Enums\StatutVirement;
use App\Http\Requests\StoreVirementRequest;
use App\Http\Requests\UpdateVirementRequest;
use App\Models\Proprietaire;
use App\Models\Reglement;
use App\Models\Virement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VirementController extends Controller
{
    public function index(Request $request)
    {
        $query = Virement::with('proprietaire')
            ->withCount('reglements')
            ->when($request->search, fn($q, $s) => $q->where('numero', 'like', "%$s%")
                ->orWhereHas('proprietaire', fn($p) => $p
                    ->where('nom', 'like', "%$s%")
                    ->orWhere('raison_sociale', 'like', "%$s%")))
            ->when($request->statut, fn($q, $st) => $q->where('statut', $st))
            ->orderByDesc('created_at');

        $virements = $query->paginate(20)->withQueryString();

        $stats = [
            'total'        => Virement::count(),
            'en_cours'     => Virement::enCours()->count(),
            'montant_execute' => (float) Virement::executes()->sum('montant_total'),
            'reglements_attente' => Reglement::enAttenteVirement()->whereNull('virement_id')->count(),
        ];

        $statuts = StatutVirement::options();

        return view('virements.index', compact('virements', 'stats', 'statuts'));
    }

    public function create(Request $request)
    {
        $proprietaireId = $request->integer('proprietaire_id') ?: null;
        $proprietaire   = $proprietaireId ? Proprietaire::find($proprietaireId) : null;

        return view('virements.create', [
            'proprietaires' => $this->proprietairesAvecReglements(),
            'proprietaire'  => $proprietaire,
            'reglements'    => $proprietaire ? $this->reglementsDisponibles($proprietaire->id) : collect(),
            'numero'        => Virement::genererNumero(),
        ]);
    }

    public function store(StoreVirementRequest $request)
    {
        $data = $request->validated();

        $virement = DB::transaction(function () use ($data) {
            $virement = Virement::create([
                'numero'          => Virement::genererNumero(),
                'proprietaire_id' => $data['proprietaire_id'],
                'banque'          => $data['banque'] ?? null,
                'date_virement'   => $data['date_virement'] ?? null,
                'observations'    => $data['observations'] ?? null,
                'statut'          => StatutVirement::EnPreparation->value,
                'montant_total'   => 0,
            ]);

            Reglement::whereIn('id', $data['reglements'])
                ->where('statut', StatutReglement::EnAttenteVirement->value)
                ->whereNull('virement_id')
                ->update(['virement_id' => $virement->id]);

            $virement->recalculerMontant();

            return $virement;
        });

        return redirect()->route('virements.show', $virement)
            ->with('success', "Virement {$virement->numero} créé.");
    }

    public function show(Virement $virement)
    {
        $virement->load([
            'proprietaire.localite',
            'reglements.contratBail.policier',
            'reglements.contratBail.logementCivil',
            'emisPar', 'executePar',
        ]);

        return view('virements.show', compact('virement'));
    }

    public function edit(Virement $virement)
    {
        if (! $virement->contenuModifiable()) {
            return redirect()->route('virements.show', $virement)
                ->with('error', 'Ce virement n\'est plus modifiable (il a été émis).');
        }

        $virement->load('proprietaire', 'reglements');

        return view('virements.edit', [
            'virement'   => $virement,
            'reglements' => $this->reglementsDisponibles($virement->proprietaire_id, $virement),
        ]);
    }

    public function update(UpdateVirementRequest $request, Virement $virement)
    {
        if (! $virement->contenuModifiable()) {
            return redirect()->route('virements.show', $virement)
                ->with('error', 'Ce virement n\'est plus modifiable.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($virement, $data) {
            $virement->update([
                'banque'        => $data['banque'] ?? null,
                'date_virement' => $data['date_virement'] ?? null,
                'observations'  => $data['observations'] ?? null,
            ]);

            // Détache les règlements retirés.
            $virement->reglements()
                ->whereNotIn('id', $data['reglements'])
                ->update(['virement_id' => null]);

            // Rattache les nouveaux (en attente et libres).
            Reglement::whereIn('id', $data['reglements'])
                ->where('statut', StatutReglement::EnAttenteVirement->value)
                ->whereNull('virement_id')
                ->update(['virement_id' => $virement->id]);

            $virement->recalculerMontant();
        });

        return redirect()->route('virements.show', $virement)
            ->with('success', 'Virement mis à jour.');
    }

    public function destroy(Virement $virement)
    {
        if (! $virement->isEnPreparation()) {
            return redirect()->route('virements.index')
                ->with('error', 'Seul un virement en préparation peut être supprimé.');
        }

        $numero = $virement->numero;

        DB::transaction(function () use ($virement) {
            $virement->reglements()->update(['virement_id' => null]);
            $virement->delete();
        });

        return redirect()->route('virements.index')
            ->with('success', "Virement {$numero} supprimé. Ses règlements ont été libérés.");
    }

    // ── Cycle de vie ────────────────────────────────────────────────

    public function emettre(Virement $virement)
    {
        if (! $virement->peutEtreEmis()) {
            return back()->with('error', 'Le virement doit être en préparation et contenir au moins un règlement.');
        }

        $virement->update([
            'statut'            => StatutVirement::Emis->value,
            'reference_fichier' => 'FIC-'.$virement->numero.'.txt',
            'emis_par'          => Auth::id(),
            'date_emission'     => now(),
        ]);

        return back()->with('success', "Virement {$virement->numero} émis. Fichier : {$virement->reference_fichier}.");
    }

    public function executer(Virement $virement)
    {
        if (! $virement->peutEtreExecute()) {
            return back()->with('error', 'Seul un virement émis peut être marqué comme exécuté.');
        }

        DB::transaction(function () use ($virement) {
            $virement->update([
                'statut'         => StatutVirement::Execute->value,
                'execute_par'    => Auth::id(),
                'date_execution' => now(),
                'date_virement'  => $virement->date_virement ?? now(),
            ]);

            // Marque les règlements comme virés + dénormalise les infos de virement.
            $virement->reglements()->update([
                'statut'                     => StatutReglement::Vire->value,
                'numero_virement'            => $virement->numero,
                'date_virement'              => $virement->date_virement ?? now(),
                'banque_bailleur'            => $virement->banque,
                'reference_fichier_virement' => $virement->reference_fichier,
            ]);
        });

        return back()->with('success', "Virement {$virement->numero} exécuté. Les règlements sont désormais virés.");
    }

    public function annuler(Virement $virement)
    {
        if (! $virement->peutEtreAnnule()) {
            return back()->with('error', 'Ce virement ne peut pas être annulé.');
        }

        DB::transaction(function () use ($virement) {
            // Libère les règlements (ils repassent disponibles, statut inchangé : en attente).
            $virement->reglements()->update(['virement_id' => null]);

            $virement->update([
                'statut'        => StatutVirement::Annule->value,
                'montant_total' => 0,
            ]);
        });

        return back()->with('success', "Virement {$virement->numero} annulé. Ses règlements ont été libérés.");
    }

    // ── Helpers ─────────────────────────────────────────────────────

    /**
     * Propriétaires ayant des règlements « en attente de virement » non rattachés,
     * avec le nombre et le montant cumulé (exposés en `reglements_count` / `reglements_total`).
     */
    private function proprietairesAvecReglements()
    {
        $agg = Reglement::query()
            ->where('reglements.statut', StatutReglement::EnAttenteVirement->value)
            ->whereNull('reglements.virement_id')
            ->join('contrats_bail', 'contrats_bail.id', '=', 'reglements.contrat_bail_id')
            ->join('logements_civils', 'logements_civils.id', '=', 'contrats_bail.logement_civil_id')
            ->selectRaw('logements_civils.proprietaire_id as pid, COUNT(*) as nb, SUM(reglements.montant) as total')
            ->groupBy('logements_civils.proprietaire_id')
            ->get()
            ->keyBy('pid');

        return Proprietaire::whereIn('id', $agg->keys())
            ->orderBy('nom')
            ->get()
            ->map(function ($p) use ($agg) {
                $p->reglements_count = (int) ($agg[$p->id]->nb ?? 0);
                $p->reglements_total = (float) ($agg[$p->id]->total ?? 0);
                return $p;
            });
    }

    /**
     * Règlements « en attente de virement » d'un propriétaire (ceux du virement édité restent inclus).
     */
    private function reglementsDisponibles(int $proprietaireId, ?Virement $virement = null)
    {
        return Reglement::with(['contratBail.policier', 'contratBail.logementCivil'])
            ->where('statut', StatutReglement::EnAttenteVirement->value)
            ->where(function ($q) use ($virement) {
                $q->whereNull('virement_id');
                if ($virement) {
                    $q->orWhere('virement_id', $virement->id);
                }
            })
            ->whereHas('contratBail.logementCivil', fn($q) => $q->where('proprietaire_id', $proprietaireId))
            ->orderByDesc('periode_annee')
            ->orderByDesc('periode_mois')
            ->get();
    }
}
