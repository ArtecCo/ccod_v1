<?php

namespace App\Services;

use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;
use App\Models\AccessRequest;
use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\Developer;
use App\Models\User;
use App\Models\UserAccessGrant;
use App\Models\UserSubscriptionAccessOverride;
use Illuminate\Support\Collection;

class AccessAuthorizationService
{
    public function effectiveRole(User $user, AccessRequestTargetType|string $targetType, BusinessUnit|AzureSubscription|int|string $target): ?UserRole
    {
        $targetType = $targetType instanceof AccessRequestTargetType
            ? $targetType
            : AccessRequestTargetType::from($targetType);

        if ($user->isGlobal()) {
            return $user->roleEnum();
        }

        if ($targetType === AccessRequestTargetType::Subscription) {
            $subscription = $target instanceof AzureSubscription
                ? $target
                : AzureSubscription::query()->whereKey($target)->firstOrFail();

            if ($this->isSubscriptionRevoked($user, $subscription)) {
                return null;
            }
        }

        $roles = collect();

        if ($targetType === AccessRequestTargetType::Team) {
            $businessUnit = $target instanceof BusinessUnit ? $target : BusinessUnit::query()->findOrFail($target);

            if ($user->businessUnits()->whereKey($businessUnit->getKey())->exists()) {
                $roles->push($user->roleEnum());
            }

            $roles = $roles->merge(
                $this->activeGrants($user, $targetType, $businessUnit->getKey(), null)->pluck('role')
                    ->map(fn (string $role): ?UserRole => UserRole::tryFrom($role))
                    ->filter(),
            );
        } else {
            $subscription = $target instanceof AzureSubscription
                ? $target
                : AzureSubscription::query()->whereKey($target)->firstOrFail();

            if ($user->businessUnits()->whereHas('subscriptions', fn ($query) => $query->whereKey($subscription->getKey()))->exists()) {
                $roles->push($user->roleEnum());
            }

            $businessUnitIds = $subscription->businessUnits()->pluck('business_units.id');

            $roles = $roles->merge(
                $this->activeGrants($user, AccessRequestTargetType::Team, null, null)
                    ->whereIn('business_unit_id', $businessUnitIds)
                    ->pluck('role')
                    ->map(fn (string $role): ?UserRole => UserRole::tryFrom($role))
                    ->filter(),
            );

            $roles = $roles->merge(
                $this->activeGrants($user, $targetType, null, $subscription->getKey())->pluck('role')
                    ->map(fn (string $role): ?UserRole => UserRole::tryFrom($role))
                    ->filter(),
            );
        }

        return $this->highestRole($roles);
    }

    public function accessibleSubscriptions(User $user): Collection
    {
        if ($user->isGlobal()) {
            return AzureSubscription::query()
                ->orderBy('display_name')
                ->get()
                ->map(function (AzureSubscription $subscription) use ($user): AzureSubscription {
                    $subscription->setAttribute('access_role', $user->roleEnum());
                    $subscription->setAttribute('access_sources', ['Global role']);
                    $subscription->setAttribute('access_revoked', false);

                    return $subscription;
                });
        }

        $subscriptions = collect();

        $businessUnitSubscriptions = $user->businessUnits()
            ->with('subscriptions')
            ->get()
            ->flatMap(fn (BusinessUnit $businessUnit) => $businessUnit->subscriptions->map(fn (AzureSubscription $subscription) => [
                'subscription' => $subscription,
                'source' => 'Business Unit: '.$businessUnit->name,
            ]));

        $businessUnitGrants = $this->activeGrants($user, AccessRequestTargetType::Team, null, null)
            ->load('businessUnit.subscriptions')
            ->flatMap(fn (UserAccessGrant $grant) => $grant->businessUnit?->subscriptions->map(fn (AzureSubscription $subscription) => [
                'subscription' => $subscription,
                'source' => 'Business Unit grant: '.$grant->businessUnit->name,
            ]) ?? collect());

        $directGrants = $this->activeGrants($user, AccessRequestTargetType::Subscription, null, null)
            ->load('subscription')
            ->filter(fn (UserAccessGrant $grant): bool => $grant->subscription !== null)
            ->map(fn (UserAccessGrant $grant) => [
                'subscription' => $grant->subscription,
                'source' => 'Direct grant',
            ]);

        foreach ($businessUnitSubscriptions->concat($businessUnitGrants)->concat($directGrants) as $entry) {
            $subscription = $entry['subscription'];
            $id = $subscription->getKey();

            if ($this->isSubscriptionRevoked($user, $subscription)) {
                continue;
            }

            $existing = $subscriptions->get($id);
            $sources = $existing?->getAttribute('access_sources') ?? [];
            $sources[] = $entry['source'];

            $role = $this->effectiveRole($user, AccessRequestTargetType::Subscription, $subscription);
            if ($role === null) {
                continue;
            }

            $subscription->setAttribute('access_role', $role);
            $subscription->setAttribute('access_sources', array_values(array_unique($sources)));
            $subscription->setAttribute('access_revoked', false);
            $subscriptions->put($id, $subscription);
        }

        return $subscriptions->sortBy('display_name')->values();
    }

    public function canRequestRole(User $user, AccessRequestTargetType|string $targetType, BusinessUnit|AzureSubscription|int|string $target, UserRole $requestedRole): bool
    {
        if ($user->isGlobal()) {
            return false;
        }

        $current = $this->effectiveRole($user, $targetType, $target);

        return $current === null || $this->rank($requestedRole) > $this->rank($current);
    }

    public function canApprove(User $approver, AccessRequest $request): bool
    {
        if ($approver->id === $request->user_id) {
            return false;
        }

        if ($approver->isGlobalOwner()) {
            return true;
        }

        if ($approver->roleEnum() !== UserRole::RestrictedOwner) {
            return false;
        }

        if ($request->target_type === AccessRequestTargetType::Team) {
            return $approver->businessUnits()->whereKey($request->business_unit_id)->exists();
        }

        return $approver->businessUnits()
            ->whereHas('subscriptions', fn ($query) => $query->whereKey($request->subscription_id))
            ->exists();
    }

    public function canRevokeSubscriptionAccess(User|Developer $actor, User $targetUser, AzureSubscription $subscription): bool
    {
        if ($targetUser->isGlobal() || ($actor instanceof User && $actor->getKey() === $targetUser->getKey())) {
            return false;
        }

        if ($actor instanceof Developer || $actor->isGlobalOwner()) {
            return true;
        }

        if ($actor->roleEnum() !== UserRole::RestrictedOwner) {
            return false;
        }

        return $actor->businessUnits()
            ->whereHas('subscriptions', fn ($query) => $query->whereKey($subscription->getKey()))
            ->exists();
    }

    public function isSubscriptionRevoked(User $user, AzureSubscription $subscription): bool
    {
        return UserSubscriptionAccessOverride::query()
            ->where('user_id', $user->getKey())
            ->where('subscription_id', $subscription->getKey())
            ->where('override', 'revoked')
            ->exists();
    }

    public function approvers(AccessRequest $request): Collection
    {
        $query = User::query()
            ->where('is_active', true)
            ->where('id', '<>', $request->user_id)
            ->where(function ($query) use ($request): void {
                $query->where('role', UserRole::GlobalOwner->value);

                if ($request->target_type === AccessRequestTargetType::Team) {
                    $query->orWhere(function ($ownerQuery) use ($request): void {
                        $ownerQuery
                            ->where('role', UserRole::RestrictedOwner->value)
                            ->whereHas('businessUnits', fn ($businessUnits) => $businessUnits->whereKey($request->business_unit_id));
                    });
                } else {
                    $businessUnitIds = AzureSubscription::query()
                        ->whereKey($request->subscription_id)
                        ->first()?->businessUnits()
                        ->pluck('business_units.id')
                        ->all() ?? [];

                    if ($businessUnitIds !== []) {
                        $query->orWhere(function ($ownerQuery) use ($businessUnitIds): void {
                            $ownerQuery
                                ->where('role', UserRole::RestrictedOwner->value)
                                ->whereHas('businessUnits', fn ($businessUnits) => $businessUnits->whereIn('business_units.id', $businessUnitIds));
                        });
                    }
                }
            });

        return $query->get()->unique('id')->values();
    }

    public function rank(UserRole $role): int
    {
        return match ($role) {
            UserRole::RestrictedReader => 10,
            UserRole::RestrictedContributor => 20,
            UserRole::RestrictedOwner => 30,
            UserRole::GlobalReader => 40,
            UserRole::GlobalContributor => 50,
            UserRole::GlobalOwner => 60,
        };
    }

    private function highestRole(Collection $roles): ?UserRole
    {
        return $roles
            ->filter(fn ($role): bool => $role instanceof UserRole)
            ->sortByDesc(fn (UserRole $role): int => $this->rank($role))
            ->first();
    }

    private function activeGrants(User $user, AccessRequestTargetType $targetType, int|string|null $businessUnitId, ?string $subscriptionId): Collection
    {
        $query = UserAccessGrant::query()
            ->where('user_id', $user->getKey())
            ->where('target_type', $targetType->value)
            ->where('starts_at', '<=', now())
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        if ($businessUnitId !== null) {
            $query->where('business_unit_id', $businessUnitId);
        }

        if ($subscriptionId !== null) {
            $query->where('subscription_id', $subscriptionId);
        }

        return $query->get();
    }
}
