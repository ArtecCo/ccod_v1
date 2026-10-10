<?php

namespace App\Providers\Filament;

use App\Filament\Pages\LogsControl;
use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
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
                'primary' => Color::Amber,
            ])
            ->sidebarWidth('18rem')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    NavigationGroup::make()
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon('heroicon-o-home')
                                ->url(Dashboard::getUrl())
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.developer.pages.dashboard')),
                        ]),
                    NavigationGroup::make('Logging')
                        ->items([
                            NavigationItem::make('Audit Logs')
                                ->icon('heroicon-o-shield-check')
                                ->url(AuditLogResource::getUrl())
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.developer.resources.audit-logs.*')),
                            NavigationItem::make('Logs Control')
                                ->icon('heroicon-o-adjustments-horizontal')
                                ->url(LogsControl::getUrl())
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.developer.pages.logs-control')),
                        ]),
                ]);
            })
            ->resources([
                AuditLogResource::class,
            ])
            ->pages([
                Dashboard::class,
                LogsControl::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->bootUsing(function (): void {
                FilamentView::registerRenderHook(
                    'panels::styles.after',
                    fn (): HtmlString => new HtmlString('...same CSS...')
                );
            });
    }
}
