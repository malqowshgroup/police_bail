<?php

namespace App\Http\Controllers;

use App\Enums\StatutContrat;
use App\Enums\StatutReglement;
use App\Enums\TypeReglement;
use App\Http\Requests\StoreReglementRequest;
use App\Http\Requests\UpdateReglementRequest;
use App\Models\ContratBail;
use App\Models\Reglement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReglementController extends Controller
{
    public function index(Request $request)
    {
        $query = Reglement::with(['contratBail.policier.grade', 'contratBail.logementCivil'])
            ->when($request->search, fn($q, $s) => $q->whereHas('contratBail', fn($c) => $c
                ->where('numero_contrat', 'like', "%$s%")
                ->orWhereHas('policier', fn($p) => $p
                    ->where('nom', 'like', "%$s%")
                    ->orWhere('prenoms', 'like', "%$s%")
                    ->orWhere('matricule', 'like', "%$s%"))))
            ->when($request->statut, fn($q, $st) => $q->where('statut', $st))
            ->when($request->type, fn($q, $t) => $q->where('type_reglement', $t))
            ->when($request->mois, fn($q, $m) => $q->where('periode_mois', $m))
            ->when($request->annee, fn($q, $a) => $q->where('periode_annee', $a))
            ->orderByDesc('periode_annee')
            ->orderByDesc('periode_mois')
            ->orderByDesc('id');

        $reglements = $query->paginate(20)->withQueryString();

        $stats = [
            'total'             => Reglement::count(),
            'montant_a_payer'   => (float) Reglement::aPayer()->sum('montant'),
            'attente_virement'  => Reglement::enAttenteVirement()->count(),
            'montant_vire'      => (float) Reglement::vires()->sum('montant'),
        ];

        $statuts = StatutReglement::options();
        $types   = TypeReglement::options();
        $annees  = Reglement::query()->distinct()->orderByDesc('periode_annee')->pluck('periode_annee');

        return view('reglements.index', [
            'reglements'  => $reglements,
            'stats'       => $stats,
            'statuts'     => $statuts,
            'types'       => $types,
            'annees'      => $annees,
            'moisCourant' => now()->month,
            'anneeCourante' => now()->year,
        ]);
    }

    public function create()
    {
        return view('reglements.create', [
            'contrats' => $this->contratsActifs(),
            'types'    => TypeReglement::options(),
            'mois'     => now()->month,
            'annee'    => now()->year,
        ]);
    }

    public function store(StoreReglementRequest $request)
    {
        $data = $request->validated();
        $data['statut']     = StatutReglement::APayer->value;
        $data['genere_par'] = Auth::id();

        $reglement = Reglement::create($data);

        return redirect()->route('reglements.show', $reglement)
            ->with('success', 'Règlement enregistré.');
    }

    public function show(Reglement $reglement)
    {
        $reglement->load(['contratBail.policier.grade', 'contratBail.logementCivil.proprietaire', 'generePar']);

        return view('reglements.show', compact('reglement'));
    }

    public function edit(Reglement $reglement)
    {
        if (! $reglement->peutEtreModifie()) {
            return redirect()->route('reglements.show', $reglement)
                ->with('error', 'Seul un règlement « à payer » est modifiable.');
        }

        $reglement->load('contratBail.policier');

        return view('reglements.edit', [
            'reglement' => $reglement,
            'types'     => TypeReglement::options(),
        ]);
    }

    public function update(UpdateReglementRequest $request, Reglement $reglement)
    {
        if (! $reglement->peutEtreModifie()) {
            return redirect()->route('reglements.show', $reglement)
                ->with('error', 'Seul un règlement « à payer » est modifiable.');
        }

        $reglement->update($request->validated());

        return redirect()->route('reglements.show', $reglement)
            ->with('success', 'Règlement mis à jour.');
    }

    public function destroy(Reglement $reglement)
    {
        if ($reglement->isVire()) {
            return redirect()->route('reglements.index')
                ->with('error', 'Un règlement viré ne peut pas être supprimé.');
        }

        $reglement->delete();

        return redirect()->route('reglements.index')
            ->with('success', 'Règlement supprimé.');
    }

    // ── Actions ─────────────────────────────────────────────────────

    /**
     * Génère les loyers mensuels pour une période donnée, sur tous les
     * contrats actifs n'ayant pas encore de loyer pour cette période.
     */
    public function genererMensuel(Request $request)
    {
        $data = $request->validate([
            'periode_mois'  => ['required', 'integer', 'min:1', 'max:12'],
            'periode_annee' => ['required', 'integer', 'min:2020', 'max:2100'],
        ]);

        $mois  = (int) $data['periode_mois'];
        $annee = (int) $data['periode_annee'];

        $finPeriode = \Carbon\Carbon::create($annee, $mois)->endOfMonth();

        // Contrats actifs déjà démarrés à la fin de la période.
        $contrats = ContratBail::where('statut', StatutContrat::Actif->value)
            ->whereDate('date_debut', '<=', $finPeriode)
            ->get();

        // Contrats déjà couverts pour cette période (loyer non annulé).
        $dejaCouverts = Reglement::periode($mois, $annee)
            ->where('type_reglement', TypeReglement::LoyerMensuel->value)
            ->where('statut', '!=', StatutReglement::Annule->value)
            ->pluck('contrat_bail_id')
            ->flip();

        $cree = 0;

        DB::transaction(function () use ($contrats, $dejaCouverts, $mois, $annee, &$cree) {
            foreach ($contrats as $contrat) {
                if ($dejaCouverts->has($contrat->id)) {
                    continue;
                }

                Reglement::create([
                    'contrat_bail_id' => $contrat->id,
                    'periode_mois'    => $mois,
                    'periode_annee'   => $annee,
                    'type_reglement'  => TypeReglement::LoyerMensuel->value,
                    'montant'         => $contrat->taux_bail,
                    'statut'          => StatutReglement::APayer->value,
                    'genere_par'      => Auth::id(),
                ]);
                $cree++;
            }
        });

        $message = $cree > 0
            ? "$cree loyer(s) généré(s) pour la période sélectionnée."
            : 'Aucun nouveau loyer à générer : tous les contrats actifs sont déjà couverts pour cette période.';

        return redirect()->route('reglements.index', ['mois' => $mois, 'annee' => $annee])
            ->with($cree > 0 ? 'success' : 'error', $message);
    }

    public function mettreEnAttente(Reglement $reglement)
    {
        if (! $reglement->peutPasserEnAttenteVirement()) {
            return back()->with('error', 'Ce règlement ne peut pas passer en attente de virement.');
        }

        $reglement->update(['statut' => StatutReglement::EnAttenteVirement->value]);

        return back()->with('success', 'Règlement placé en attente de virement.');
    }

    public function annuler(Reglement $reglement)
    {
        if (! $reglement->peutEtreAnnule()) {
            return back()->with('error', 'Ce règlement ne peut pas être annulé.');
        }

        $reglement->update(['statut' => StatutReglement::Annule->value]);

        return back()->with('success', 'Règlement annulé.');
    }

    // ── Helpers ─────────────────────────────────────────────────────

    private function contratsActifs()
    {
        return ContratBail::with(['policier.grade', 'logementCivil'])
            ->where('statut', StatutContrat::Actif->value)
            ->orderBy('numero_contrat')
            ->get();
    }
}
