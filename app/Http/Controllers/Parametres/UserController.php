<?php

namespace App\Http\Controllers\Parametres;

use App\Http\Controllers\Controller;
use App\Http\Requests\Parametres\StoreUserRequest;
use App\Http\Requests\Parametres\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /** Métadonnées d'affichage des rôles : nom => [libellé, badge]. */
    public const ROLES = [
        'administrateur'   => ['Administrateur',     'bg-red-100 text-red-700'],
        'directeur_solde'  => ['Directeur Solde',    'bg-indigo-100 text-indigo-700'],
        'responsable_baux' => ['Responsable Baux',   'bg-orange-100 text-orange-700'],
        'agent_validation' => ['Agent Validation',   'bg-green-100 text-green-700'],
        'agent_controle'   => ['Agent Contrôle',     'bg-blue-100 text-blue-700'],
        'agent_saisie'     => ['Agent Saisie',       'bg-slate-100 text-slate-600'],
    ];

    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, fn($q, $s) => $q->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%");
            }))
            ->when($request->role, fn($q, $r) => $q->whereHas('roles', fn($x) => $x->where('name', $r)))
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        return view('parametres.utilisateurs.index', [
            'users'      => $users,
            'rolesMeta'  => self::ROLES,
            'roles'      => $this->roles(),
            'total'      => User::count(),
            'admins'     => User::role('administrateur')->count(),
        ]);
    }

    public function create()
    {
        return view('parametres.utilisateurs.create', [
            'roles'     => $this->roles(),
            'rolesMeta' => self::ROLES,
        ]);
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'], // hashé via cast 'hashed'
        ]);
        $user->syncRoles($data['roles']);

        return redirect()->route('parametres.utilisateurs.index')
            ->with('success', "Utilisateur {$user->name} créé.");
    }

    public function edit(User $user)
    {
        return view('parametres.utilisateurs.edit', [
            'user'      => $user->load('roles'),
            'roles'     => $this->roles(),
            'rolesMeta' => self::ROLES,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        // Empêche de retirer son propre rôle administrateur (auto-verrouillage).
        if ($user->id === Auth::id() && $user->hasRole('administrateur') && ! in_array('administrateur', $data['roles'], true)) {
            return back()->withInput()->with('error', 'Vous ne pouvez pas retirer votre propre rôle administrateur.');
        }

        // Empêche de retirer le dernier administrateur.
        if ($this->estDernierAdmin($user) && ! in_array('administrateur', $data['roles'], true)) {
            return back()->withInput()->with('error', 'Impossible de retirer le rôle au dernier administrateur.');
        }

        $user->update([
            'name'  => $data['name'],
            'email' => $data['email'],
        ]);

        if (! empty($data['password'])) {
            $user->update(['password' => $data['password']]);
        }

        $user->syncRoles($data['roles']);

        return redirect()->route('parametres.utilisateurs.index')
            ->with('success', "Utilisateur {$user->name} mis à jour.");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        if ($this->estDernierAdmin($user)) {
            return back()->with('error', 'Impossible de supprimer le dernier administrateur.');
        }

        $nom = $user->name;
        $user->delete();

        return redirect()->route('parametres.utilisateurs.index')
            ->with('success', "Utilisateur {$nom} supprimé.");
    }

    // ── Helpers ─────────────────────────────────────────────────────

    /** Rôles ordonnés du plus permissif au moins permissif (selon ROLES). */
    private function roles()
    {
        return Role::where('guard_name', 'web')
            ->get()
            ->sortBy(fn($r) => array_search($r->name, array_keys(self::ROLES)))
            ->values();
    }

    private function estDernierAdmin(User $user): bool
    {
        return $user->hasRole('administrateur') && User::role('administrateur')->count() <= 1;
    }
}
