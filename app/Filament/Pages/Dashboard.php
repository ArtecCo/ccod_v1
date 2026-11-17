<?php

namespace App\Filament\Pages;

use App\Livewire\Dashboard\ConsolidatedDashboard;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Schema;

class Dashboard extends BaseDashboard
{
    public function mount(): void
    {
        abort_unless(auth()->guard('web')->user() instanceof User, 403);

        if (! session()->has('auth_session_started_at')) {
            session()->put('auth_session_started_at', now()->timestamp);
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Livewire::make(ConsolidatedDashboard::class)->columnSpanFull(),
        ]);
    }

    public function getWidgets(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('myProfile')
                ->label('My Profile')
                ->icon('heroicon-o-user-circle')
                ->url(fn (): string => Profile::getUrl())
                ->button(),
            Action::make('notifications')
                ->label('Notifications')
                ->icon('heroicon-o-bell')
                ->url(fn (): string => ClientNotifications::getUrl())
                ->button(),
        ];
    }
}
