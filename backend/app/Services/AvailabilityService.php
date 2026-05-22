<?php


namespace App\Services;

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Collection;

class AvailabilityService
{
    /**
     * Return all materials available for the given period.
     * A material is available if:
     *  - its status is 'available'
     *  - it has no conflicting validated/pending reservation
     */
    public function checkAvailability(string $startDate, string $endDate): Collection
    {
        // Get IDs of materials that have conflicts in this period
        $conflictingIds = Reservation::query()
            ->conflicting('', $startDate, $endDate)
            ->whereNotNull('material_id')
            ->pluck('material_id')
            ->unique()
            ->toArray();

        // Fix: conflicting scope needs material_id — use raw query instead
        $conflictingIds = Reservation::query()
            ->whereNotIn('status', ['cancelled', 'rejected'])
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
     */
    public function hasConflict(
        string  $materialId,
        string  $startDate,
        string  $endDate,
        ?string $excludeReservationId = null
    ): bool {
        return Reservation::conflicting($materialId, $startDate, $endDate, $excludeReservationId)
            ->exists();
    }
}
