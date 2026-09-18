<?php
namespace App\Models;
use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Proprietaire extends Model
{
    use HasFactory, LogsActivite;
    protected $guarded = [];

    protected function casts(): array {
        return ['actif' => 'boolean'];
    }

    public function localite(): BelongsTo { return $this->belongsTo(Localite::class); }
    public function logementsCivils(): HasMany { return $this->hasMany(LogementCivil::class); }
    public function documentLiens(): MorphMany {
        return $this->morphMany(DocumentLien::class, 'entite', 'entite_type', 'entite_id');
    }

    public function getNomCompletAttribute(): string {
        if ($this->type_personne === 'morale') {
            return $this->raison_sociale ?? $this->nom;
        }
        return trim($this->nom . ' ' . ($this->prenoms ?? ''));
    }

    public function getTypePieceLibelleAttribute(): string {
        return match($this->type_piece) {
            'cni' => 'Carte Nationale d\'Identité',
            'passeport' => 'Passeport',
            'sejour' => 'Titre de séjour',
            default => ucfirst($this->type_piece ?? ''),
        };
    }
}
