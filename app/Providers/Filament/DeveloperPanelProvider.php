<?php

namespace App\Providers\Filament;

use App\Filament\Pages\LogsControl;
use App\Filament\Pages\SendNotification;
use App\Filament\Resources\AuditLogs\AuditLogResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
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
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarWidth('18rem')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->plugin(
                UpdaterPlugin::make()
                    ->navigationGroup('Maintenance')
                    ->navigationSort(10),
            )
            ->navigationGroups([
                NavigationGroup::make('Notifications'),
                NavigationGroup::make('Logging'),
                NavigationGroup::make('Maintenance'),
            ])
            ->resources([
                AuditLogResource::class,
            ])
            ->pages([
                Dashboard::class,
                SendNotification::class,
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
                    fn (): HtmlString => new HtmlString('
                        <style>
                            .fi-sidebar-nav,.fi-sidebar-nav-groups,.fi-sidebar-group,aside.fi-sidebar nav,aside.fi-sidebar div{scrollbar-width:none!important;-ms-overflow-style:none!important}
                            .fi-sidebar-nav::-webkit-scrollbar,.fi-sidebar-nav-groups::-webkit-scrollbar,.fi-sidebar-group::-webkit-scrollbar,aside.fi-sidebar nav::-webkit-scrollbar,aside.fi-sidebar div::-webkit-scrollbar{display:none!important;width:0!important;height:0!important}
                            .fi-sidebar{box-shadow:inset -1px 0 0 rgba(0,0,0,.18)!important}.dark .fi-sidebar{box-shadow:inset -1px 0 0 rgba(255,255,255,.18)!important}
                            .fi-sidebar-item-grouped-border{display:none!important}.fi-sidebar-group .fi-sidebar-item-icon{display:block!important}
                            html{font-size:13px!important}.fi-section,.fi-ta-ctn,.fi-wi-widget,.fi-card,.fi-modal-window{padding:.6rem!important;border-radius:.375rem!important}
                            .fi-section-header,.fi-ta-header{padding-bottom:.35rem!important;margin-bottom:.35rem!important}
                            .fi-sidebar-item-button{padding-top:.2rem!important;padding-bottom:.2rem!important;margin-top:.05rem!important;margin-bottom:.05rem!important}
                            .fi-sidebar-group-label{padding-top:.2rem!important;padding-bottom:.2rem!important;margin-bottom:0!important}
                            .fi-sidebar-nav-groups{gap:1rem!important}.grid{gap:.6rem!important}.fi-fo-field-wrp{margin-bottom:.4rem!important}
                            .fi-ta-table th{padding-top:1rem!important;padding-bottom:1rem!important}.fi-ta-table td{padding-top:.2rem!important;padding-bottom:.2rem!important}
                            @media (min-width:1280px){.fi-ta-text-item,.fi-in-text{font-size:.95rem!important}}
                            @media (min-width:1024px){.fi-sidebar{transition:width 280ms cubic-bezier(.22,1,.36,1),transform 280ms cubic-bezier(.22,1,.36,1),box-shadow 220ms ease!important}.fi-main-ctn{transition:transform 280ms cubic-bezier(.22,1,.36,1),opacity 180ms ease!important}.fi-sidebar-item-button,.fi-sidebar-group-btn{transition:background-color 160ms ease,color 160ms ease,transform 180ms cubic-bezier(.22,1,.36,1)!important}.fi-sidebar-item-button:hover{transform:translateX(2px)}.fi-sidebar-group-btn:hover{transform:translateX(1px)}}
                        </style>
                    ')
                );
            });
    }
}
