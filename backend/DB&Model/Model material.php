<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = ['name', 'category_id', 'status', 'description'];

    protected $casts = [
        'status' => \App\Enums\MaterialStatus::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isAvailable(): bool
    {
        return $this->status === \App\Enums\MaterialStatus::Available;
    }
}

?>