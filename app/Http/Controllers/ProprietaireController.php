<?php
namespace App\Http\Controllers;

use App\Models\Proprietaire;
use App\Models\Localite;
use App\Http\Requests\StoreProprietaireRequest;
use App\Http\Requests\UpdateProprietaireRequest;
use Illuminate\Http\Request;

class ProprietaireController extends Controller
{
    public function index(Request $request)
    {
        $query = Proprietaire::with('localite')
            ->withCount('logementsCivils')
            ->when($request->search, fn($q, $s) => $q->where(function($q) use ($s) {
                $q->where('nom', 'like', "%$s%")
                  ->orWhere('prenoms', 'like', "%$s%")
                  ->orWhere('raison_sociale', 'like', "%$s%")
                  ->orWhere('num_piece_identite', 'like', "%$s%");
            }))
            ->when($request->type_personne, fn($q, $t) => $q->where('type_personne', $t))
            ->when($request->localite_id, fn($q, $l) => $q->where('localite_id', $l))
            ->when($request->filled('actif'), fn($q) => $q->where('actif', $request->actif))
            ->orderBy('nom');

        $proprietaires = $query->paginate(20)->withQueryString();
        $localites = Localite::orderBy('libelle')->get();

        $stats = [
            'total'           => Proprietaire::count(),
            'physiques'       => Proprietaire::where('type_personne', 'physique')->count(),
            'morales'         => Proprietaire::where('type_personne', 'morale')->count(),
            'logements_count' => \App\Models\LogementCivil::count(),
        ];

        return view('proprietaires.index', compact('proprietaires', 'localites', 'stats'));
    }

    public function create()
    {
        $localites = Localite::where('actif', true)->orderBy('libelle')->get();
        return view('proprietaires.create', compact('localites'));
    }

    public function store(StoreProprietaireRequest $request)
    {
        $proprietaire = Proprietaire::create($request->validated());
        return redirect()->route('proprietaires.show', $proprietaire)
            ->with('success', 'Propriétaire enregistré avec succès.');
    }

    public function show(Proprietaire $proprietaire)
    {
        $proprietaire->load(['localite', 'logementsCivils.localite', 'documentLiens.document']);
        return view('proprietaires.show', compact('proprietaire'));
    }

    public function edit(Proprietaire $proprietaire)
    {
        $localites = Localite::where('actif', true)->orderBy('libelle')->get();
        return view('proprietaires.edit', compact('proprietaire', 'localites'));
    }

    public function update(UpdateProprietaireRequest $request, Proprietaire $proprietaire)
    {
        $proprietaire->update($request->validated());
        return redirect()->route('proprietaires.show', $proprietaire)
            ->with('success', 'Propriétaire mis à jour avec succès.');
    }

    public function destroy(Proprietaire $proprietaire)
    {
        if ($proprietaire->logementsCivils()->exists()) {
            return redirect()->route('proprietaires.index')
                ->with('error', 'Impossible de supprimer ce propriétaire : il possède des logements enregistrés.');
        }
        $proprietaire->delete();
        return redirect()->route('proprietaires.index')
            ->with('success', 'Propriétaire supprimé.');
    }
}
