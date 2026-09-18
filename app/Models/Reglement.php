<?php

namespace App\Models;

use App\Enums\StatutReglement;
use App\Enums\TypeReglement;
use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reglement extends Model
{
    use LogsActivite;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'periode_mois'  => 'integer',
            'periode_annee' => 'integer',
            'montant'       => 'decimal:2',
            'date_virement' => 'date',
        ];
    }

    // Relations
    public function contratBail(): BelongsTo
    {
        return $this->belongsTo(ContratBail::class);
    }

    public function generePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'genere_par');
    }

    public function virement(): BelongsTo
    {
        return $this->belongsTo(Virement::class);
    }

    // Accesseurs
    public function getStatutEnumAttribute(): StatutReglement
    {
        return StatutReglement::from($this->statut);
    }

    public function getTypeEnumAttribute(): TypeReglement
    {
        return TypeReglement::from($this->type_reglement);
    }

    public function getPeriodeLabelAttribute(): string
    {
        $mois = [
            1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
            5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
            9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre',
        ];

        return ($mois[$this->periode_mois] ?? '?').' '.$this->periode_annee;
    }

    // États
    public function isAPayer(): bool
    {
        return $this->statut === StatutReglement::APayer->value;
    }

    public function isVire(): bool
    {
        return $this->statut === StatutReglement::Vire->value;
    }

    public function peutEtreModifie(): bool
    {
        return $this->statut === StatutReglement::APayer->value;
    }

    public function peutPasserEnAttenteVirement(): bool
    {
        return $this->statut === StatutReglement::APayer->value;
    }

    public function peutEtreAnnule(): bool
    {
        return in_array($this->statut, [
            StatutReglement::APayer->value,
            StatutReglement::EnAttenteVirement->value,
        ], true);
    }

    // Scopes
    public function scopeAPayer(Builder $query): Builder
    {
        return $query->where('statut', StatutReglement::APayer->value);
    }

    public function scopeEnAttenteVirement(Builder $query): Builder
    {
        return $query->where('statut', StatutReglement::EnAttenteVirement->value);
    }

    public function scopeVires(Builder $query): Builder
    {
        return $query->where('statut', StatutReglement::Vire->value);
    }

    public function scopePeriode(Builder $query, int $mois, int $annee): Builder
    {
        return $query->where('periode_mois', $mois)->where('periode_annee', $annee);
    }
}
