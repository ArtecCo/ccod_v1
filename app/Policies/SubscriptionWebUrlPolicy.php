<?php

namespace App\Policies;

use App\Models\AzureSubscription;
use App\Models\SubscriptionWebUrl;
use App\Models\User;
use App\Services\AccessAuthorizationService;

class SubscriptionWebUrlPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active === true;
    }

    public function view(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->hasSubscriptionPermission($user, 'urls.view', $webUrl->subscription_id);
    }

    public function create(User $user, AzureSubscription $subscription): bool
    {
        return $this->hasSubscriptionPermission($user, 'urls.create', $subscription->subscription_id);
    }

    public function update(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->hasSubscriptionPermission($user, 'urls.edit', $webUrl->subscription_id);
    }

    public function delete(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->hasSubscriptionPermission($user, 'urls.delete', $webUrl->subscription_id);
    }

    public function restore(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->update($user, $webUrl);
    }

    public function forceDelete(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->delete($user, $webUrl);
    }

    private function hasSubscriptionPermission(User $user, string $permission, string $subscriptionId): bool
    {
        if (! $user->is_active) {
            return false;
        }

        $subscription = AzureSubscription::query()
            ->whereKey($subscriptionId)
            ->first();

        return $subscription !== null
            && app(AccessAuthorizationService::class)->hasPermission($user, $permission, $subscription);
    }
}
