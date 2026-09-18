<?php

namespace App\Models;

use App\Enums\StatutContrat;
use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContratBail extends Model
{
    use SoftDeletes, LogsActivite;

    protected $table = 'contrats_bail';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date_debut'           => 'date',
            'date_fin'             => 'date',
            'date_suspension'      => 'date',
            'date_levee_suspension' => 'date',
            'date_resiliation'     => 'date',
            'date_fin_preavis'     => 'date',
            'date_debut_arrieres'  => 'date',
            'date_validation'      => 'datetime',
            'avec_arrieres'        => 'boolean',
            'taux_bail'            => 'decimal:2',
        ];
    }

    // Relations BelongsTo
    public function policier(): BelongsTo
    {
        return $this->belongsTo(Policier::class);
    }

    public function logementCivil(): BelongsTo
    {
        return $this->belongsTo(LogementCivil::class);
    }

    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function bordereau(): BelongsTo
    {
        return $this->belongsTo(Bordereau::class);
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

    // Relations HasMany
    public function reglements(): HasMany
    {
        return $this->hasMany(Reglement::class);
    }

    public function confirmations(): HasMany
    {
        return $this->hasMany(ConfirmationTrimestrielle::class);
    }

    // Documents via lien polymorphique (morphologie personnalisée : entite_type/entite_id)
    public function documentLiens(): MorphMany
    {
        return $this->morphMany(DocumentLien::class, 'entite', 'entite_type', 'entite_id');
    }

    // Accesseurs
    public function getStatutEnumAttribute(): StatutContrat
    {
        return StatutContrat::from($this->statut);
    }

    // Business logic
    public function isPaiable(): bool
    {
        return $this->statut === StatutContrat::Actif->value;
    }

    public function isActif(): bool
    {
        return $this->statut === StatutContrat::Actif->value;
    }

    public function isSuspendu(): bool
    {
        return $this->statut === StatutContrat::Suspendu->value;
    }

    public function isResilie(): bool
    {
        return $this->statut === StatutContrat::Resilie->value;
    }

    public function peutEtreActive(): bool
    {
        return in_array($this->statut, [
            StatutContrat::EnAttente->value,
            StatutContrat::Suspendu->value,
        ], true);
    }

    public function peutEtreSuspendu(): bool
    {
        return $this->statut === StatutContrat::Actif->value;
    }

    public function peutEtreResilie(): bool
    {
        return in_array($this->statut, [
            StatutContrat::Actif->value,
            StatutContrat::Suspendu->value,
            StatutContrat::EnResiliation->value,
        ], true);
    }

    /**
     * Génère le prochain numéro de contrat : CB-{année}-{séquence}.
     */
    public static function genererNumero(): string
    {
        $annee = now()->year;
        $prefixe = "CB-{$annee}-";

        $dernier = static::withTrashed()
            ->where('numero_contrat', 'like', $prefixe.'%')
            ->orderByDesc('numero_contrat')
            ->value('numero_contrat');

        $sequence = $dernier
            ? ((int) substr($dernier, strlen($prefixe))) + 1
            : 1;

        return $prefixe.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    // Scopes
    public function scopeActifs(Builder $query): Builder
    {
        return $query->where('statut', StatutContrat::Actif->value);
    }

    public function scopeSuspendus(Builder $query): Builder
    {
        return $query->where('statut', StatutContrat::Suspendu->value);
    }

    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', StatutContrat::EnAttente->value);
    }
}
