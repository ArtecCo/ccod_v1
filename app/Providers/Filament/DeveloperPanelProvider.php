<?php

namespace App\Providers\Filament;

use App\Filament\Pages\DatabaseTablePage;
use App\Filament\Pages\LogsControl;
use App\Filament\Pages\MaintenanceMode;
use App\Filament\Pages\PillColors;
use App\Filament\Pages\SendNotification;
use App\Filament\Pages\Styling;
use App\Filament\Pages\TabAccess;
use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Filament\Resources\AuditLogs\AuditLogResource;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\FailedJobs\FailedJobResource;
use App\Filament\Resources\NotificationRules\NotificationRuleResource;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\QueueJobs\QueueJobResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\DeveloperOverview;
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
                NavigationGroup::make('Administration')->icon('heroicon-o-cog-6-tooth'),
                NavigationGroup::make('Notifications')->icon('heroicon-o-bell'),
                NavigationGroup::make('Operations')->icon('heroicon-o-bolt'),
                NavigationGroup::make('Database')->icon('heroicon-o-circle-stack'),
                NavigationGroup::make('Logging')->icon('heroicon-o-clipboard-document-list'),
                NavigationGroup::make('Styling')->icon('heroicon-o-paint-brush'),
                NavigationGroup::make('Maintenance')->icon('heroicon-o-wrench-screwdriver'),
            ])
            ->resources([
                AccessRequestResource::class,
                AzureSubscriptionResource::class,
                ProjectResource::class,
                RoleResource::class,
                TeamResource::class,
                UserResource::class,
                QueueJobResource::class,
                FailedJobResource::class,
                AuditLogResource::class,
                NotificationRuleResource::class,
            ])
            ->pages([
                Dashboard::class,
                Styling::class,
                TabAccess::class,
                SendNotification::class,
                PillColors::class,
                LogsControl::class,
                DatabaseTablePage::class,
                MaintenanceMode::class,
            ])
            ->widgets([
                DeveloperOverview::class,
            ])
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
                    fn (): HtmlString => new HtmlString('<style>
                        /* 1. Hide the scrollbar for the sidebar container across all modern browsers */
/* 1. Base Setup: Apply to Light Mode by default */
/* Disable scrollbars globally on all sidebar inner structural scroll layers */
.fi-sidebar-nav,
.fi-sidebar-nav-groups,
.fi-sidebar-group,
aside.fi-sidebar nav,
aside.fi-sidebar div {
    scrollbar-width: none !important;
    -ms-overflow-style: none !important;
}

.fi-sidebar-nav::-webkit-scrollbar,
.fi-sidebar-nav-groups::-webkit-scrollbar,
.fi-sidebar-group::-webkit-scrollbar,
aside.fi-sidebar nav::-webkit-scrollbar,
aside.fi-sidebar div::-webkit-scrollbar {
    display: none !important;
    width: 0px !important;
    height: 0px !important;
    background: transparent !important;
}

/* Keep the sidebar divider visible. */
.fi-sidebar {
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, 0.18) !important;
}

.dark .fi-sidebar {
    box-shadow: inset -1px 0 0 rgba(255, 255, 255, 0.18) !important;
}

.dark .fi-sidebar::after {
    background-color: rgba(255, 255, 255, 0.12) !important;
}

/* Keep grouped team subscriptions as independent items, without Filament\'s connecting guide. */
.fi-sidebar-item-grouped-border {
    display: none !important;
}

.fi-sidebar-group .fi-sidebar-item-icon {
    display: block !important;
}

.fi-badge{
                        font-size: 0.81rem !important;
                        text-transform: capitalize !important;
                        }
                        
html { font-size: 13px !important; }
.fi-section, .fi-ta-ctn, .fi-wi-widget, .fi-card, .fi-modal-window {
    padding: 0.6rem !important;
    border-radius: var(--ccod-corner-radius, 0.375rem) !important;
}
.fi-section-header, .fi-ta-header {
    padding-bottom: 0.35rem !important;
    margin-bottom: 0.35rem !important;
}
.fi-sidebar-item-button {
    padding-top: 0.2rem !important;
    padding-bottom: 0.2rem !important;
    margin-top: 0.05rem !important;
    margin-bottom: 0.05rem !important;
}
.fi-sidebar-group-label {
    padding-top: 0.2rem !important;
    padding-bottom: 0.2rem !important;
    margin-bottom: 0px !important;
}
.fi-sidebar-nav-groups { gap: 1rem !important; }
.grid { gap: 0.6rem !important; }
.fi-fo-field-wrp { margin-bottom: 0.4rem !important; }
.fi-ta-table th { padding-top: 1rem !important; padding-bottom: 1rem !important; }
.fi-ta-table td { padding-top: 0.2rem !important; padding-bottom: 0.2rem !important; }

/* Slightly larger table and infolist text on larger screens only. */
@media (min-width: 1280px) {
    .fi-ta-text-item,
    .fi-in-text {
        font-size: 0.95rem !important;
    }
}

/* Smooth desktop sidebar collapse / expansion */
@media (min-width: 1024px) {
    .fi-sidebar {
        transition:
            width 340ms cubic-bezier(0.22, 1, 0.36, 1),
            transform 340ms cubic-bezier(0.22, 1, 0.36, 1),
            box-shadow 280ms ease !important;
    }

    /* Let the main area visually follow the sidebar movement. */
    .fi-main-ctn {
        transition:
            transform 340ms cubic-bezier(0.22, 1, 0.36, 1),
            opacity 220ms ease !important;
    }

    /* Small coordinated movement of navigation controls. */
    .fi-sidebar-item-button,
    .fi-sidebar-group-btn {
        transition:
            background-color 180ms ease,
            color 180ms ease,
            transform 220ms cubic-bezier(0.22, 1, 0.36, 1) !important;
    }

    .fi-sidebar-item-button:hover {
        transform: translateX(2px);
    }

    .fi-sidebar-group-btn:hover {
        transform: translateX(1px);
    }

    /* Smooth rotation of the group collapse indicator. */
    .fi-sidebar-group-btn svg {
        transition: transform 220ms ease !important;
    }

    .fi-sidebar-group-btn[aria-expanded="false"] svg {
        transform: rotate(-90deg);
    }
}
                        </style>')
                );

                FilamentView::registerRenderHook(
                    'panels::body.end',
                    fn (): string => view('filament.partials.sidebar-collapse-all')->render(),
                );
            })
            ->viteTheme('resources/css/filament/developer/theme.css');
    }
}
