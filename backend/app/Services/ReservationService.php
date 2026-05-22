<?php


namespace App\Services;

use App\Enums\MaterialStatus;
use App\Enums\ReservationStatus;
use App\Events\ReservationRejected;
use App\Events\ReservationSubmitted;
use App\Events\ReservationValidated;
use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class ReservationService
{
    public function __construct(
        private readonly AvailabilityService $availabilityService
    ) {}

    public function list(array $filters): LengthAwarePaginator
    {
        return Reservation::query()
            ->with(['user', 'material.category'])
            ->when($filters['status']   ?? null, fn($q, $v) => $q->where('status', $v))
            ->when($filters['user_id']  ?? null, fn($q, $v) => $q->where('user_id', $v))
            ->when($filters['start']    ?? null, fn($q, $v) => $q->where('start_date', '>=', $v))
            ->when($filters['end']      ?? null, fn($q, $v) => $q->where('end_date',   '<=', $v))
            ->latest()
            ->paginate(15);
    }

    public function findOrFail(string $id): Reservation
    {
        return Reservation::with(['user', 'material.category'])->findOrFail($id);
    }

    public function submit(User $user, array $data): Reservation
    {
        $reservation = Reservation::create([
            'user_id'     => $user->id,
            'material_id' => null,
            'start_date'  => $data['start_date'],
            'end_date'    => $data['end_date'],
            'status'      => ReservationStatus::Pending->value,
        ]);

        $reservation->load(['user', 'material']);
        event(new ReservationSubmitted($reservation));

        return $reservation;
    }

    public function validate(Reservation $reservation, string $materialId): Reservation
    {
        // Must be pending
        if (! $reservation->isPending()) {
            throw ValidationException::withMessages([
                'reservation' => ['Seules les réservations en attente peuvent être validées.'],
            ]);
        }

        // Check material exists and is available
        $material = Material::findOrFail($materialId);

        if ($material->status !== MaterialStatus::Available) {
            throw ValidationException::withMessages([
                'material_id' => ['Ce matériel n\'est pas disponible (statut : ' . $material->status->label() . ').'],
            ]);
        }

        // Check no date conflict
        if ($this->availabilityService->hasConflict(
            $materialId,
            $reservation->start_date->format('Y-m-d'),
            $reservation->end_date->format('Y-m-d'),
            $reservation->id
        )) {
            throw ValidationException::withMessages([
                'material_id' => ['Ce matériel est déjà réservé sur cette période.'],
            ]);
        }

        // Update reservation
        $reservation->update([
            'material_id' => $materialId,
            'status'      => ReservationStatus::Validated,
        ]);

        // Update material status to in_use
        $material->update(['status' => MaterialStatus::InUse]);

        $reservation->load(['user', 'material.category']);
        event(new ReservationValidated($reservation));

        return $reservation->fresh(['user', 'material.category']);
    }

    public function reject(Reservation $reservation, ?string $reason): Reservation
    {
        if (! $reservation->isPending()) {
            throw ValidationException::withMessages([
                'reservation' => ['Seules les réservations en attente peuvent être rejetées.'],
            ]);
        }

        $reservation->update([
            'status'           => ReservationStatus::Rejected,
            'rejection_reason' => $reason,
        ]);

        $reservation->load(['user', 'material']);
        event(new ReservationRejected($reservation));

        return $reservation->fresh(['user', 'material']);
    }
}
