<?php


namespace App\Models;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory, HasUuids;


    protected $fillable = [
        'reservation_id',
        'user_id',
        'material_id',
        'loan_date',
        'expected_return_date',
        'actual_return_date',
        'return_status',
        'notes',
    ];

    protected $casts = [
        'loan_date'            => 'date',
        'expected_return_date' => 'date',
        'actual_return_date'   => 'date',
        'return_status'        => ReturnStatus::class,
    ];

    // ── Relations ─────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    // ── Utility methods ───────────────────────────────────

    /**
     * Loan has been returned (actual_return_date is set).
     */
    public function isReturned(): bool
    {
        return ! is_null($this->actual_return_date);
    }

    /**
     * Loan is past due and not yet returned.
     */
    public function isOverdue(): bool
    {
        return ! $this->isReturned()
            && $this->expected_return_date->isPast();
    }

    /**
     * Loan is active and not yet overdue.
     */
    public function isOngoing(): bool
    {
        return ! $this->isReturned()
            && ! $this->isOverdue();
    }

    /**
     * Number of overdue days (0 if not overdue).
     */
    public function overdueDays(): int
    {
        if (! $this->isOverdue()) return 0;

        return (int) $this->expected_return_date->diffInDays(Carbon::today());
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeReturned(Builder $query): Builder
    {
        return $query->whereNotNull('actual_return_date');
    }

    public function scopeOngoing(Builder $query): Builder
    {
        return $query->whereNull('actual_return_date')
                     ->where('expected_return_date', '>=', today());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNull('actual_return_date')
                     ->where('expected_return_date', '<', today());
    }

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForMaterial(Builder $query, string $materialId): Builder
    {
        return $query->where('material_id', $materialId);
    }
}
