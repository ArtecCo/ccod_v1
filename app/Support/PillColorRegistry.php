<?php

namespace App\Support;

use App\Enums\AccessRequestStatus;
use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;

class PillColorRegistry
{
    /**
     * Every pill used by the application should be registered here.
     * Adding a new entry automatically makes it available on the developer Colors page.
     *
     * @return array<string, array{label: string, default: string}>
     */
    public static function all(): array
    {
        $pills = [];

        foreach (AccessRequestStatus::cases() as $status) {
            $pills[$status->value] = [
                'label' => $status->label(),
                'default' => match ($status) {
                    AccessRequestStatus::Pending => 'warning',
                    AccessRequestStatus::Approved => 'success',
                    AccessRequestStatus::Rejected => 'danger',
                    AccessRequestStatus::Revoked => 'gray',
                },
            ];
        }

        foreach (AccessRequestTargetType::cases() as $type) {
            $pills[$type->value] = [
                'label' => $type->label(),
                'default' => 'gray',
            ];
        }

        foreach (UserRole::cases() as $role) {
            $pills[$role->value] = [
                'label' => $role->label(),
                'default' => 'gray',
            ];
        }

        foreach ([
            'healthy' => ['label' => 'Healthy', 'default' => 'success'],
            'degraded' => ['label' => 'Degraded', 'default' => 'warning'],
            'critical' => ['label' => 'Critical', 'default' => 'danger'],
            'unknown' => ['label' => 'Unknown', 'default' => 'gray'],
        ] as $key => $definition) {
            $pills[$key] = $definition;
        }

        return $pills;
    }
}
