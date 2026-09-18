<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DocumentLien extends Model
{
    protected $guarded = [];

    /** Types d'entités liables (clé => libellé). */
    public const ENTITES = [
        'policier'       => 'Policier',
        'logement_civil' => 'Logement',
        'proprietaire'   => 'Propriétaire',
        'contrat_bail'   => 'Contrat de bail',
        'bordereau'      => 'Bordereau',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /** Morphologie personnalisée : colonnes entite_type / entite_id (résolues via le morph map). */
    public function entite(): MorphTo
    {
        return $this->morphTo('entite', 'entite_type', 'entite_id');
    }

    public function getEntiteTypeLabelAttribute(): string
    {
        return self::ENTITES[$this->entite_type] ?? $this->entite_type;
    }

    /** Libellé lisible de l'entité liée. */
    public function getEntiteLabelAttribute(): string
    {
        $e = $this->entite;
        if (! $e) {
            return '#'.$this->entite_id.' (introuvable)';
        }

        return match($this->entite_type) {
            'policier'       => trim("{$e->nom} {$e->prenoms}").' — '.$e->matricule,
            'logement_civil' => $e->reference.' ('.$e->quartier.')',
            'proprietaire'   => $e->nom_complet,
            'contrat_bail'   => $e->numero_contrat,
            'bordereau'      => $e->numero,
            default          => '#'.$this->entite_id,
        };
    }

    /** URL de la fiche de l'entité liée (null si non résolvable). */
    public function getEntiteUrlAttribute(): ?string
    {
        if (! $this->entite) {
            return null;
        }

        return match($this->entite_type) {
            'policier'       => route('policiers.show', $this->entite_id),
            'logement_civil' => route('logements.show', $this->entite_id),
            'proprietaire'   => route('proprietaires.show', $this->entite_id),
            'contrat_bail'   => route('contrats.show', $this->entite_id),
            'bordereau'      => route('bordereaux.show', $this->entite_id),
            default          => null,
        };
    }
}
