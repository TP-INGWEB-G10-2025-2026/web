<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;



class Category extends Model
{
    use HasUuids ;
    use HasFactory; 
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['name'];

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    /**
     * Scope to load material count.
     */
    public function scopeWithMaterialCount(Builder $query): Builder
    {
        return $query->withCount('materials');
    }
}
