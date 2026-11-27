<?php

declare(strict_types=1);

namespace App\Filament\Pages\Widgets\Concerns;

use App\Models\AzureSubscription;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Illuminate\Support\Collection;

trait HasConsolidatedCostScope
{
    /**
     * Resolve only subscriptions the current user may view costs for, then
     * apply the consolidated dashboard's selected project/business-unit scope.
     */
    protected function authorizedCostSubscriptions(string $scopeFilter): Collection
    {
        $user = auth()->guard('web')->user();
        abort_unless($user instanceof User, 403);

        $authorization = app(AccessAuthorizationService::class);
        $subscriptions = $authorization->accessibleSubscriptionQuery($user)->get()
            ->filter(fn (AzureSubscription $subscription): bool => $authorization->hasPermission($user, 'cost.view', $subscription));

        if (preg_match('/^project:(\d+)$/', $scopeFilter, $matches) === 1) {
            $subscriptions = $subscriptions->filter(
                fn (AzureSubscription $subscription): bool => $subscription->projects()->whereKey((int) $matches[1])->exists()
            );
        } elseif (preg_match('/^business_unit:(\d+)$/', $scopeFilter, $matches) === 1) {
            $subscriptions = $subscriptions->filter(
                fn (AzureSubscription $subscription): bool => $subscription->projects()->where('business_unit_id', (int) $matches[1])->exists()
            );
        } elseif ($scopeFilter !== 'all') {
            abort(403);
        }

        return $subscriptions->values();
    }
}
