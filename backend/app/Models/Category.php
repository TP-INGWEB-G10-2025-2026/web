<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name'];

    // ─── Relations ───────────────────────────────────────────────

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    /**
     * Scope withMaterialCount : charge le nombre de matériels liés.
     * Usage : Category::withMaterialCount()->get()
     */
    public function scopeWithMaterialCount($query)
    {
        return $query->withCount('materials');
    }
}
