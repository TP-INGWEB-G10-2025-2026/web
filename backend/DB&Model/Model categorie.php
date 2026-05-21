<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasUuids;

    protected $fillable = ['name'];

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function scopeWithMaterialCount($query)
    {
        return $query->withCount('materials');
    }
}

?>