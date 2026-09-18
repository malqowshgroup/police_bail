<?php

namespace App\Http\Controllers;

use App\Enums\StatutContrat;
use App\Http\Requests\StoreContratBailRequest;
use App\Http\Requests\UpdateContratBailRequest;
use App\Models\ContratBail;
use App\Models\LogementCivil;
use App\Models\Policier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratBailController extends Controller
{
    /** Statuts considérés comme « en cours » (bloquent un nouveau contrat). */
    private const STATUTS_EN_COURS = [
        'en_attente', 'actif', 'suspendu', 'en_resiliation',
    ];

    public function index(Request $request)
    {
        $query = ContratBail::with(['policier.grade', 'logementCivil.localite'])
            ->when($request->search, fn($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('numero_contrat', 'like', "%$s%")
                  ->orWhereHas('policier', fn($p) => $p
                      ->where('nom', 'like', "%$s%")
                      ->orWhere('prenoms', 'like', "%$s%")
                      ->orWhere('matricule', 'like', "%$s%"))
                  ->orWhereHas('logementCivil', fn($l) => $l->where('reference', 'like', "%$s%"));
            }))
            ->when($request->statut, fn($q, $st) => $q->where('statut', $st))
            ->orderByDesc('created_at');

        $contrats = $query->paginate(20)->withQueryString();

        $stats = [
            'total'     => ContratBail::count(),
            'actifs'    => ContratBail::actifs()->count(),
            'attente'   => ContratBail::enAttente()->count(),
            'suspendus' => ContratBail::suspendus()->count(),
        ];

        $statuts = StatutContrat::options();

        return view('contrats.index', compact('contrats', 'stats', 'statuts'));
    }

    public function create()
    {
        return view('contrats.create', [
            'policiers' => $this->policiersDisponibles(),
            'logements' => $this->logementsDisponibles(),
        ]);
    }

    public function store(StoreContratBailRequest $request)
    {
        $data = $request->validated();

        $policier = Policier::with('grade')->findOrFail($data['policier_id']);

        $data['numero_contrat'] = ContratBail::genererNumero();
        $data['grade_id']       = $policier->grade_id;
        $data['taux_bail']      = $policier->grade?->taux_bail ?? 0;
        $data['statut']         = StatutContrat::EnAttente->value;
        $data['saisi_par']      = Auth::id();

        if (! ($data['avec_arrieres'] ?? false)) {
            $data['nb_mois_arrieres']    = 0;
            $data['date_debut_arrieres'] = null;
        }

        $contrat = ContratBail::create($data);

        return redirect()->route('contrats.show', $contrat)
            ->with('success', "Contrat {$contrat->numero_contrat} créé. Il est en attente de validation.");
    }

    public function show(ContratBail $contrat)
    {
        $contrat->load([
            'policier.grade', 'policier.service',
            'logementCivil.localite', 'logementCivil.proprietaire',
            'grade', 'saisiPar', 'validePar', 'bordereau',
        ]);

        return view('contrats.show', compact('contrat'));
    }

    public function edit(ContratBail $contrat)
    {
        return view('contrats.edit', [
            'contrat'   => $contrat,
            'policiers' => $this->policiersDisponibles($contrat),
            'logements' => $this->logementsDisponibles($contrat),
        ]);
    }

    public function update(UpdateContratBailRequest $request, ContratBail $contrat)
    {
        $data = $request->validated();

        // Resynchronise le grade et le taux si le policier change.
        if ((int) $data['policier_id'] !== (int) $contrat->policier_id) {
            $policier = Policier::with('grade')->findOrFail($data['policier_id']);
            $data['grade_id']  = $policier->grade_id;
            $data['taux_bail'] = $policier->grade?->taux_bail ?? 0;
        }

        if (! ($data['avec_arrieres'] ?? false)) {
            $data['nb_mois_arrieres']    = 0;
            $data['date_debut_arrieres'] = null;
        }

        $contrat->update($data);

        return redirect()->route('contrats.show', $contrat)
            ->with('success', 'Contrat mis à jour avec succès.');
    }

    public function destroy(ContratBail $contrat)
    {
        if ($contrat->statut !== StatutContrat::EnAttente->value) {
            return redirect()->route('contrats.index')
                ->with('error', 'Seul un contrat en attente peut être supprimé. Résiliez-le pour clore un contrat actif.');
        }

        $numero = $contrat->numero_contrat;
        $contrat->delete();

        return redirect()->route('contrats.index')
            ->with('success', "Contrat {$numero} supprimé.");
    }

    // ── Cycle de vie ────────────────────────────────────────────────

    public function activer(ContratBail $contrat)
    {
        if (! $contrat->peutEtreActive()) {
            return back()->with('error', 'Ce contrat ne peut pas être activé dans son état actuel.');
        }

        $contrat->update([
            'statut'                => StatutContrat::Actif->value,
            'motif_suspension'      => null,
            'date_levee_suspension' => $contrat->isSuspendu() ? now() : $contrat->date_levee_suspension,
            'valide_par'            => $contrat->valide_par ?? Auth::id(),
            'date_validation'       => $contrat->date_validation ?? now(),
        ]);

        return back()->with('success', "Contrat {$contrat->numero_contrat} activé.");
    }

    public function suspendre(Request $request, ContratBail $contrat)
    {
        $request->validate([
            'motif_suspension' => ['required', 'string', 'max:1000'],
        ], [
            'motif_suspension.required' => 'Le motif de suspension est obligatoire.',
        ]);

        if (! $contrat->peutEtreSuspendu()) {
            return back()->with('error', 'Seul un contrat actif peut être suspendu.');
        }

        $contrat->update([
            'statut'           => StatutContrat::Suspendu->value,
            'motif_suspension' => $request->motif_suspension,
            'date_suspension'  => now(),
        ]);

        return back()->with('success', "Contrat {$contrat->numero_contrat} suspendu.");
    }

    public function resilier(Request $request, ContratBail $contrat)
    {
        $request->validate([
            'motif_resiliation' => ['required', 'string', 'max:1000'],
            'preavis_mois'      => ['nullable', 'integer', 'min:0', 'max:24'],
        ], [
            'motif_resiliation.required' => 'Le motif de résiliation est obligatoire.',
        ]);

        if (! $contrat->peutEtreResilie()) {
            return back()->with('error', 'Ce contrat ne peut pas être résilié dans son état actuel.');
        }

        $preavis = $request->integer('preavis_mois', $contrat->preavis_mois ?? 3);

        $contrat->update([
            'statut'            => StatutContrat::Resilie->value,
            'motif_resiliation' => $request->motif_resiliation,
            'date_resiliation'  => now(),
            'preavis_mois'      => $preavis,
            'date_fin_preavis'  => now()->copy()->addMonths($preavis),
        ]);

        return back()->with('success', "Contrat {$contrat->numero_contrat} résilié.");
    }

    // ── Helpers ─────────────────────────────────────────────────────

    /**
     * Policiers éligibles : statut éligible et sans contrat en cours
     * (le policier du contrat en cours d'édition reste inclus).
     */
    private function policiersDisponibles(?ContratBail $contrat = null)
    {
        return Policier::with('grade')
            ->eligibles()
            ->where(function ($q) use ($contrat) {
                $q->whereDoesntHave('contratsBail', fn($c) => $c->whereIn('statut', self::STATUTS_EN_COURS));
                if ($contrat) {
                    $q->orWhere('id', $contrat->policier_id);
                }
            })
            ->orderBy('nom')
            ->orderBy('prenoms')
            ->get();
    }

    /**
     * Logements libres (sans contrat en cours) — celui du contrat édité reste inclus.
     */
    private function logementsDisponibles(?ContratBail $contrat = null)
    {
        return LogementCivil::with(['localite', 'proprietaire'])
            ->where('actif', true)
            ->where(function ($q) use ($contrat) {
                $q->whereDoesntHave('contratsBail', fn($c) => $c->whereIn('statut', self::STATUTS_EN_COURS));
                if ($contrat) {
                    $q->orWhere('id', $contrat->logement_civil_id);
                }
            })
            ->orderBy('reference')
            ->get();
    }
}
