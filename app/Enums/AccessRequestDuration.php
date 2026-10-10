<?php

namespace App\Enums;

enum AccessRequestDuration: string
{
    case Permanent = 'permanent';
    case TimeBound = 'time_bound';

    public function label(): string
    {
        return match ($this) {
            self::Permanent => 'Permanent',
            self::TimeBound => 'Time-bound',
        };
    }
}
