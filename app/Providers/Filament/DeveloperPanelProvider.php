<?php

namespace App\Providers\Filament;

use App\Filament\Pages\DatabaseTablePage;
use App\Filament\Pages\LogsControl;
use App\Filament\Pages\PillColors;
use App\Filament\Pages\SendNotification;
use App\Filament\Pages\Styling;
use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use App\Filament\Resources\Users\UserResource;
use App\Support\FilamentStyling;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomaszBoloz\LaravelUpdater\Filament\UpdaterPlugin;

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
            ->brandName(fn (): string => FilamentStyling::brandName())
            ->brandLogo(fn (): ?string => FilamentStyling::logoLight())
            ->darkModeBrandLogo(fn (): ?string => FilamentStyling::logoDark())
            ->favicon(fn (): ?string => FilamentStyling::favicon())
            ->colors(FilamentStyling::colors())
            ->sidebarWidth('18rem')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->plugin(UpdaterPlugin::make()->navigationGroup('Maintenance')->navigationSort(10))
            ->navigationGroups([
                NavigationGroup::make('Styling'),
                NavigationGroup::make('Notifications'),
                NavigationGroup::make('Logging'),
                NavigationGroup::make('Administration'),
                NavigationGroup::make('Database'),
                NavigationGroup::make('Maintenance'),
            ])
            ->resources([
                AuditLogResource::class,
                NotificationRuleResource::class,
                AccessRequestResource::class,
                UserResource::class,
            ])
            ->pages([
                Dashboard::class,
                Styling::class,
                SendNotification::class,
                PillColors::class,
                LogsControl::class,
                DatabaseTablePage::class,
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
            ->authMiddleware([Authenticate::class])
            ->bootUsing(function (): void {
                FilamentView::registerRenderHook(
                    'panels::styles.after',
                    fn (): HtmlString => new HtmlString(
                        '<style>:root{--ccod-corner-radius:' . e(FilamentStyling::cornerRadius()) . ';}</style>'
                    ),
                );
            })
            ->viteTheme('resources/css/filament/developer/theme.css');
    }
}
