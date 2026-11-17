<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets;

use App\Models\AzureAlert;
use App\Models\AzureSubscription;
use App\Models\BillingResource;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ConsolidatedDashboardStats extends BaseWidget
{
    public string $filter = 'all';

    protected static bool $isLazy = true;

    protected ?string $pollingInterval = null;

    protected int | array | null $columns = 4;

    protected function getStats(): array
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $subscriptions = app(AccessAuthorizationService::class)
            ->accessibleSubscriptionQuery($user);

        if (preg_match('/^project:(\d+)$/', $this->filter, $matches) === 1) {
            $subscriptions->whereHas('projects', fn ($query) => $query->whereKey((int) $matches[1]));
        } elseif (preg_match('/^business_unit:(\d+)$/', $this->filter, $matches) === 1) {
            $subscriptions->whereHas('projects', fn ($query) => $query->where('business_unit_id', (int) $matches[1]));
        } elseif ($this->filter !== 'all') {
            abort(403);
        }

        $subscriptionModels = $subscriptions->get();
        $subscriptionIds = $subscriptionModels->pluck('subscription_id')->all();
        $projectCount = $subscriptionModels->flatMap(fn ($subscription) => $subscription->projects)->unique('id')->count();

        $authorization = app(AccessAuthorizationService::class);
        $alertIds = $subscriptionModels
            ->filter(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, 'alerts.view', $subscription))
            ->pluck('subscription_id')->all();
        $resourceIds = $subscriptionModels
            ->filter(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, 'resources.view', $subscription))
            ->pluck('subscription_id')->all();

        $alertCount = AzureAlert::query()->whereIn('subscription_id', $alertIds)->count();
        $resourceCount = BillingResource::query()->whereIn('subscription_id', $resourceIds)->count();

        return [
            Stat::make('Accessible Subscriptions', number_format(count($subscriptionIds)))
                ->description('Within your access scope')
                ->icon('heroicon-o-cloud')
                ->color('primary'),
            Stat::make('Projects in Scope', number_format($projectCount))
                ->description('Matching selected filter')
                ->icon('heroicon-o-rectangle-stack')
                ->color('info'),
            Stat::make('Alerts', number_format($alertCount))
                ->description('Across permitted subscriptions')
                ->icon('heroicon-o-bell-alert')
                ->color($alertCount > 0 ? 'warning' : 'success'),
            Stat::make('Resources', number_format($resourceCount))
                ->description('Discovered resources')
                ->icon('heroicon-o-cube')
                ->color('gray'),
        ];
    }
}
