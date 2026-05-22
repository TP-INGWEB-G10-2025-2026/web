<?php


namespace App\Enums;

enum ReservationStatus: string
{
    case Pending   = 'pending';
    case Validated = 'validated';
    case Rejected  = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Pending   => 'En attente',
            self::Validated => 'Validée',
            self::Rejected  => 'Rejetée',
            self::Cancelled => 'Annulée',
        };
    }
}
