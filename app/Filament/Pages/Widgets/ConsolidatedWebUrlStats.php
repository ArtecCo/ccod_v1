<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureSubscription;
use App\Models\SubscriptionWebUrl;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

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

        $urls = SubscriptionWebUrl::query()
            ->whereIn('subscription_id', $subscriptionIds)
            ->where('is_active', true);

        $total = (clone $urls)->count();
        $live = (clone $urls)->where('is_live', true)->count();
        $down = (clone $urls)->where('is_live', false)->count();
        $sslExpiring = (clone $urls)
            ->whereNotNull('ssl_expires_at')
            ->whereBetween('ssl_expires_at', [now(), now()->addDays(15)])
            ->count();

        return [
            Stat::make('Total URLs', number_format($total))
                ->icon('heroicon-o-globe-alt')
                ->color('primary'),
            Stat::make('Total Live', number_format($live))
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Total Down', number_format($down))
                ->icon('heroicon-o-x-circle')
                ->color($down > 0 ? 'danger' : 'success'),
            Stat::make('SSL Expiring in 15 Days', number_format($sslExpiring))
                ->icon('heroicon-o-shield-exclamation')
                ->color($sslExpiring > 0 ? 'warning' : 'success'),
        ];
    }
}
