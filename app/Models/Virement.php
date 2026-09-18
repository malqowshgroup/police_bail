<?php

namespace App\Models;

use App\Enums\StatutVirement;
use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Virement extends Model
{
    use LogsActivite;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'montant_total'  => 'decimal:2',
            'date_virement'  => 'date',
            'date_emission'  => 'datetime',
            'date_execution' => 'datetime',
        ];
    }

    // Relations
    public function proprietaire(): BelongsTo
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function reglements(): HasMany
    {
        return $this->hasMany(Reglement::class);
    }

    public function emisPar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'emis_par');
    }

    public function executePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'execute_par');
    }

    // Accesseurs
    public function getStatutEnumAttribute(): StatutVirement
    {
        return StatutVirement::from($this->statut);
    }

    // États
    public function isEnPreparation(): bool
    {
        return $this->statut === StatutVirement::EnPreparation->value;
    }

    public function isExecute(): bool
    {
        return $this->statut === StatutVirement::Execute->value;
    }

    /** Le contenu (règlements) n'est modifiable qu'en préparation. */
    public function contenuModifiable(): bool
    {
        return $this->isEnPreparation();
    }

    public function peutEtreEmis(): bool
    {
        return $this->statut === StatutVirement::EnPreparation->value && $this->reglements()->exists();
    }

    public function peutEtreExecute(): bool
    {
        return $this->statut === StatutVirement::Emis->value;
    }

    public function peutEtreAnnule(): bool
    {
        return in_array($this->statut, [
            StatutVirement::EnPreparation->value,
            StatutVirement::Emis->value,
        ], true);
    }

    /** Recalcule le montant total depuis les règlements rattachés. */
    public function recalculerMontant(): void
    {
        $this->montant_total = $this->reglements()->sum('montant');
        $this->save();
    }

    /**
     * Génère le prochain numéro : VIR-{année}-{séquence}.
     */
    public static function genererNumero(?int $annee = null): string
    {
        $annee ??= now()->year;
        $prefixe = "VIR-{$annee}-";

        $dernier = static::where('numero', 'like', $prefixe.'%')
            ->orderByDesc('numero')
            ->value('numero');

        $sequence = $dernier
            ? ((int) substr($dernier, strlen($prefixe))) + 1
            : 1;

        return $prefixe.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopeEnCours(Builder $query): Builder
    {
        return $query->whereIn('statut', [
            StatutVirement::EnPreparation->value,
            StatutVirement::Emis->value,
        ]);
    }

    public function scopeExecutes(Builder $query): Builder
    {
        return $query->where('statut', StatutVirement::Execute->value);
    }
}
