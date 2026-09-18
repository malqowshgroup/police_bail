<?php

namespace App\Models;

use App\Enums\StatutDocument;
use App\Enums\TypeDocument;
use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use LogsActivite;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'taille_ko'       => 'integer',
            'date_validation' => 'datetime',
        ];
    }

    // Relations
    public function liens(): HasMany
    {
        return $this->hasMany(DocumentLien::class);
    }

    public function uploadedPar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_par');
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    // Accesseurs
    public function getStatutEnumAttribute(): StatutDocument
    {
        return StatutDocument::from($this->statut);
    }

    public function getTypeEnumAttribute(): TypeDocument
    {
        return TypeDocument::from($this->type_document);
    }

    public function getTailleHumaineAttribute(): string
    {
        $ko = (int) $this->taille_ko;
        if ($ko <= 0) {
            return '—';
        }
        return $ko >= 1024
            ? number_format($ko / 1024, 1, ',', ' ').' Mo'
            : $ko.' Ko';
    }

    public function getIconeAttribute(): string
    {
        return match(strtolower((string) $this->format)) {
            'pdf'                 => 'ti-file-type-pdf',
            'jpg', 'jpeg', 'png'  => 'ti-photo',
            'tiff', 'tif'         => 'ti-photo-scan',
            default               => 'ti-file',
        };
    }

    public function getEstImageAttribute(): bool
    {
        return in_array(strtolower((string) $this->format), ['jpg', 'jpeg', 'png'], true);
    }

    // États
    public function isValide(): bool
    {
        return $this->statut === StatutDocument::Valide->value;
    }

    public function peutEtreValide(): bool
    {
        return $this->statut !== StatutDocument::Valide->value;
    }

    public function peutEtreRejete(): bool
    {
        return $this->statut !== StatutDocument::Rejete->value;
    }

    public function fichierDisponible(): bool
    {
        return ! empty($this->chemin_local);
    }

    // Scopes
    public function scopeEnAttente(Builder $query): Builder
    {
        return $query->where('statut', StatutDocument::EnAttente->value);
    }

    public function scopeValides(Builder $query): Builder
    {
        return $query->where('statut', StatutDocument::Valide->value);
    }
}
