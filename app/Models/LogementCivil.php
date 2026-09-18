<?php

namespace App\Models;

use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LogementCivil extends Model
{
    use HasFactory;

    protected $table = 'logements_civils';
    protected $guarded = [];

    public function localite(): BelongsTo { return $this->belongsTo(Localite::class); }
    public function proprietaire(): BelongsTo { return $this->belongsTo(Proprietaire::class); }
    public function beneficiaire(): BelongsTo { return $this->belongsTo(Beneficiaire::class); }
    public function contratsBail(): HasMany { return $this->hasMany(ContratBail::class); }
    public function contratActif(): HasOne {
        return $this->hasOne(ContratBail::class)->where('statut', 'actif');
    }
    public function documentLiens(): MorphMany {
        return $this->morphMany(DocumentLien::class, 'entite', 'entite_type', 'entite_id');
    }

    public function getAdresseCourtAttribute(): string
    {
        return collect([$this->quartier, $this->ilot ? 'Îlot '.$this->ilot : null, $this->lot ? 'Lot '.$this->lot : null])
            ->filter()->implode(', ');
    }

    public function isOccupe(): bool
    {
        return $this->contratsBail()->where('statut', 'actif')->exists();
    }
}
