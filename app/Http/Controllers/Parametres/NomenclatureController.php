<?php

namespace App\Http\Controllers\Parametres;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametres\StoreNomenclatureRequest;
use App\Http\Requests\Parametres\UpdateNomenclatureRequest;
use App\Models\Nomenclature;
use Illuminate\Http\Request;

class NomenclatureController extends Controller
{
    public function index(Request $request)
    {
        $categories = Nomenclature::CATEGORIES;
        $categorie  = array_key_exists((string) $request->categorie, $categories)
            ? $request->categorie
            : array_key_first($categories);

        $entrees = Nomenclature::categorie($categorie)
            ->orderBy('ordre')->orderBy('libelle')
            ->get();

        return view('parametres.nomenclatures.index', compact('categories', 'categorie', 'entrees'));
    }

    public function create(Request $request)
    {
        $categories = Nomenclature::CATEGORIES;
        $categorie  = array_key_exists((string) $request->categorie, $categories)
            ? $request->categorie
            : array_key_first($categories);

        return view('parametres.nomenclatures.create', compact('categories', 'categorie'));
    }

    public function store(StoreNomenclatureRequest $request)
    {
        $data = $request->validated();
        $data['systeme'] = false; // toute entrée créée à la main est non-système

        Nomenclature::create($data);

        return redirect()->route('parametres.nomenclatures.index', ['categorie' => $data['categorie']])
            ->with('success', 'Valeur ajoutée. Note : une nouvelle valeur est d\'affichage uniquement et ne déclenche aucune logique métier.');
    }

    public function edit(Nomenclature $nomenclature)
    {
        return view('parametres.nomenclatures.edit', compact('nomenclature'));
    }

    public function update(UpdateNomenclatureRequest $request, Nomenclature $nomenclature)
    {
        $nomenclature->update($request->validated());

        return redirect()->route('parametres.nomenclatures.index', ['categorie' => $nomenclature->categorie])
            ->with('success', 'Valeur mise à jour.');
    }

    public function destroy(Nomenclature $nomenclature)
    {
        if ($nomenclature->systeme) {
            return back()->with('error', 'Cette valeur est câblée à la logique métier : elle ne peut pas être supprimée (libellé/couleur restent modifiables). Désactivez-la si besoin.');
        }

        $categorie = $nomenclature->categorie;
        $nomenclature->delete();

        return redirect()->route('parametres.nomenclatures.index', ['categorie' => $categorie])
            ->with('success', 'Valeur supprimée.');
    }
}
