<?php

namespace App\Http\Controllers;

use App\Models\LogementCivil;
use App\Models\Policier;
use App\Models\Proprietaire;
use App\Models\Localite;
use App\Http\Requests\StoreLogementCivilRequest;
use App\Http\Requests\UpdateLogementCivilRequest;
use Illuminate\Http\Request;

class LogementCivilController extends Controller
{
    public function index(Request $request)
    {
        $query = LogementCivil::with(['localite', 'proprietaire'])
            ->withCount('contratsBail')
            ->when($request->search, fn($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('reference', 'like', "%$s%")
                  ->orWhere('quartier', 'like', "%$s%")
                  ->orWhere('adresse_complete', 'like', "%$s%");
            }))
            ->when($request->localite_id, fn($q, $l) => $q->where('localite_id', $l))
            ->when($request->proprietaire_id, fn($q, $p) => $q->where('proprietaire_id', $p))
            ->when($request->filled('statut'), function ($q) use ($request) {
                if ($request->statut === 'occupe') {
                    $q->whereHas('contratsBail', fn($c) => $c->where('statut', 'actif'));
                } else {
                    $q->whereDoesntHave('contratsBail', fn($c) => $c->where('statut', 'actif'));
                }
            })
            ->orderBy('reference');

        $logements     = $query->paginate(20)->withQueryString();
        $localites     = Localite::orderBy('libelle')->get();
        $proprietaires = Proprietaire::orderBy('nom')->get();

        $total  = LogementCivil::count();
        $libres = LogementCivil::whereDoesntHave('contratsBail', fn($q) => $q->where('statut', 'actif'))->count();
        $policiersSans = Policier::where('statut', 'actif')
            ->whereDoesntHave('contratsBail', fn($q) => $q->where('statut', 'actif'))->count();

        $stats = [
            'total'                   => $total,
            'occupes'                 => $total - $libres,
            'libres'                  => $libres,
            'proprietaires'           => Proprietaire::count(),
            'policiers_sans_logement' => $policiersSans,
            'deficit'                 => max(0, $policiersSans - $libres),
            'taux_occupation'         => $total > 0 ? round(($total - $libres) / $total * 100) : 0,
        ];

        return view('logements.index', compact('logements', 'localites', 'proprietaires', 'stats'));
    }

    public function create()
    {
        $localites     = Localite::where('actif', true)->orderBy('libelle')->get();
        $proprietaires = Proprietaire::where('actif', true)->orderBy('nom')->get();
        return view('logements.create', compact('localites', 'proprietaires'));
    }

    public function store(StoreLogementCivilRequest $request)
    {
        $logement = LogementCivil::create($request->validated());
        return redirect()->route('logements.show', $logement)
            ->with('success', 'Logement enregistré avec succès.');
    }

    public function show(LogementCivil $logement)
    {
        $logement->load(['localite', 'proprietaire.localite', 'contratsBail.policier.grade', 'documentLiens.document']);
        $contratActif = $logement->contratsBail->firstWhere('statut', 'actif');
        return view('logements.show', compact('logement', 'contratActif'));
    }

    public function edit(LogementCivil $logement)
    {
        $localites     = Localite::where('actif', true)->orderBy('libelle')->get();
        $proprietaires = Proprietaire::where('actif', true)->orderBy('nom')->get();
        return view('logements.edit', compact('logement', 'localites', 'proprietaires'));
    }

    public function update(UpdateLogementCivilRequest $request, LogementCivil $logement)
    {
        $logement->update($request->validated());
        return redirect()->route('logements.show', $logement)
            ->with('success', 'Logement mis à jour avec succès.');
    }

    public function destroy(LogementCivil $logement)
    {
        if ($logement->contratsBail()->exists()) {
            return redirect()->route('logements.index')
                ->with('error', 'Impossible de supprimer ce logement : il possède des contrats de bail.');
        }
        $logement->delete();
        return redirect()->route('logements.index')
            ->with('success', 'Logement supprimé.');
    }
}
