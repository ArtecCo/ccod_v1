<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function mount(): void
    {
        $user = auth()->guard('web')->user();

        abort_unless(
            $user instanceof User && app(AccessAuthorizationService::class)->hasPermission($user, 'dashboard.view'),
            403,
        );
    }
}
