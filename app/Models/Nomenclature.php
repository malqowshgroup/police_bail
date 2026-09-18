<?php

namespace App\Models;

use App\Support\Nomenclatures;
use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Nomenclature extends Model
{
    use LogsActivite;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'actif'   => 'boolean',
            'systeme' => 'boolean',
            'ordre'   => 'integer',
        ];
    }

    /** Libellés humains des catégories. */
    public const CATEGORIES = [
        'statut_contrat'   => 'Statuts de contrat',
        'statut_policier'  => 'Statuts de policier',
        'statut_bordereau' => 'Statuts de bordereau',
        'statut_reglement' => 'Statuts de règlement',
        'type_reglement'   => 'Types de règlement',
        'statut_virement'  => 'Statuts de virement',
        'statut_document'  => 'Statuts de document',
        'type_document'    => 'Types de document',
    ];

    public function getCategorieLabelAttribute(): string
    {
        return self::CATEGORIES[$this->categorie] ?? $this->categorie;
    }

    // Vide le cache du résolveur à chaque écriture.
    protected static function booted(): void
    {
        $flush = fn () => Nomenclatures::flush();
        static::saved($flush);
        static::deleted($flush);
    }

    public function scopeCategorie(Builder $q, string $categorie): Builder
    {
        return $q->where('categorie', $categorie);
    }
}
