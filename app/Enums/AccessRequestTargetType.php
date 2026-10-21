<?php

namespace App\Enums;

enum AccessRequestTargetType: string
{
    case UserGroup = 'user_group';

    public function label(): string
    {
        return 'User Group';
    }
}
