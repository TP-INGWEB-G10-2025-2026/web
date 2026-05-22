<?php


namespace App\Models;

use App\Enums\MaterialStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Material extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'status',
        'description',
    ];

    protected $casts = [
        'status' => MaterialStatus::class,
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function isAvailable(): bool
    {
        return $this->status === MaterialStatus::Available;
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }
}
