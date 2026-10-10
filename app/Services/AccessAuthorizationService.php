<?php

namespace App\Services;

use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;
use App\Models\AzureSubscription;
use App\Models\Team;
use App\Models\User;
use App\Models\UserAccessGrant;
use Illuminate\Support\Collection;

class AccessAuthorizationService
{
    public function effectiveRole(User $user, AccessRequestTargetType|string $targetType, Team|AzureSubscription|int|string $target): ?UserRole
    {
        $targetType = $targetType instanceof AccessRequestTargetType
            ? $targetType
            : AccessRequestTargetType::from($targetType);

        if ($user->isGlobal()) {
            return $user->roleEnum();
        }

        $roles = collect();

        if ($targetType === AccessRequestTargetType::Team) {
            $team = $target instanceof Team ? $target : Team::query()->findOrFail($target);

            if ($user->teams()->whereKey($team->getKey())->exists()) {
                $roles->push($user->roleEnum());
            }

            $roles = $roles->merge(
                $this->activeGrants($user, $targetType, $team->getKey(), null)->pluck('role')
                    ->map(fn (string $role): UserRole => UserRole::tryFrom($role))
                    ->filter(),
            );
        } else {
            $subscription = $target instanceof AzureSubscription
                ? $target
                : AzureSubscription::query()->whereKey($target)->firstOrFail();

            $roles = $roles->merge(
                $user->teams()
                    ->whereHas('subscriptions', fn ($query) => $query->whereKey($subscription->getKey()))
                    ->pluck('users.role')
                    ->map(fn ($role): UserRole => $role instanceof UserRole ? $role : UserRole::tryFrom($role))
                    ->filter(),
            );

            $teamIds = $subscription->teams()->pluck('teams.id');
            $roles = $roles->merge(
                $this->activeGrants($user, AccessRequestTargetType::Team, null, null)
                    ->whereIn('team_id', $teamIds)
                    ->pluck('role')
                    ->map(fn (string $role): UserRole => UserRole::tryFrom($role))
                    ->filter(),
            );

            $roles = $roles->merge(
                $this->activeGrants($user, $targetType, null, $subscription->getKey())->pluck('role')
                    ->map(fn (string $role): UserRole => UserRole::tryFrom($role))
                    ->filter(),
            );
        }

        return $this->highestRole($roles);
    }

    public function canRequestRole(User $user, AccessRequestTargetType|string $targetType, Team|AzureSubscription|int|string $target, UserRole $requestedRole): bool
    {
        if ($user->isGlobal()) {
            return false;
        }

        $current = $this->effectiveRole($user, $targetType, $target);

        return $current === null || $this->rank($requestedRole) > $this->rank($current);
    }

    public function canApprove(User $approver, \App\Models\AccessRequest $request): bool
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
            return $approver->teams()->whereKey($request->team_id)->exists();
        }

        return $approver->teams()
            ->whereHas('subscriptions', fn ($query) => $query->whereKey($request->subscription_id))
            ->exists();
    }

    public function approvers(\App\Models\AccessRequest $request): Collection
    {
        $users = User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($request): void {
                $query->where('role', UserRole::GlobalOwner->value);

                if ($request->target_type === AccessRequestTargetType::Team) {
                    $query->orWhere(function ($teamOwnerQuery) use ($request): void {
                        $teamOwnerQuery
                            ->where('role', UserRole::RestrictedOwner->value)
                            ->whereHas('teams', fn ($teams) => $teams->whereKey($request->team_id));
                    });
                } else {
                    $teamIds = AzureSubscription::query()
                        ->whereKey($request->subscription_id)
                        ->first()?->teams()
                        ->pluck('teams.id')
                        ->all() ?? [];

                    if ($teamIds !== []) {
                        $query->orWhere(function ($teamOwnerQuery) use ($teamIds): void {
                            $teamOwnerQuery
                                ->where('role', UserRole::RestrictedOwner->value)
                                ->whereHas('teams', fn ($teams) => $teams->whereIn('teams.id', $teamIds));
                        });
                    }
                }
            })
            ->whereKeyNot($request->user_id)
            ->get();

        return $users->unique('id')->values();
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

    private function activeGrants(User $user, AccessRequestTargetType $targetType, int|string|null $teamId, ?string $subscriptionId): Collection
    {
        $query = UserAccessGrant::query()
            ->where('user_id', $user->getKey())
            ->where('target_type', $targetType->value)
            ->where('starts_at', '<=', now())
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });

        if ($teamId !== null) {
            $query->where('team_id', $teamId);
        }

        if ($subscriptionId !== null) {
            $query->where('subscription_id', $subscriptionId);
        }

        return $query->get();
    }
}
