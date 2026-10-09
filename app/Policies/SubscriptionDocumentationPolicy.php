<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\AzureSubscription;
use App\Models\SubscriptionDocumentation;
use App\Models\User;

class SubscriptionDocumentationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, SubscriptionDocumentation $documentation): bool
    {
        return $user->is_active;
    }

    public function create(User $user): bool
    {
        return $this->canModify($user);
    }

    public function createForSubscription(User $user, AzureSubscription $subscription): bool
    {
        if (! $user->is_active) {
            return false;
        }

        $role = $user->roleEnum();

        if (in_array($role, [
            UserRole::GlobalOwner,
            UserRole::GlobalContributor,
        ], true)) {
            return true;
        }

        if (! in_array($role, [
            UserRole::RestrictedOwner,
            UserRole::RestrictedContributor,
        ], true)) {
            return false;
        }

        return $user->teams()
            ->whereHas('subscriptions', function ($query) use ($subscription): void {
                $query->where(
                    'azure_subscriptions.subscription_id',
                    $subscription->subscription_id
                );
            })
            ->exists();
    }

    public function update(User $user, SubscriptionDocumentation $documentation): bool
    {
        return $this->canModify($user, $documentation);
    }

    public function delete(User $user, SubscriptionDocumentation $documentation): bool
    {
        return $this->canModify($user, $documentation);
    }

    public function restore(User $user, SubscriptionDocumentation $documentation): bool
    {
        return $this->canModify($user, $documentation);
    }

    public function forceDelete(User $user, SubscriptionDocumentation $documentation): bool
    {
        return $this->canModify($user, $documentation);
    }

    private function canModify(
        User $user,
        ?SubscriptionDocumentation $documentation = null
    ): bool {
        if (! $user->is_active) {
            return false;
        }

        $role = $user->roleEnum();

        if (in_array($role, [
            UserRole::GlobalOwner,
            UserRole::GlobalContributor,
        ], true)) {
            return true;
        }

        if (! in_array($role, [
            UserRole::RestrictedOwner,
            UserRole::RestrictedContributor,
        ], true)) {
            return false;
        }

        if ($documentation === null) {
            return false;
        }

        return $user->teams()
            ->whereHas('subscriptions', function ($query) use ($documentation): void {
                $query->where(
                    'azure_subscriptions.subscription_id',
                    $documentation->subscription_id
                );
            })
            ->exists();
    }
}
