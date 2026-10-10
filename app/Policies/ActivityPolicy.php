<?php

namespace App\Policies;

use App\Models\Developer;
use Filament\Facades\Filament;
use Spatie\Activitylog\Models\Activity;

class ActivityPolicy
{
    public function viewAny(mixed $user): bool
    {
        return $this->isDeveloperAuditUser($user);
    }

    public function view(mixed $user, Activity $activity): bool
    {
        return $this->isDeveloperAuditUser($user);
    }

    public function exportActivity(mixed $user): bool
    {
        return $this->isDeveloperAuditUser($user);
    }

    public function manageExportPresets(mixed $user): bool
    {
        return $this->isDeveloperAuditUser($user);
    }

    public function viewSensitiveData(mixed $user): bool
    {
        return $this->isDeveloperAuditUser($user);
    }

    private function isDeveloperAuditUser(mixed $user): bool
    {
        return $user instanceof Developer
            && $user->is_active
            && Filament::getCurrentPanel()?->getId() === 'developer';
    }
}
