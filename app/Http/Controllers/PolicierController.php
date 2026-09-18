<?php
namespace App\Http\Controllers;

use App\Models\Policier;
use App\Models\Grade;
use App\Models\Service;
use App\Models\Localite;
use App\Http\Requests\StorePolicierRequest;
use App\Http\Requests\UpdatePolicierRequest;
use Illuminate\Http\Request;

class PolicierController extends Controller
{
    public function index(Request $request)
    {
        $query = Policier::with(['grade', 'service', 'localite'])
            ->when($request->search, fn($q, $s) => $q->where(function($q) use ($s) {
                $q->where('matricule', 'like', "%$s%")
                  ->orWhere('nom', 'like', "%$s%")
                  ->orWhere('prenoms', 'like', "%$s%");
            }))
            ->when($request->statut, fn($q, $s) => $q->where('statut', $s))
            ->when($request->grade_id, fn($q, $g) => $q->where('grade_id', $g))
            ->when($request->localite_id, fn($q, $l) => $q->where('localite_id', $l))
            ->orderBy('nom');

        $policiers = $query->paginate(20)->withQueryString();
        $grades = Grade::orderBy('ordre')->get();
        $localites = Localite::orderBy('libelle')->get();
        $stats = [
            'total'       => Policier::count(),
            'actifs'      => Policier::where('statut', 'actif')->count(),
            'suspendus'   => Policier::where('statut', 'suspendu')->count(),
            'avec_bail'   => \App\Models\ContratBail::where('statut', 'actif')->count(),
        ];

        return view('policiers.index', compact('policiers', 'grades', 'localites', 'stats'));
    }

    public function create()
    {
        $grades   = Grade::where('actif', true)->orderBy('ordre')->get();
        $services = Service::where('actif', true)->orderBy('libelle')->get();
        $localites = Localite::where('actif', true)->orderBy('libelle')->get();
        return view('policiers.create', compact('grades', 'services', 'localites'));
    }

    public function store(StorePolicierRequest $request)
    {
        $policier = Policier::create($request->validated());
        return redirect()->route('policiers.show', $policier)
            ->with('success', 'Policier enregistré avec succès.');
    }

    public function show(Policier $policier)
    {
        $policier->load(['grade', 'service', 'localite', 'contratsBail.logementCivil', 'contratsBail.grade', 'documentLiens.document']);
        $contratActif = $policier->contratsBail()->where('statut', 'actif')->with('logementCivil')->first();
        return view('policiers.show', compact('policier', 'contratActif'));
    }

    public function edit(Policier $policier)
    {
        $grades    = Grade::where('actif', true)->orderBy('ordre')->get();
        $services  = Service::where('actif', true)->orderBy('libelle')->get();
        $localites = Localite::where('actif', true)->orderBy('libelle')->get();
        return view('policiers.edit', compact('policier', 'grades', 'services', 'localites'));
    }

    public function update(UpdatePolicierRequest $request, Policier $policier)
    {
        $policier->update($request->validated());
        return redirect()->route('policiers.show', $policier)
            ->with('success', 'Policier mis à jour avec succès.');
    }

    public function destroy(Policier $policier)
    {
        $policier->delete();
        return redirect()->route('policiers.index')
            ->with('success', 'Policier supprimé.');
    }
}
