<?php

namespace App\Providers;

use App\Filament\Pages\Profile;
use Filament\Actions\Action;
use Filament\Panel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\ServiceProvider;

class ProfileMenuServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            if ($panel->getId() !== 'admin') {
                return;
            }

            $panel->userMenuItems([
                'profile' => Action::make('profile')
                    ->label(fn (): string => auth()->user()?->name ?? 'Profile')
                    ->icon(Heroicon::OutlinedUserCircle)
                    ->url(fn (): string => Profile::getUrl())
                    ->sort(1),
            ]);
        });
    }
}
