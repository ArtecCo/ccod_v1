<?php

namespace App\Providers\Filament;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
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
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
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
                            ->icon(Heroicon::OutlinedUserGroup)
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

                return $builder->groups([
                    NavigationGroup::make()
                        ->items([
                            NavigationItem::make('Dashboard')
                                ->icon(Heroicon::OutlinedHome)
                                ->url(Dashboard::getUrl())
                                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.pages.dashboard')),
                        ]),
                    ...$teamGroups,
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
            ]);
    }
}
