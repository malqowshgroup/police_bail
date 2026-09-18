<?php

namespace App\Http\Controllers;

use App\Enums\StatutDocument;
use App\Enums\TypeDocument;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Bordereau;
use App\Models\ContratBail;
use App\Models\Document;
use App\Models\DocumentLien;
use App\Models\LogementCivil;
use App\Models\Policier;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::withCount('liens')
            ->when($request->search, fn($q, $s) => $q->where('nom_original', 'like', "%$s%"))
            ->when($request->type, fn($q, $t) => $q->where('type_document', $t))
            ->when($request->statut, fn($q, $st) => $q->where('statut', $st))
            ->when($request->entite_type, function ($q) use ($request) {
                $q->whereHas('liens', function ($l) use ($request) {
                    $l->where('entite_type', $request->entite_type);
                    if ($request->entite_id) {
                        $l->where('entite_id', $request->entite_id);
                    }
                });
            })
            ->orderByDesc('created_at');

        $documents = $query->paginate(20)->withQueryString();

        $stats = [
            'total'      => Document::count(),
            'en_attente' => Document::enAttente()->count(),
            'valides'    => Document::valides()->count(),
            'volume_ko'  => (int) Document::sum('taille_ko'),
        ];

        return view('documents.index', [
            'documents'  => $documents,
            'stats'      => $stats,
            'types'      => TypeDocument::options(),
            'statuts'    => StatutDocument::options(),
            'entites'    => DocumentLien::ENTITES,
            'policiers'  => Policier::orderBy('nom')->get(['id', 'nom', 'prenoms', 'matricule']),
            'logements'  => LogementCivil::orderBy('reference')->get(['id', 'reference', 'quartier']),
            'proprietaires' => Proprietaire::orderBy('nom')->get(['id', 'nom', 'prenoms', 'raison_sociale']),
        ]);
    }

    public function create(Request $request)
    {
        return view('documents.create', [
            'types'         => TypeDocument::options(),
            'entites'       => DocumentLien::ENTITES,
            'entitesParType' => $this->entitesParType(),
            'preEntiteType' => $request->entite_type,
            'preEntiteId'   => $request->integer('entite_id') ?: null,
        ]);
    }

    public function store(StoreDocumentRequest $request)
    {
        $data = $request->validated();
        $file = $request->file('fichier');

        $chemin = $file->store('documents');

        $document = Document::create([
            'nom_original'  => $file->getClientOriginalName(),
            'type_document' => $data['type_document'],
            'chemin_local'  => $chemin,
            'taille_ko'     => (int) ceil($file->getSize() / 1024),
            'format'        => strtoupper($file->getClientOriginalExtension()),
            'statut'        => StatutDocument::EnAttente->value,
            'uploaded_par'  => Auth::id(),
            'observations'  => $data['observations'] ?? null,
        ]);

        if (! empty($data['entite_type']) && ! empty($data['entite_id'])) {
            $document->liens()->create([
                'entite_type' => $data['entite_type'],
                'entite_id'   => $data['entite_id'],
            ]);
        }

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document téléversé avec succès.');
    }

    public function show(Document $document)
    {
        $document->load(['liens.entite', 'uploadedPar', 'validePar']);

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        return view('documents.edit', [
            'document' => $document,
            'types'    => TypeDocument::options(),
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $document->update($request->validated());

        return redirect()->route('documents.show', $document)
            ->with('success', 'Document mis à jour.');
    }

    public function destroy(Document $document)
    {
        if ($document->chemin_local && Storage::exists($document->chemin_local)) {
            Storage::delete($document->chemin_local);
        }

        $document->delete(); // cascade sur document_liens

        return redirect()->route('documents.index')
            ->with('success', 'Document supprimé.');
    }

    // ── Actions ─────────────────────────────────────────────────────

    public function valider(Document $document)
    {
        $document->update([
            'statut'          => StatutDocument::Valide->value,
            'valide_par'      => Auth::id(),
            'date_validation' => now(),
        ]);

        return back()->with('success', 'Document validé.');
    }

    public function rejeter(Request $request, Document $document)
    {
        $request->validate([
            'motif_rejet' => ['required', 'string', 'max:1000'],
        ], ['motif_rejet.required' => 'Le motif de rejet est obligatoire.']);

        $document->update([
            'statut'       => StatutDocument::Rejete->value,
            'observations' => trim(($document->observations ? $document->observations."\n" : '')
                .'Rejet : '.$request->motif_rejet),
        ]);

        return back()->with('success', 'Document rejeté.');
    }

    public function preview(Document $document)
    {
        if (! $document->fichierDisponible() || ! Storage::exists($document->chemin_local)) {
            abort(404, 'Fichier introuvable sur le serveur.');
        }

        return response()->file(Storage::path($document->chemin_local));
    }

    public function download(Document $document)
    {
        if (! $document->chemin_local || ! Storage::exists($document->chemin_local)) {
            return back()->with('error', 'Fichier introuvable sur le serveur.');
        }

        return Storage::download($document->chemin_local, $document->nom_original);
    }

    // ── Helpers ─────────────────────────────────────────────────────

    /** Cartographie {type => [{id, label}]} pour le sélecteur dépendant du formulaire. */
    private function entitesParType(): array
    {
        return [
            'policier' => Policier::orderBy('nom')->get()
                ->map(fn($p) => ['id' => $p->id, 'label' => "{$p->nom} {$p->prenoms} — {$p->matricule}"])->values(),
            'logement_civil' => LogementCivil::orderBy('reference')->get()
                ->map(fn($l) => ['id' => $l->id, 'label' => "{$l->reference} ({$l->quartier})"])->values(),
            'proprietaire' => Proprietaire::orderBy('nom')->get()
                ->map(fn($p) => ['id' => $p->id, 'label' => $p->nom_complet])->values(),
            'contrat_bail' => ContratBail::orderByDesc('id')->get()
                ->map(fn($c) => ['id' => $c->id, 'label' => $c->numero_contrat])->values(),
            'bordereau' => Bordereau::orderByDesc('id')->get()
                ->map(fn($b) => ['id' => $b->id, 'label' => $b->numero])->values(),
        ];
    }
}
