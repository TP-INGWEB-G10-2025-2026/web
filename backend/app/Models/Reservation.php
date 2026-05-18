<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'material_id',
        'start_date',
        'end_date',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'status'     => 'string',
    ];

    // ─── Statuts disponibles ─────────────────────────────────────

    const STATUS_PENDING   = 'pending';
    const STATUS_VALIDATED = 'validated';
    const STATUS_REJECTED  = 'rejected';
    const STATUS_CANCELLED = 'cancelled';

    // ─── Méthodes utilitaires ─────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isValidated(): bool
    {
        return $this->status === self::STATUS_VALIDATED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    // ─── Relations ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────

    /**
     * Scope conflicting : détecte les chevauchements de dates
     * sur un même matériel validé.
     *
     * Usage : Reservation::conflicting($materialId, $start, $end)->exists()
     */
    public function scopeConflicting($query, string $materialId, string $start, string $end)
    {
        return $query
            ->where('material_id', $materialId)
            ->where('status', self::STATUS_VALIDATED)
            ->where(function ($q) use ($start, $end) {
                // Chevauchement : start ≤ end_demande ET end ≥ start_demande
                $q->where('start_date', '<=', $end)
                  ->where('end_date',   '>=', $start);
            });
    }

    /**
     * Scope pending : uniquement les demandes en attente.
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope forUser : réservations d'un utilisateur donné.
     */
    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }
}
