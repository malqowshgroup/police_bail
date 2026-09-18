<?php

namespace App\Models;

use App\Traits\LogsActivite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['actif' => 'boolean'];
    }

    public function policiers(): HasMany
    {
        return $this->hasMany(Policier::class);
    }
}
