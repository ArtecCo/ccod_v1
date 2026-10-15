<?php

namespace App\Services;

use App\Enums\AccessRequestTargetType;
use App\Models\AccessRequest;
use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\Developer;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\UserResourceAccess;
use App\Models\UserRoleAssignment;
use App\Models\UserSubscriptionAccessOverride;
use Illuminate\Support\Collection;

class AccessAuthorizationService
{
    public function hasPermission(User $user, string $permission, BusinessUnit|Project|AzureSubscription|null $target = null): bool
    {
        $assignments = UserRoleAssignment::query()
            ->with(['role.permissions', 'resourceAccess'])
            ->where('user_id', $user->getKey())
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereHas('role.permissions', fn ($q) => $q->where('slug', $permission)->where('is_active', true))
            ->get();

        if ($target === null) {
            return $assignments->isNotEmpty();
        }

        if ($assignments->contains(fn (UserRoleAssignment $assignment): bool => $assignment->role->slug === 'super_admin')) {
            return true;
        }

        foreach ($assignments as $assignment) {
            if ($this->resourceAccessApplies($assignment->resourceAccess, $target)) {
                return true;
            }
        }

        return false;
    }

    public function effectiveRole(User $user, AccessRequestTargetType|string $targetType, BusinessUnit|Project|AzureSubscription|int|string $target): ?Role
    {
        $target = $this->resolveTarget($targetType, $target);
        $assignments = $this->activeAssignments($user)->with('resourceAccess')->get();

        $superAdminRole = $assignments
            ->first(fn (UserRoleAssignment $assignment): bool => $assignment->role->slug === 'super_admin')
            ?->role;

        if ($superAdminRole !== null) {
            return $superAdminRole;
        }

        return $assignments
            ->filter(fn (UserRoleAssignment $assignment): bool => $this->resourceAccessApplies($assignment->resourceAccess, $target))
            ->sortByDesc(fn ($assignment) => $this->roleWeight($assignment->role))
            ->first()
            ?->role;
    }

    public function accessibleSubscriptions(User $user): Collection
    {
        $subscriptions = AzureSubscription::query()->orderBy('display_name')->get();
        $isSuperAdmin = $this->isSuperAdmin($user);

        return $subscriptions
            ->filter(fn (AzureSubscription $subscription): bool => $isSuperAdmin
                || ($this->hasPermission($user, 'subscriptions.view', $subscription)
                    && ! $this->isSubscriptionRevoked($user, $subscription)))
            ->values()
            ->map(function (AzureSubscription $subscription) use ($user, $isSuperAdmin): AzureSubscription {
                $subscription->setAttribute(
                    'access_role',
                    $isSuperAdmin
                        ? $this->superAdminRole($user)
                        : $this->effectiveRole($user, AccessRequestTargetType::Subscription, $subscription)
                );
                $subscription->setAttribute(
                    'access_sources',
                    $isSuperAdmin
                        ? ['global: Super Admin']
                        : $this->accessSources($user, $subscription)
                );
                $subscription->setAttribute('access_revoked', false);

                return $subscription;
            });
    }

    public function canRequestRole(User $user, AccessRequestTargetType|string $targetType, BusinessUnit|Project|AzureSubscription|int|string $target, Role $requestedRole): bool
    {
        if (! $user->is_active) {
            return false;
        }

        $current = $this->effectiveRole($user, $targetType, $target);

        return $current === null || $this->roleWeight($requestedRole) > $this->roleWeight($current);
    }

    public function canApprove(User $approver, AccessRequest $request): bool
    {
        if ($approver->getKey() === $request->user_id) {
            return false;
        }

        $target = match ((string) $request->target_type) {
            'project' => Project::query()->find($request->project_id),
            'subscription' => AzureSubscription::query()->whereKey($request->subscription_id)->first(),
            default => BusinessUnit::query()->find($request->business_unit_id),
        };

        return $target !== null && $this->hasPermission($approver, 'users.roles.assign', $target);
    }

    public function canRevokeSubscriptionAccess(User|Developer $actor, User $targetUser, AzureSubscription $subscription): bool
    {
        if ($targetUser->getKey() === $actor->getKey()) {
            return false;
        }

        return $actor instanceof Developer || $this->hasPermission($actor, 'users.scopes.assign', $subscription);
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
        $target = match ((string) $request->target_type) {
            'project' => Project::query()->find($request->project_id),
            'subscription' => AzureSubscription::query()->whereKey($request->subscription_id)->first(),
            default => BusinessUnit::query()->find($request->business_unit_id),
        };

        if (! $target) {
            return collect();
        }

        return User::query()
            ->where('is_active', true)
            ->where('id', '<>', $request->user_id)
            ->get()
            ->filter(fn (User $user): bool => $this->hasPermission($user, 'users.roles.assign', $target))
            ->values();
    }

    public function roleWeight(Role $role): int
    {
        return match ($role->slug) {
            'viewer' => 10,
            'customer' => 15,
            'operator' => 20,
            'admin' => 30,
            'super_admin' => 40,
            default => $role->permissions()->count(),
        };
    }

    public function isSuperAdmin(User $user): bool
    {
        return $this->activeAssignments($user)
            ->whereHas('role', fn ($q) => $q->where('slug', 'super_admin'))
            ->exists();
    }

    private function superAdminRole(User $user): ?Role
    {
        return $this->activeAssignments($user)
            ->with('role')
            ->get()
            ->first(fn (UserRoleAssignment $assignment): bool => $assignment->role->slug === 'super_admin')
            ?->role;
    }

    private function activeAssignments(User $user)
    {
        return UserRoleAssignment::query()
            ->with('role')
            ->where('user_id', $user->getKey())
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    private function resolveTarget(AccessRequestTargetType|string $type, BusinessUnit|Project|AzureSubscription|int|string $target): BusinessUnit|Project|AzureSubscription
    {
        $type = $type instanceof AccessRequestTargetType ? $type->value : (string) $type;

        return match ($type) {
            'business_unit', 'team' => $target instanceof BusinessUnit ? $target : BusinessUnit::query()->findOrFail($target),
            'project' => $target instanceof Project ? $target : Project::query()->findOrFail($target),
            'subscription' => $target instanceof AzureSubscription ? $target : AzureSubscription::query()->whereKey($target)->firstOrFail(),
            default => throw new \InvalidArgumentException("Unsupported access target type [{$type}]."),
        };
    }

    private function resourceAccessApplies(?UserResourceAccess $access, BusinessUnit|Project|AzureSubscription $target): bool
    {
        if ($access === null) {
            return false;
        }

        if ($target instanceof BusinessUnit) {
            return $access->access_type === 'business_unit'
                && (int) $access->business_unit_id === (int) $target->getKey();
        }

        if ($target instanceof Project) {
            if ($access->access_type === 'project') {
                return (int) $access->project_id === (int) $target->getKey();
            }

            return $access->access_type === 'business_unit'
                && (int) $access->business_unit_id === (int) $target->business_unit_id;
        }

        if ($access->access_type === 'subscription') {
            return (string) $access->subscription_id === (string) $target->getKey();
        }

        $project = $target->projects()->first();

        if (! $project) {
            return false;
        }

        if ($access->access_type === 'project') {
            return (int) $access->project_id === (int) $project->getKey();
        }

        return $access->access_type === 'business_unit'
            && (int) $access->business_unit_id === (int) $project->business_unit_id;
    }

    private function accessSources(User $user, AzureSubscription $subscription): array
    {
        $sources = [];

        foreach ($this->activeAssignments($user)->with('resourceAccess', 'role')->get() as $assignment) {
            if ($this->resourceAccessApplies($assignment->resourceAccess, $subscription)) {
                $sources[] = $assignment->resourceAccess->access_type . ': ' . $assignment->role->name;
            }
        }

        return array_values(array_unique($sources));
    }
}
