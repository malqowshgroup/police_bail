<?php

namespace App\Support;

use App\Models\JournalAction;

class ActivityLogger
{
    /** Libellés humains des actions. */
    public const ACTIONS = [
        'creation'     => ['Création',    'bg-green-100 text-green-700',  'ti-plus'],
        'modification' => ['Modification','bg-orange-100 text-orange-700','ti-pencil'],
        'suppression'  => ['Suppression', 'bg-red-100 text-red-700',      'ti-trash'],
        'activation'   => ['Activation',  'bg-teal-100 text-teal-700',    'ti-circle-check'],
        'suspension'   => ['Suspension',  'bg-amber-100 text-amber-700',  'ti-player-pause'],
        'resiliation'  => ['Résiliation', 'bg-purple-100 text-purple-700','ti-file-off'],
        'validation'   => ['Validation',  'bg-blue-100 text-blue-700',    'ti-circle-check'],
        'refus'        => ['Refus',        'bg-rose-100 text-rose-700',    'ti-x'],
        'soumission'   => ['Soumission',  'bg-indigo-100 text-indigo-700','ti-send'],
        'connexion'    => ['Connexion',   'bg-slate-100 text-slate-600',  'ti-login'],
        'deconnexion'  => ['Déconnexion', 'bg-slate-100 text-slate-600',  'ti-logout'],
    ];

    /** Libellés humains des modules. */
    public const MODULES = [
        'Policier'      => 'Policier',
        'ContratBail'   => 'Contrat de bail',
        'LogementCivil' => 'Logement civil',
        'Proprietaire'  => 'Propriétaire',
        'Reglement'     => 'Règlement',
        'Virement'      => 'Virement',
        'Document'      => 'Document',
        'Bordereau'     => 'Bordereau',
        'User'          => 'Utilisateur',
        'Grade'         => 'Grade',
        'Localite'      => 'Localité',
        'Service'       => 'Service',
        'Nomenclature'  => 'Nomenclature',
    ];

    private static array $HIDDEN = ['password', 'remember_token', 'email_verified_at'];

    public static function log(
        string  $action,
        string  $module,
        ?int    $entiteId    = null,
        ?string $entiteLabel = null,
        ?array  $avant       = null,
        ?array  $apres       = null,
        ?string $entiteType  = null,
    ): void {
        try {
            $avant = $avant ? array_diff_key($avant, array_flip(self::$HIDDEN)) : null;
            $apres = $apres ? array_diff_key($apres, array_flip(self::$HIDDEN)) : null;

            JournalAction::create([
                'user_id'     => auth()->id(),
                'action'      => $action,
                'entite_type' => $entiteType ?? $module,
                'entite_id'   => $entiteId,
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
                'details'     => array_filter([
                    'user_nom'     => auth()->user()?->name,
                    'module'       => self::MODULES[$module] ?? $module,
                    'entite_label' => $entiteLabel,
                    'avant'        => $avant ?: null,
                    'apres'        => $apres ?: null,
                ]),
            ]);
        } catch (\Throwable) {
            // ne jamais bloquer le flux principal
        }
    }
}
