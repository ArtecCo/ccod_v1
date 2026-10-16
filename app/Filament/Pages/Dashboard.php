<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function mount(): void
    {
        abort_unless(auth()->guard('web')->user() instanceof User, 403);
    }
}
