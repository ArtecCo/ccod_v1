<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureSubscription;
use App\Models\SubscriptionWebUrl;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class ConsolidatedWebUrlStats extends BaseWidget
{
    protected static bool $isLazy = true;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 4;

    protected function getStats(): array
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $authorization = app(AccessAuthorizationService::class);
        $subscriptionIds = $authorization->accessibleSubscriptionQuery($user)
            ->get()
            ->filter(fn (AzureSubscription $subscription): bool =>
                $authorization->hasPermission($user, 'urls.view', $subscription)
            )
            ->pluck('subscription_id')
            ->all();

        $allUrls = SubscriptionWebUrl::query()
            ->whereIn('subscription_id', $subscriptionIds);
        $activeUrls = (clone $allUrls)->where('is_active', true);

        $total = (clone $allUrls)->count();
        $live = (clone $activeUrls)->where('is_live', true)->count();
        $down = (clone $activeUrls)->where('is_live', false)->count();
        $sslExpiring = (clone $activeUrls)
            ->whereNotNull('ssl_expires_at')
            ->whereBetween('ssl_expires_at', [now(), now()->addDays(15)])
            ->count();

        $card = fn (string $filter): array => [
            'role' => 'button',
            'tabindex' => '0',
            'class' => 'cursor-pointer',
            'x-on:click' => "\$dispatch('filter-web-urls', { filter: '{$filter}' })",
            'x-on:keydown.enter' => "\$dispatch('filter-web-urls', { filter: '{$filter}' })",
        ];

        return [
            Stat::make('Total URLs', new HtmlString('<span style="color: #2563eb;">' . e(number_format($total)) . '</span>'))
                ->icon('heroicon-o-globe-alt')
                ->extraAttributes($card('all')),
            Stat::make('Total Live', new HtmlString('<span style="color: #16a34a;">' . e(number_format($live)) . '</span>'))
                ->icon('heroicon-o-check-circle')
                ->extraAttributes($card('live')),
            Stat::make('Total Down', new HtmlString('<span style="color: #dc2626;">' . e(number_format($down)) . '</span>'))
                ->icon('heroicon-o-x-circle')
                ->extraAttributes($card('down')),
            Stat::make('SSL Expiring in 15 Days', new HtmlString('<span style="color: #d97706;">' . e(number_format($sslExpiring)) . '</span>'))
                ->icon('heroicon-o-shield-exclamation')
                ->extraAttributes($card('ssl_expiring')),
        ];
    }
}
