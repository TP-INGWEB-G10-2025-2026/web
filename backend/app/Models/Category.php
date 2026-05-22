<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

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
    /**
     * Scope to load material count.
     */
    public function scopeWithMaterialCount(Builder $query): Builder
    {
        return $query->withCount('materials');
    }
}
