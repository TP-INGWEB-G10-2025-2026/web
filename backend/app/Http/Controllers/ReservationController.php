<?php


namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reservation\RejectReservationRequest;
use App\Http\Requests\Reservation\StoreReservationRequest;
use App\Http\Requests\Reservation\ValidateReservationRequest;
use App\Http\Resources\AvailabilityResource;
use App\Http\Resources\Material\MaterialResource;
use App\Http\Resources\Reservation\ReservationCollection;
use App\Http\Resources\Reservation\ReservationResource;
use App\Services\AvailabilityService;
use App\Services\ReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function __construct(
        private readonly ReservationService  $reservationService,
        private readonly AvailabilityService $availabilityService,
    ) {}

    /** GET /api/v1/reservations — admin */
    public function index(Request $request): JsonResponse
    {
        $reservations = $this->reservationService->list(
            $request->only(['status', 'user_id', 'start', 'end'])
        );

        return response()->json(new ReservationCollection($reservations));
    }

    /** GET /api/v1/reservations/{id} — admin */
    public function show(string $id): JsonResponse
    {
        $reservation = $this->reservationService->findOrFail($id);

        return response()->json(['data' => new ReservationResource($reservation)]);
    }

    /** POST /api/v1/reservations — teacher */
    public function store(StoreReservationRequest $request): JsonResponse
    {
        $user = $request->user();

        if ($user->isBlocked()) {
            return response()->json([
                'message' => 'Votre compte est bloqué. Vous ne pouvez pas soumettre de demande.',
            ], 403);
        }

        $reservation = $this->reservationService->submit($user, $request->validated());

        return response()->json([
            'message' => 'Demande soumise avec succès. Vous serez notifié(e) par email et SMS.',
            'data'    => new ReservationResource($reservation),
        ], 201);
    }

    /** PATCH /api/v1/reservations/{id}/validate — admin */
    public function validate(ValidateReservationRequest $request, string $id): JsonResponse
    {
        $reservation = $this->reservationService->findOrFail($id);
        $reservation = $this->reservationService->validate($reservation, $request->validated('material_id'));

        return response()->json([
            'message' => 'Réservation validée avec succès.',
            'data'    => new ReservationResource($reservation),
        ]);
    }

    /** PATCH /api/v1/reservations/{id}/reject — admin */
    public function reject(RejectReservationRequest $request, string $id): JsonResponse
    {
        $reservation = $this->reservationService->findOrFail($id);
        $reservation = $this->reservationService->reject($reservation, $request->validated('reason'));

        return response()->json([
            'message' => 'Réservation rejetée.',
            'data'    => new ReservationResource($reservation),
        ]);
    }

    /** GET /api/v1/reservations/available */
    public function available(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date', 'after_or_equal:today'],
            'end'   => ['required', 'date', 'after:start'],
        ]);

        $materials = $this->availabilityService->checkAvailability(
            $request->query('start'),
            $request->query('end'),
        );

        return response()->json([
            'data' => MaterialResource::collection($materials),
            'meta' => [
                'start_date' => $request->query('start'),
                'end_date'   => $request->query('end'),
                'count'      => $materials->count(),
            ],
        ]);
    }
}
