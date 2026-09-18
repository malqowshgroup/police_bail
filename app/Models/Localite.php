<?php

namespace App\Models;

use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Localite extends Model
{
    protected $fillable = [
        'code',
        'libelle',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }

    public function policiers(): HasMany
    {
        return $this->hasMany(Policier::class);
    }

    public function citesPolicieres(): HasMany
    {
        return $this->hasMany(CitePolicieres::class);
    }

    public function proprietaires(): HasMany
    {
        return $this->hasMany(Proprietaire::class);
    }

    public function logementsCivils(): HasMany
    {
        return $this->hasMany(LogementCivil::class);
    }
}
