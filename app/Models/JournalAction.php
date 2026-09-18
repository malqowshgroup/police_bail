<?php

namespace App\Models;

use App\Support\ActivityLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JournalAction extends Model
{
    protected $table    = 'journal_actions';
    protected $guarded  = [];
    const UPDATED_AT    = null;

    protected function casts(): array
    {
        return ['details' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Accesseurs pratiques depuis details JSON ──────────────────────

    public function getUserNomAttribute(): string
    {
        return $this->details['user_nom'] ?? 'Système';
    }

    public function getModuleAttribute(): string
    {
        return $this->details['module'] ?? ($this->entite_type ?? '—');
    }

    public function getEntiteLabelAttribute(): ?string
    {
        return $this->details['entite_label'] ?? null;
    }

    public function getAvantAttribute(): array
    {
        return $this->details['avant'] ?? [];
    }

    public function getApresAttribute(): array
    {
        return $this->details['apres'] ?? [];
    }

    /** Badge HTML de l'action. */
    public function badgeAction(): string
    {
        $cfg = ActivityLogger::ACTIONS[$this->action] ?? ['Inconnu', 'bg-slate-100 text-slate-600', 'ti-help'];
        return sprintf(
            '<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold %s"><i class="ti %s text-xs"></i>%s</span>',
            $cfg[1], $cfg[2], $cfg[0]
        );
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeAction($q, string $a)  { return $q->where('action', $a); }
    public function scopeModule($q, string $m)  { return $q->where('entite_type', $m); }
    public function scopeRecent($q)             { return $q->latest('created_at'); }
}
