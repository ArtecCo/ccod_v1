<?php

namespace App\Providers\Filament;

use App\Enums\UserRole;
use App\Filament\Pages\ClientNotifications;
use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\Documentations\DocumentationResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\AzureSubscription;
use Filament\Support\Facades\FilamentView;
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
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->topbar(false)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->sidebarWidth('16rem')
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(\Filament\Support\Enums\Width::Full)
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                $user = auth()->user();

                $subscriptions = AzureSubscriptionResource::getEloquentQuery()
                    ->with('teams')
                    ->orderBy('display_name')
                    ->get();

                $teamGroups = $subscriptions
                    ->flatMap(fn (AzureSubscription $subscription) => $subscription->teams->map(
                        fn ($team) => [
                            'team' => $team,
                            'subscription' => $subscription,
                        ],
                    ))
                    ->groupBy(fn (array $item) => $item['team']->getKey())
                    ->sortBy(fn ($items) => $items->first()['team']->name)
                    ->map(
                        fn ($items) => NavigationGroup::make($items->first()['team']->name)
                            ->icon(Heroicon::OutlinedKey)
                            ->collapsible()
                            ->items(
                                $items
                                    ->sortBy(fn (array $item) => $item['subscription']->display_name)
                                    ->map(
                                        fn (array $item) => NavigationItem::make($item['subscription']->display_name)
                                            ->icon(Heroicon::OutlinedCloud)
                                            ->url(AzureSubscriptionResource::getUrl('view', [
                                                'record' => $item['subscription'],
                                            ]))
                                            ->isActiveWhen(fn (): bool => request()->routeIs(
                                                'filament.admin.resources.azure-subscriptions.view',
                                            ) && request()->route('record') === $item['subscription']->getRouteKey()),
                                    )
                                    ->all(),
                            ),
                    )
                    ->values()
                    ->all();

                $documentationItem = NavigationItem::make('Documentation')
                    ->icon(Heroicon::OutlinedDocumentText)
                    ->url(DocumentationResource::getUrl())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.documentations.*'));

                $notificationItem = NavigationItem::make('Notifications')
                    ->icon(Heroicon::OutlinedBell)
                    ->url(ClientNotifications::getUrl())
                    ->badge(function () use ($user): ?string {
                        if (! $user) {
                            return null;
                        }

                        $count = $user->unreadNotifications()->count();

                        return $count > 0 ? (string) $count : null;
                    })
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.notifications'));

                if ($user?->role === UserRole::GlobalOwner) {
                    $managementItems = [
                        NavigationItem::make('Users')
                            ->icon(Heroicon::OutlinedUsers)
                            ->url(UserResource::getUrl()),
                        NavigationItem::make('Teams')
                            ->icon(Heroicon::OutlinedUserGroup)
                            ->url(TeamResource::getUrl()),
                        NavigationItem::make('Subscriptions')
                            ->icon(Heroicon::OutlinedCloud)
                            ->url(AzureSubscriptionResource::getUrl()),
                        NavigationItem::make('Access Requests')
                            ->icon(Heroicon::OutlinedKey)
                            ->url(AccessRequestResource::getUrl())
                            ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.access-requests.*')),
                    ];
                } else {
                    $managementItems = [
                        NavigationItem::make('Access Requests')
                            ->icon(Heroicon::OutlinedKey)
                            ->url(AccessRequestResource::getUrl())
                            ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.access-requests.*')),
                    ];
                }

                return $builder->groups([
                    NavigationGroup::make()
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon(Heroicon::OutlinedHome)
                                ->url(Dashboard::getUrl())
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard')),
                            $notificationItem,
                            $documentationItem,
                        ]),
                    ...$teamGroups,
                    NavigationGroup::make('Administration')
                        ->icon(Heroicon::OutlinedCog6Tooth)
                        ->items($managementItems),
                ]);
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
                ClientNotifications::class,
                \App\Filament\Pages\Profile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->sidebarWidth('18rem')
            ->sidebarCollapsibleOnDesktop()
            ->bootUsing(function () {
                FilamentView::registerRenderHook(
                    'panels::styles.after',
                    fn (): string => new HtmlString('                       <style>
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

html { font-size: 13px !important; }
.fi-section, .fi-ta-ctn, .fi-wi-widget, .fi-card, .fi-modal-window {
    padding: 0.6rem !important;
    border-radius: 0.375rem !important;
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
            width 280ms cubic-bezier(0.22, 1, 0.36, 1),
            transform 280ms cubic-bezier(0.22, 1, 0.36, 1),
            box-shadow 220ms ease !important;
    }

    /* Let the main area visually follow the sidebar movement. */
    .fi-main-ctn {
        transition:
            transform 280ms cubic-bezier(0.22, 1, 0.36, 1),
            opacity 180ms ease !important;
    }

    /* Small coordinated movement of navigation controls. */
    .fi-sidebar-item-button,
    .fi-sidebar-group-btn {
        transition:
            background-color 160ms ease,
            color 160ms ease,
            transform 180ms cubic-bezier(0.22, 1, 0.36, 1) !important;
    }

    .fi-sidebar-item-button:hover {
        transform: translateX(2px);
    }

    .fi-sidebar-group-btn:hover {
        transform: translateX(1px);
    }

    /* Smooth rotation of the group collapse indicator. */
    .fi-sidebar-group-btn svg {
        transition: transform 180ms ease !important;
    }

    .fi-sidebar-group-btn[aria-expanded="false"] svg {
        transform: rotate(-90deg);
    }
}
                        </style>                   ')
                );
            });
    }
}
