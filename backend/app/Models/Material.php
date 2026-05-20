<?php

namespace App\Models;

use App\Enums\MaterialStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'status',
        'description',
        'price',   // ✅ Ajouté
        'stock',   // ✅ Ajouté
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'status' => MaterialStatus::class,
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}