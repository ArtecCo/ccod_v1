<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AzureSubscription;
use App\Models\SubscriptionWebUrl;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use App\Enums\AccessRequestTargetType;

class SubscriptionWebUrlPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active === true;
    }

    public function view(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->canAccessSubscription($user, $webUrl->subscription_id);
    }

    public function create(User $user, AzureSubscription $subscription): bool
    {
        if (! $user->is_active) {
            return false;
        }

        $role = $this->effectiveRole($user, $subscription);

        return in_array($role, [
            UserRole::GlobalOwner,
            UserRole::GlobalContributor,
            UserRole::RestrictedOwner,
            UserRole::RestrictedContributor,
        ], true);
    }

    public function update(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->canModify($user, $webUrl);
    }

    public function delete(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->canModify($user, $webUrl);
    }

    public function restore(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->canModify($user, $webUrl);
    }

    public function forceDelete(User $user, SubscriptionWebUrl $webUrl): bool
    {
        return $this->canModify($user, $webUrl);
    }

    private function canModify(User $user, SubscriptionWebUrl $webUrl): bool
    {
        if (! $user->is_active) {
            return false;
        }

        $subscription = AzureSubscription::query()
            ->whereKey($webUrl->subscription_id)
            ->first();

        if ($subscription === null) {
            return false;
        }

        $role = $this->effectiveRole($user, $subscription);

        return in_array($role, [
            UserRole::GlobalOwner,
            UserRole::GlobalContributor,
            UserRole::RestrictedOwner,
            UserRole::RestrictedContributor,
        ], true);
    }

    private function canAccessSubscription(User $user, string $subscriptionId): bool
    {
        if (! $user->is_active) {
            return false;
        }

        $subscription = AzureSubscription::query()
            ->whereKey($subscriptionId)
            ->first();

        return $subscription !== null
            && app(AccessAuthorizationService::class)->effectiveRole(
                $user,
                AccessRequestTargetType::Subscription,
                $subscription,
            ) !== null;
    }

    private function effectiveRole(User $user, AzureSubscription $subscription): ?UserRole
    {
        return app(AccessAuthorizationService::class)->effectiveRole(
            $user,
            AccessRequestTargetType::Subscription,
            $subscription,
        );
    }
}
