<?php

namespace App\Enums;

enum MaterialStatus: string
{
    case Available   = 'available';
    case InUse       = 'in_use';
    case Broken      = 'broken';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match($this) {
            self::Available   => 'Disponible',
            self::InUse       => 'En utilisation',
            self::Broken      => 'En panne',
            self::Maintenance => 'En maintenance',
        };
    }
}
