<?php

namespace App\Models;

use App\Enums\StatutBordereau;
use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Bordereau extends Model
{
    use LogsActivite;

    protected $table = 'bordereaux';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'annee'           => 'integer',
            'date_saisie'     => 'datetime',
            'date_controle'   => 'datetime',
            'date_validation' => 'datetime',
        ];
    }

    // Relations
    public function contrats(): HasMany
    {
        return $this->hasMany(ContratBail::class);
    }

    public function saisiPar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'saisi_par');
    }

    public function controlePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'controle_par');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function documentLiens(): MorphMany
    {
        return $this->morphMany(DocumentLien::class, 'entite', 'entite_type', 'entite_id');
    }

    // Accesseurs
    public function getStatutEnumAttribute(): StatutBordereau
    {
        return StatutBordereau::from($this->statut);
    }

    public function getMontantTotalAttribute(): float
    {
        return (float) $this->contrats->sum('taux_bail');
    }

    // États
    public function isEnSaisie(): bool
    {
        return $this->statut === StatutBordereau::EnSaisie->value;
    }

    public function isValide(): bool
    {
        return $this->statut === StatutBordereau::Valide->value;
    }

    public function estCloture(): bool
    {
        return in_array($this->statut, [
            StatutBordereau::Valide->value,
            StatutBordereau::Rejete->value,
        ], true);
    }

    /** Le contenu (contrats) n'est modifiable qu'en saisie. */
    public function contenuModifiable(): bool
    {
        return $this->isEnSaisie();
    }

    public function peutEtreSoumis(): bool
    {
        return $this->statut === StatutBordereau::EnSaisie->value && $this->contrats()->exists();
    }

    public function peutEtreControle(): bool
    {
        return $this->statut === StatutBordereau::EnControle->value;
    }

    public function peutEtreValide(): bool
    {
        return $this->statut === StatutBordereau::EnValidation->value;
    }

    public function peutEtreRejete(): bool
    {
        return in_array($this->statut, [
            StatutBordereau::EnControle->value,
            StatutBordereau::EnValidation->value,
        ], true);
    }

    /**
     * Génère le prochain numéro : BORD-{année}-{séquence}.
     */
    public static function genererNumero(?int $annee = null): string
    {
        $annee ??= now()->year;
        $prefixe = "BORD-{$annee}-";

        $dernier = static::where('numero', 'like', $prefixe.'%')
            ->orderByDesc('numero')
            ->value('numero');

        $sequence = $dernier
            ? ((int) substr($dernier, strlen($prefixe))) + 1
            : 1;

        return $prefixe.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopeValides(Builder $query): Builder
    {
        return $query->where('statut', StatutBordereau::Valide->value);
    }

    public function scopeEnCours(Builder $query): Builder
    {
        return $query->whereIn('statut', [
            StatutBordereau::EnSaisie->value,
            StatutBordereau::EnControle->value,
            StatutBordereau::EnValidation->value,
        ]);
    }
}
