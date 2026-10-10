<?php

namespace App\Providers\Filament;

use App\Http\Middleware\AuditRequestMiddleware;
use App\Models\Developer;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DeveloperPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('developer')
            ->path('developer')
            ->authGuard('developers')
            ->login()
            ->topbar(false)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->items([
                    NavigationItem::make('Dashboard')
                        ->icon('heroicon-o-home')
                        ->url(Dashboard::getUrl())
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.developer.pages.dashboard')),
                    NavigationItem::make('Audit Log')
                        ->icon('heroicon-o-shield-check')
                        ->url(config('filament-logger.activity_resource')::getUrl())
                        ->isActiveWhen(fn (): bool => request()->routeIs('filament.developer.resources.*')),
                ]);
            })
            ->resources([
                config('filament-logger.activity_resource'),
            ])
            ->pages([
                Dashboard::class,
            ])
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                AuditRequestMiddleware::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
