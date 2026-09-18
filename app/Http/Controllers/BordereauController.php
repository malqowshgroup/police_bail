<?php

namespace App\Http\Controllers;

use App\Enums\StatutBordereau;
use App\Enums\StatutContrat;
use App\Http\Requests\StoreBordereauRequest;
use App\Http\Requests\UpdateBordereauRequest;
use App\Models\Bordereau;
use App\Models\ContratBail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BordereauController extends Controller
{
    public function index(Request $request)
    {
        $query = Bordereau::withCount('contrats')
            ->withSum('contrats', 'taux_bail')
            ->when($request->search, fn($q, $s) => $q->where('numero', 'like', "%$s%"))
            ->when($request->statut, fn($q, $st) => $q->where('statut', $st))
            ->when($request->annee, fn($q, $a) => $q->where('annee', $a))
            ->orderByDesc('created_at');

        $bordereaux = $query->paginate(20)->withQueryString();

        $stats = [
            'total'    => Bordereau::count(),
            'en_cours' => Bordereau::enCours()->count(),
            'valides'  => Bordereau::valides()->count(),
            'contrats_lies' => ContratBail::whereNotNull('bordereau_id')->count(),
        ];

        $statuts = StatutBordereau::options();
        $annees  = Bordereau::query()->distinct()->orderByDesc('annee')->pluck('annee');

        return view('bordereaux.index', compact('bordereaux', 'stats', 'statuts', 'annees'));
    }

    public function create()
    {
        return view('bordereaux.create', [
            'annee'     => now()->year,
            'numero'    => Bordereau::genererNumero(),
            'contrats'  => $this->contratsDisponibles(),
        ]);
    }

    public function store(StoreBordereauRequest $request)
    {
        $data = $request->validated();

        $bordereau = DB::transaction(function () use ($data) {
            $bordereau = Bordereau::create([
                'numero'       => Bordereau::genererNumero($data['annee']),
                'annee'        => $data['annee'],
                'statut'       => StatutBordereau::EnSaisie->value,
                'observations' => $data['observations'] ?? null,
                'saisi_par'    => Auth::id(),
                'date_saisie'  => now(),
            ]);

            $this->attacherContrats($bordereau, $data['contrats'] ?? []);

            return $bordereau;
        });

        return redirect()->route('bordereaux.show', $bordereau)
            ->with('success', "Bordereau {$bordereau->numero} créé.");
    }

    public function show(Bordereau $bordereau)
    {
        $bordereau->load([
            'contrats.policier.grade', 'contrats.logementCivil',
            'saisiPar', 'controlePar', 'validePar',
        ]);

        return view('bordereaux.show', compact('bordereau'));
    }

    public function edit(Bordereau $bordereau)
    {
        if (! $bordereau->contenuModifiable()) {
            return redirect()->route('bordereaux.show', $bordereau)
                ->with('error', 'Ce bordereau n\'est plus modifiable (il a quitté la phase de saisie).');
        }

        $bordereau->load('contrats.policier.grade', 'contrats.logementCivil');

        return view('bordereaux.edit', [
            'bordereau' => $bordereau,
            'contrats'  => $this->contratsDisponibles($bordereau),
        ]);
    }

    public function update(UpdateBordereauRequest $request, Bordereau $bordereau)
    {
        if (! $bordereau->contenuModifiable()) {
            return redirect()->route('bordereaux.show', $bordereau)
                ->with('error', 'Ce bordereau n\'est plus modifiable.');
        }

        $data = $request->validated();

        DB::transaction(function () use ($bordereau, $data) {
            $bordereau->update([
                'annee'        => $data['annee'],
                'observations' => $data['observations'] ?? null,
            ]);

            $this->synchroniserContrats($bordereau, $data['contrats'] ?? []);
        });

        return redirect()->route('bordereaux.show', $bordereau)
            ->with('success', 'Bordereau mis à jour.');
    }

    public function destroy(Bordereau $bordereau)
    {
        if (! $bordereau->isEnSaisie()) {
            return redirect()->route('bordereaux.index')
                ->with('error', 'Seul un bordereau en saisie peut être supprimé.');
        }

        $numero = $bordereau->numero;

        DB::transaction(function () use ($bordereau) {
            $bordereau->contrats()->update(['bordereau_id' => null]);
            $bordereau->delete();
        });

        return redirect()->route('bordereaux.index')
            ->with('success', "Bordereau {$numero} supprimé. Ses contrats ont été détachés.");
    }

    // ── Circuit de validation ───────────────────────────────────────

    public function soumettre(Bordereau $bordereau)
    {
        if (! $bordereau->peutEtreSoumis()) {
            return back()->with('error', 'Le bordereau doit être en saisie et contenir au moins un contrat.');
        }

        $bordereau->update(['statut' => StatutBordereau::EnControle->value]);

        return back()->with('success', "Bordereau {$bordereau->numero} transmis au contrôle.");
    }

    public function controler(Bordereau $bordereau)
    {
        if (! $bordereau->peutEtreControle()) {
            return back()->with('error', 'Ce bordereau n\'est pas en attente de contrôle.');
        }

        $bordereau->update([
            'statut'        => StatutBordereau::EnValidation->value,
            'controle_par'  => Auth::id(),
            'date_controle' => now(),
        ]);

        return back()->with('success', "Bordereau {$bordereau->numero} contrôlé et transmis à la validation.");
    }

    public function valider(Bordereau $bordereau)
    {
        if (! $bordereau->peutEtreValide()) {
            return back()->with('error', 'Ce bordereau n\'est pas en attente de validation.');
        }

        DB::transaction(function () use ($bordereau) {
            $bordereau->update([
                'statut'          => StatutBordereau::Valide->value,
                'valide_par'      => Auth::id(),
                'date_validation' => now(),
            ]);

            // Active tous les contrats « en attente » du bordereau.
            $bordereau->contrats()
                ->where('statut', StatutContrat::EnAttente->value)
                ->update([
                    'statut'          => StatutContrat::Actif->value,
                    'valide_par'      => Auth::id(),
                    'date_validation' => now(),
                ]);
        });

        return back()->with('success', "Bordereau {$bordereau->numero} validé. Les contrats rattachés sont désormais actifs.");
    }

    public function rejeter(Request $request, Bordereau $bordereau)
    {
        $request->validate([
            'motif_rejet' => ['required', 'string', 'max:1000'],
        ], [
            'motif_rejet.required' => 'Le motif de rejet est obligatoire.',
        ]);

        if (! $bordereau->peutEtreRejete()) {
            return back()->with('error', 'Ce bordereau ne peut pas être rejeté dans son état actuel.');
        }

        $bordereau->update([
            'statut'       => StatutBordereau::Rejete->value,
            'observations' => trim(($bordereau->observations ? $bordereau->observations."\n" : '')
                .'Rejet : '.$request->motif_rejet),
        ]);

        return back()->with('success', "Bordereau {$bordereau->numero} rejeté.");
    }

    // ── Helpers ─────────────────────────────────────────────────────

    /**
     * Contrats « en attente » sans bordereau (ceux du bordereau édité restent inclus).
     */
    private function contratsDisponibles(?Bordereau $bordereau = null)
    {
        return ContratBail::with(['policier.grade', 'logementCivil'])
            ->where('statut', StatutContrat::EnAttente->value)
            ->where(function ($q) use ($bordereau) {
                $q->whereNull('bordereau_id');
                if ($bordereau) {
                    $q->orWhere('bordereau_id', $bordereau->id);
                }
            })
            ->orderBy('numero_contrat')
            ->get();
    }

    /** Rattache une liste de contrats (création). */
    private function attacherContrats(Bordereau $bordereau, array $ids): void
    {
        if (empty($ids)) {
            return;
        }

        ContratBail::whereIn('id', $ids)
            ->where('statut', StatutContrat::EnAttente->value)
            ->whereNull('bordereau_id')
            ->update(['bordereau_id' => $bordereau->id]);
    }

    /** Resynchronise les contrats rattachés (édition) : détache les retirés, rattache les ajoutés. */
    private function synchroniserContrats(Bordereau $bordereau, array $ids): void
    {
        // Détache ceux qui ne sont plus sélectionnés.
        $bordereau->contrats()
            ->whereNotIn('id', $ids ?: [0])
            ->update(['bordereau_id' => null]);

        // Rattache les nouveaux (toujours en attente et libres).
        ContratBail::whereIn('id', $ids ?: [0])
            ->where('statut', StatutContrat::EnAttente->value)
            ->whereNull('bordereau_id')
            ->update(['bordereau_id' => $bordereau->id]);
    }
}
