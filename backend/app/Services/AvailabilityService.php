<?php

namespace App\Services;

use App\Enums\MaterialStatus;
use App\Enums\ReservationStatus;
use App\Models\Material;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityService
{
    /**
     * Return all materials available for the given period.
     * A material is available if:
     *  - its status is 'available'
     *  - it has no conflicting VALIDATED reservation (pending is ignored)
     */
    public function checkAvailability(string $startDate, string $endDate): Collection
    {
        $conflictingIds = Reservation::query()
            ->whereIn('status', [
                ReservationStatus::Validated->value,  // ← only validated blocks a slot
            ])
            ->where('start_date', '<=', $endDate)
            ->where('end_date',   '>=', $startDate)
            ->whereNotNull('material_id')
            ->pluck('material_id')
            ->unique()
            ->toArray();

        return Material::query()
            ->with('category')
            ->where('status', MaterialStatus::Available->value)
            ->whereNotIn('id', $conflictingIds)
            ->get();
    }

    /**
     * Check if a specific material has a conflict in the given period.
     * Used at validation time — pending reservations DO count here
     * to prevent two admins validating the same material simultaneously.
     */
    public function hasConflict(
        string  $materialId,
        string  $startDate,
        string  $endDate,
        ?string $excludeReservationId = null
    ): bool {
        return Reservation::query()
            ->whereIn('status', [
                ReservationStatus::Validated->value,  // ← still blocks at validation
            ])
            ->where('material_id', $materialId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date',   '>=', $startDate)
            ->when($excludeReservationId, fn($q) => $q->where('id', '!=', $excludeReservationId))
            ->exists();
    }
}
