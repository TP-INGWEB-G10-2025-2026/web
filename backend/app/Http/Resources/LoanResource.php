<?php
// app/Http/Resources/LoanResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\Material\MaterialResource;
use App\Http\Resources\Reservation\ReservationResource;


class LoanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'loan_date'            => $this->loan_date?->format('Y-m-d'),
            'expected_return_date' => $this->expected_return_date?->format('Y-m-d'),
            'actual_return_date'   => $this->actual_return_date?->format('Y-m-d'),
            'return_status'        => $this->return_status?->value,
            'return_status_label'  => $this->return_status?->label(),
            'notes'                => $this->notes,
            'is_returned'          => $this->isReturned(),
            'is_overdue'           => $this->isOverdue(),
            'is_ongoing'           => $this->isOngoing(),
            'overdue_days'         => $this->overdueDays(),
            'user'                 => new UserResource($this->whenLoaded('user')),
            'material'             => new MaterialResource($this->whenLoaded('material')),
            'reservation'          => new ReservationResource($this->whenLoaded('reservation')),
            'created_at'           => $this->created_at?->toISOString(),
        ];
    }
}
