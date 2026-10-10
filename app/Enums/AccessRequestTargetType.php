<?php

namespace App\Enums;

enum AccessRequestTargetType: string
{
    case Team = 'team';
    case Subscription = 'subscription';

    public function label(): string
    {
        return match ($this) {
            self::Team => 'Team',
            self::Subscription => 'Subscription',
        };
    }
}
