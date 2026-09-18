<?php

namespace App\Traits;

use App\Support\ActivityLogger;

trait LogsActivite
{
    public static function bootLogsActivite(): void
    {
        static::created(function ($m) {
            ActivityLogger::log(
                'creation',
                class_basename($m),
                $m->id,
                $m->getActiviteLabel(),
            );
        });

        static::updated(function ($m) {
            $changes = $m->getChanges();
            unset($changes['updated_at']);
            if (empty($changes)) {
                return;
            }

            // Détection de transitions de statut spéciales
            $action = 'modification';
            if (isset($changes['statut'])) {
                $action = match ($changes['statut']) {
                    'actif'                     => 'activation',
                    'suspendu'                  => 'suspension',
                    'resilie', 'en_resiliation' => 'resiliation',
                    'valide'                    => 'validation',
                    'rejete'                    => 'refus',
                    'soumis'                    => 'soumission',
                    default                     => 'modification',
                };
            }

            $avant = array_intersect_key($m->getOriginal(), $changes);

            ActivityLogger::log(
                $action,
                class_basename($m),
                $m->id,
                $m->getActiviteLabel(),
                $avant,
                $changes,
            );
        });

        static::deleted(function ($m) {
            ActivityLogger::log(
                'suppression',
                class_basename($m),
                $m->id,
                $m->getActiviteLabel(),
            );
        });
    }

    /** Libellé lisible de l'entité pour le journal. */
    public function getActiviteLabel(): string
    {
        return $this->reference
            ?? $this->numero_contrat
            ?? $this->numero
            ?? $this->libelle
            ?? $this->raison_sociale
            ?? (isset($this->nom, $this->prenoms) ? trim($this->nom . ' ' . $this->prenoms) : null)
            ?? $this->nom
            ?? $this->name
            ?? ('#' . $this->id);
    }
}
