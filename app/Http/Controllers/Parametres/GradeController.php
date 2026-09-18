<?php

namespace App\Http\Controllers\Parametres;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametres\StoreGradeRequest;
use App\Http\Requests\Parametres\UpdateGradeRequest;
use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $grades = Grade::withCount(['policiers', 'contratsBail'])
            ->when($request->search, fn($q, $s) => $q->where('libelle', 'like', "%$s%"))
            ->when($request->filled('actif'), fn($q) => $q->where('actif', $request->boolean('actif')))
            ->orderBy('ordre')->orderByDesc('taux_bail')
            ->paginate(20)->withQueryString();

        return view('parametres.grades.index', compact('grades'));
    }

    public function create()
    {
        return view('parametres.grades.create');
    }

    public function store(StoreGradeRequest $request)
    {
        Grade::create($request->validated());

        return redirect()->route('parametres.grades.index')
            ->with('success', 'Grade créé.');
    }

    public function edit(Grade $grade)
    {
        return view('parametres.grades.edit', compact('grade'));
    }

    public function update(UpdateGradeRequest $request, Grade $grade)
    {
        $grade->update($request->validated());

        return redirect()->route('parametres.grades.index')
            ->with('success', 'Grade mis à jour.');
    }

    public function destroy(Grade $grade)
    {
        if ($grade->policiers()->exists() || $grade->contratsBail()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce grade : il est référencé par des policiers ou des contrats. Désactivez-le plutôt.');
        }

        $grade->delete();

        return redirect()->route('parametres.grades.index')
            ->with('success', 'Grade supprimé.');
    }
}
