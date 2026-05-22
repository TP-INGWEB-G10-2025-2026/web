<?php


namespace App\Enums;

enum ReturnStatus: string
{
    case Good    = 'good';
    case Damaged = 'damaged';
    case Lost    = 'lost';

    public function label(): string
    {
        return match($this) {
            self::Good    => 'Bon état',
            self::Damaged => 'Endommagé',
            self::Lost    => 'Perdu',
        };
    }
}
