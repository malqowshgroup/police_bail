<?php

namespace App\Models;

use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grade extends Model
{
    protected $fillable = [
        'libelle',
        'taux_bail',
        'ordre',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif'      => 'boolean',
            'taux_bail'  => 'integer',
            'ordre'      => 'integer',
        ];
    }

    public function policiers(): HasMany
    {
        return $this->hasMany(Policier::class);
    }

    public function contratsBail(): HasMany
    {
        return $this->hasMany(ContratBail::class);
    }
}
