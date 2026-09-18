<?php

namespace App\Http\Controllers\Parametres;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametres\StoreLocaliteRequest;
use App\Http\Requests\Parametres\UpdateLocaliteRequest;
use App\Models\Localite;
use Illuminate\Http\Request;

class LocaliteController extends Controller
{
    public function index(Request $request)
    {
        $localites = Localite::withCount(['logementsCivils', 'policiers', 'proprietaires'])
            ->when($request->search, fn($q, $s) => $q->where('libelle', 'like', "%$s%")->orWhere('code', 'like', "%$s%"))
            ->when($request->filled('actif'), fn($q) => $q->where('actif', $request->boolean('actif')))
            ->orderBy('libelle')
            ->paginate(20)->withQueryString();

        return view('parametres.localites.index', compact('localites'));
    }

    public function create()
    {
        return view('parametres.localites.create');
    }

    public function store(StoreLocaliteRequest $request)
    {
        Localite::create($request->validated());

        return redirect()->route('parametres.localites.index')
            ->with('success', 'Localité créée.');
    }

    public function edit(Localite $localite)
    {
        return view('parametres.localites.edit', compact('localite'));
    }

    public function update(UpdateLocaliteRequest $request, Localite $localite)
    {
        $localite->update($request->validated());

        return redirect()->route('parametres.localites.index')
            ->with('success', 'Localité mise à jour.');
    }

    public function destroy(Localite $localite)
    {
        if ($localite->logementsCivils()->exists() || $localite->policiers()->exists()
            || $localite->proprietaires()->exists() || $localite->citesPolicieres()->exists()) {
            return back()->with('error', 'Impossible de supprimer cette localité : elle est référencée ailleurs. Désactivez-la plutôt.');
        }

        $localite->delete();

        return redirect()->route('parametres.localites.index')
            ->with('success', 'Localité supprimée.');
    }
}
