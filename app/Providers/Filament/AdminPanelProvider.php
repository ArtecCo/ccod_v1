<?php

namespace App\Providers\Filament;

use Filament\Support\Facades\FilamentView;
use Illuminate\Support\HtmlString;

use App\Enums\UserRole;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\Teams\TeamResource;
use App\Filament\Resources\Users\UserResource;
use App\Models\AzureSubscription;
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
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
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

                $subscriptions = AzureSubscription::query()
                    ->with('teams')
                    ->when(
                        $user && ! $user->isGlobal(),
                        fn ($query) => $query->whereHas(
                            'teams.users',
                            fn ($teamUsers) => $teamUsers->whereKey($user->getKey()),
                        ),
                    )
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
                    ];
                } else {
                    $managementItems = [];
                }

                return $builder->groups([
                    NavigationGroup::make()
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon(Heroicon::OutlinedHome)
                                ->url(Dashboard::getUrl())
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard')),
                        ]),
                    ...$teamGroups,
                    ...($managementItems === [] ? [] : [
                        NavigationGroup::make('Administration')
                            ->icon(Heroicon::OutlinedCog6Tooth)
                            ->items($managementItems),
                    ]),
                ]);
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
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
                    fn (): string => new HtmlString('\n                        <style>\n                        /* 1. Hide the scrollbar for the sidebar container across all modern browsers */\n/* 1. Base Setup: Apply to Light Mode by default */\n/* Disable scrollbars globally on all sidebar inner structural scroll layers */\n.fi-sidebar-nav,\n.fi-sidebar-nav-groups,\n.fi-sidebar-group,\naside.fi-sidebar nav,\naside.fi-sidebar div {\n    scrollbar-width: none !important;\n    -ms-overflow-style: none !important;\n}\n\n.fi-sidebar-nav::-webkit-scrollbar,\n.fi-sidebar-nav-groups::-webkit-scrollbar,\n.fi-sidebar-group::-webkit-scrollbar,\naside.fi-sidebar nav::-webkit-scrollbar,\naside.fi-sidebar div::-webkit-scrollbar {\n    display: none !important;\n    width: 0px !important;\n    height: 0px !important;\n    background: transparent !important;\n}\n\n/* Keep the sidebar divider visible. */\n.fi-sidebar {\n    box-shadow: inset -1px 0 0 rgba(0, 0, 0, 0.18) !important;\n}\n\n.dark .fi-sidebar {\n    box-shadow: inset -1px 0 0 rgba(255, 255, 255, 0.18) !important;\n}\n\n.dark .fi-sidebar::after {\n    background-color: rgba(255, 255, 255, 0.12) !important;\n}\n\n/* Keep grouped team subscriptions as independent items, without Filament\\'s connecting guide. */\n.fi-sidebar-item-grouped-border {\n    display: none !important;\n}\n\n.fi-sidebar-group .fi-sidebar-item-icon {\n    display: block !important;\n}\n\nhtml { font-size: 13px !important; }\n.fi-section, .fi-ta-ctn, .fi-wi-widget, .fi-card, .fi-modal-window {\n    padding: 0.6rem !important;\n    border-radius: 0.375rem !important;\n}\n.fi-section-header, .fi-ta-header {\n    padding-bottom: 0.35rem !important;\n    margin-bottom: 0.35rem !important;\n}\n.fi-sidebar-item-button {\n    padding-top: 0.2rem !important;\n    padding-bottom: 0.2rem !important;\n    margin-top: 0.05rem !important;\n    margin-bottom: 0.05rem !important;\n}\n.fi-sidebar-group-label {\n    padding-top: 0.2rem !important;\n    padding-bottom: 0.2rem !important;\n    margin-bottom: 0px !important;\n}\n.fi-sidebar-nav-groups { gap: 1rem !important; }\n.grid { gap: 0.6rem !important; }\n.fi-fo-field-wrp { margin-bottom: 0.4rem !important; }\n.fi-ta-table th { padding-top: 1rem !important; padding-bottom: 1rem !important; }\n.fi-ta-table td { padding-top: 0.2rem !important; padding-bottom: 0.2rem !important; }\n\n/* Slightly larger table and infolist text on larger screens only. */\n@media (min-width: 1280px) {\n    .fi-ta-text-item,\n    .fi-in-text {\n        font-size: 0.95rem !important;\n    }\n}\n                        </style>\n                    ')\n                );\n            });\n
    }
}
