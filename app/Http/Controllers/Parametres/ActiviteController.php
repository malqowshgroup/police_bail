<?php

namespace App\Http\Controllers\Parametres;

use App\Http\Controllers\Controller;
use App\Models\JournalAction;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index(Request $request)
    {
        $query = JournalAction::with('user')
            ->when($request->filled('action'), fn($q) => $q->where('action', $request->action))
            ->when($request->filled('module'), fn($q) => $q->where('entite_type', $request->module))
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->search;
                $q->where(function ($q) use ($s) {
                    $q->whereRaw("JSON_EXTRACT(details, '$.entite_label') LIKE ?", ["%$s%"])
                      ->orWhereRaw("JSON_EXTRACT(details, '$.user_nom') LIKE ?", ["%$s%"])
                      ->orWhere('entite_id', is_numeric($s) ? (int) $s : null);
                });
            })
            ->when($request->filled('date_debut'), fn($q) => $q->whereDate('created_at', '>=', $request->date_debut))
            ->when($request->filled('date_fin'),   fn($q) => $q->whereDate('created_at', '<=', $request->date_fin))
            ->latest('created_at')
            ->paginate(60)
            ->withQueryString();

        $modules = JournalAction::selectRaw('entite_type, COUNT(*) as nb')
            ->groupBy('entite_type')
            ->orderByDesc('nb')
            ->pluck('nb', 'entite_type');

        $users = User::orderBy('name')->get(['id', 'name']);

        return view('activites.index', [
            'activites' => $query,
            'modules'   => $modules,
            'users'     => $users,
            'actions'   => ActivityLogger::ACTIONS,
            'total'     => JournalAction::count(),
        ]);
    }
}
