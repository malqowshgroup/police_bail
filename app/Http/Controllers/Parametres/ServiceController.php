<?php

namespace App\Http\Controllers\Parametres;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametres\StoreServiceRequest;
use App\Http\Requests\Parametres\UpdateServiceRequest;
use App\Models\Localite;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $services = Service::withCount('policiers')
            ->when($request->search, fn($q, $s) => $q->where('libelle', 'like', "%$s%")->orWhere('code', 'like', "%$s%"))
            ->when($request->filled('actif'), fn($q) => $q->where('actif', $request->boolean('actif')))
            ->orderBy('libelle')
            ->paginate(20)->withQueryString();

        return view('parametres.services.index', compact('services'));
    }

    public function create()
    {
        return view('parametres.services.create', ['localites' => $this->localites()]);
    }

    public function store(StoreServiceRequest $request)
    {
        Service::create($request->validated());

        return redirect()->route('parametres.services.index')
            ->with('success', 'Service créé.');
    }

    public function edit(Service $service)
    {
        return view('parametres.services.edit', ['service' => $service, 'localites' => $this->localites()]);
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());

        return redirect()->route('parametres.services.index')
            ->with('success', 'Service mis à jour.');
    }

    public function destroy(Service $service)
    {
        if ($service->policiers()->exists()) {
            return back()->with('error', 'Impossible de supprimer ce service : des policiers y sont rattachés. Désactivez-le plutôt.');
        }

        $service->delete();

        return redirect()->route('parametres.services.index')
            ->with('success', 'Service supprimé.');
    }

    /** Libellés de localités pour le sélecteur (le champ services.localite est une chaîne). */
    private function localites()
    {
        return Localite::where('actif', true)->orderBy('libelle')->pluck('libelle');
    }
}
