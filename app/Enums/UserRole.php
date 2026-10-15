<?php

namespace App\Enums;

enum UserRole: string
{
    case Viewer = 'viewer';
    case Operator = 'operator';
    case Customer = 'customer';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Viewer => 'Viewer', self::Operator => 'Operator', self::Customer => 'Customer', self::Admin => 'Admin', self::SuperAdmin => 'Super Admin',
        };
    }
}
