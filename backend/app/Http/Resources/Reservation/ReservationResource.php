<?php


namespace App\Http\Resources\Reservation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\Material\MaterialResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'status'           => $this->status->value,
            'status_label'     => $this->status->label(),
            'start_date'       => $this->start_date?->format('Y-m-d'),
            'end_date'         => $this->end_date?->format('Y-m-d'),
            'rejection_reason' => $this->rejection_reason,
            'user'             => new UserResource($this->whenLoaded('user')),
            'material'         => new MaterialResource($this->whenLoaded('material')),
            'created_at'       => $this->created_at?->toISOString(),
        ];
    }
}
