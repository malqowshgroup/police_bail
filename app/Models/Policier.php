<?php

namespace App\Models;

use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Policier extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_naissance'      => 'date',
            'date_prise_service'  => 'date',
            'date_statut'         => 'date',
            'statut'              => \App\Enums\StatutPolicier::class,
        ];
    }

    // Relations BelongsTo
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function localite(): BelongsTo
    {
        return $this->belongsTo(Localite::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relations HasMany
    public function logementsCites(): HasMany
    {
        return $this->hasMany(LogementCite::class);
    }

    public function contratsBail(): HasMany
    {
        return $this->hasMany(ContratBail::class);
    }

    // HasOne : contrat bail actif
    public function contratBailActif(): HasOne
    {
        return $this->hasOne(ContratBail::class)->where('statut', 'actif');
    }

    public function documentLiens(): MorphMany
    {
        return $this->morphMany(DocumentLien::class, 'entite', 'entite_type', 'entite_id');
    }

    // Scopes
    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('statut', 'actif');
    }

    public function scopeEligibles(Builder $query): Builder
    {
        return $query->whereIn('statut', ['actif', 'detachement', 'stagiaire']);
    }
}
