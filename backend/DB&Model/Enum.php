<?php
namespace App\Enums;

enum MaterialStatus: string
{
    case Available = 'available';
    case InUse = 'in_use';
    case Broken = 'broken';
    case Maintenance = 'maintenance';
}
?>