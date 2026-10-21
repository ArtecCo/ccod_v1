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
use App\Models\UserGroup;
use App\Models\UserResourceAccess;
use App\Models\UserRoleAssignment;
use App\Models\UserSubscriptionAccessOverride;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AccessAuthorizationService
{
    private const ROLE_WEIGHTS = [
        'viewer' => 10,
        'customer' => 15,
        'operator' => 20,
        'admin' => 30,
        'super_admin' => 40,
    ];

    private array $assignmentCache = [];
    private array $permissionAssignmentCache = [];
    private array $groupCache = [];

    public function hasPermission(User $user, string $permission, BusinessUnit|Project|AzureSubscription|null $target = null): bool
    {
        $assignments = $this->permissionAssignments($user, $permission);
        if ($target === null) return $assignments->isNotEmpty();
        if ($assignments->contains(fn (UserRoleAssignment $assignment): bool => $assignment->role->slug === 'super_admin')) return true;
        if ($assignments->contains(fn (UserRoleAssignment $assignment): bool => $this->resourceAccessApplies($assignment->resourceAccess, $target))) return true;
        return $this->groupPermissionsApply($user, $permission, $target);
    }

    public function effectiveRole(User $user, AccessRequestTargetType|string $targetType, BusinessUnit|Project|AzureSubscription|int|string $target): ?Role
    {
        $target = $this->resolveTarget($targetType, $target);
        $assignments = $this->activeAssignments($user);
        $superAdminRole = $assignments->first(fn (UserRoleAssignment $assignment): bool => $assignment->role->slug === 'super_admin')?->role;
        if ($superAdminRole !== null) return $superAdminRole;

        $directRole = $assignments
            ->filter(fn (UserRoleAssignment $assignment): bool => $this->resourceAccessApplies($assignment->resourceAccess, $target))
            ->sortByDesc(fn (UserRoleAssignment $assignment): int => $this->roleWeight($assignment->role))
            ->first()?->role;

        $groupRole = $this->groups($user)
            ->filter(fn (UserGroup $group): bool => $this->groupApplies($group, $target))
            ->sortByDesc(fn (UserGroup $group): int => $this->roleWeight($group->role))
            ->first()?->role;

        return collect([$directRole, $groupRole])->filter()->sortByDesc(fn (Role $role): int => $this->roleWeight($role))->first();
    }

    public function hasAnyResourceAccess(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $this->activeAssignments($user)->contains(fn (UserRoleAssignment $assignment): bool => $assignment->resourceAccess !== null)
            || $this->groups($user)->contains(fn (UserGroup $group): bool => $group->projects->isNotEmpty());
    }

    public function hasResourceAccess(User $user, BusinessUnit|Project|AzureSubscription $target): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if ($this->activeAssignments($user)->contains(fn (UserRoleAssignment $assignment): bool => $this->resourceAccessApplies($assignment->resourceAccess, $target))) return true;
        return $this->groups($user)->contains(fn (UserGroup $group): bool => $this->groupApplies($group, $target));
    }

    public function accessibleSubscriptionQuery(User $user): Builder
    {
        $assignments = $this->activeAssignments($user);
        $superAdminAssignment = $assignments->first(fn (UserRoleAssignment $assignment): bool => $assignment->role->slug === 'super_admin');
        $query = AzureSubscription::query()->with('projects.businessUnit')->orderBy('display_name');
        if ($superAdminAssignment !== null) return $query;

        $revokedIds = UserSubscriptionAccessOverride::query()->where('user_id', $user->getKey())->where('override', 'revoked')->pluck('subscription_id');
        $groups = $this->groups($user);

        return $query->whereNotIn('subscription_id', $revokedIds)->where(function (Builder $query) use ($assignments, $groups): void {
            foreach ($assignments as $assignment) {
                $access = $assignment->resourceAccess;
                if ($access === null) continue;
                match ($access->access_type) {
                    'subscription' => $query->orWhere('subscription_id', $access->subscription_id),
                    'project' => $query->orWhereHas('projects', fn (Builder $projectQuery): Builder => $projectQuery->whereKey($access->project_id)),
                    'business_unit' => $query->orWhereHas('projects', fn (Builder $projectQuery): Builder => $projectQuery->where('business_unit_id', $access->business_unit_id)),
                    default => null,
                };
            }
            foreach ($groups as $group) {
                foreach ($group->projects as $project) {
                    $query->orWhereHas('projects', fn (Builder $projectQuery): Builder => $projectQuery->whereKey($project->getKey()));
                }
            }
        });
    }

    public function accessibleSubscriptions(User $user): Collection
    {
        $assignments = $this->activeAssignments($user);
        $superAdminAssignment = $assignments->first(fn (UserRoleAssignment $assignment): bool => $assignment->role->slug === 'super_admin');
        $subscriptions = $this->accessibleSubscriptionQuery($user)->get();
        $groups = $this->groups($user);
        if ($superAdminAssignment !== null) {
            $role = $superAdminAssignment->role;
            return $subscriptions->map(function (AzureSubscription $subscription) use ($role): AzureSubscription {
                $subscription->setAttribute('access_role', $role);
                $subscription->setAttribute('access_sources', ['global: Super Admin']);
                $subscription->setAttribute('access_revoked', false);
                return $subscription;
            });
        }
        return $subscriptions->map(function (AzureSubscription $subscription) use ($assignments, $groups): AzureSubscription {
            $applicable = $assignments->filter(fn (UserRoleAssignment $assignment): bool => $this->resourceAccessApplies($assignment->resourceAccess, $subscription));
            $directRole = $applicable->sortByDesc(fn (UserRoleAssignment $assignment): int => $this->roleWeight($assignment->role))->first()?->role;
            $groupMatches = $groups->filter(fn (UserGroup $group): bool => $this->groupApplies($group, $subscription));
            $groupRole = $groupMatches->sortByDesc(fn (UserGroup $group): int => $this->roleWeight($group->role))->first()?->role;
            $role = collect([$directRole, $groupRole])->filter()->sortByDesc(fn (Role $item): int => $this->roleWeight($item))->first();
            $sources = $applicable->map(fn (UserRoleAssignment $assignment): string => $assignment->resourceAccess->access_type . ': ' . $assignment->role->name)->merge($groupMatches->map(fn (UserGroup $group): string => 'group: ' . $group->name . ' (' . $group->role->name . ')'))->unique()->values()->all();
            $subscription->setAttribute('access_role', $role);
            $subscription->setAttribute('access_sources', $sources);
            $subscription->setAttribute('access_revoked', false);
            return $subscription;
        })->values();
    }

    public function canRequestRole(User $user, AccessRequestTargetType|string $targetType, BusinessUnit|Project|AzureSubscription|int|string $target, Role $requestedRole): bool
    {
        if (! $user->is_active) return false;
        $current = $this->effectiveRole($user, $targetType, $target);
        return $current === null || $this->roleWeight($requestedRole) > $this->roleWeight($current);
    }

    public function canRequestUserGroup(User $user, UserGroup $group): bool
    {
        return $user->is_active && ! $group->users()->whereKey($user->getKey())->exists();
    }

    public function canApprove(User $approver, AccessRequest $request): bool
    {
        if ($approver->getKey() === $request->user_id) return false;
        if ($this->isSuperAdmin($approver)) return true;

        if ($request->target_type === AccessRequestTargetType::UserGroup) {
            $group = $request->userGroup;
            if ($group === null) return false;
            return $this->hasPermissionForUserGroup($approver, 'users.access_requests.decide', $group);
        }

        $target = match ($request->target_type instanceof AccessRequestTargetType ? $request->target_type->value : (string) $request->target_type) {
            'project' => Project::query()->find($request->project_id),
            'subscription' => AzureSubscription::query()->whereKey($request->subscription_id)->first(),
            default => BusinessUnit::query()->find($request->business_unit_id),
        };
        return $target !== null && $this->hasPermission($approver, 'users.access_requests.decide', $target);
    }

    public function canRevokeSubscriptionAccess(User|Developer $actor, User $targetUser, AzureSubscription $subscription): bool
    {
        if ($targetUser->getKey() === $actor->getKey()) return false;
        return $actor instanceof Developer || $this->hasPermission($actor, 'users.scopes.assign', $subscription);
    }

    public function isSubscriptionRevoked(User $user, AzureSubscription $subscription): bool
    {
        return UserSubscriptionAccessOverride::query()->where('user_id', $user->getKey())->where('subscription_id', $subscription->getKey())->where('override', 'revoked')->exists();
    }

    public function approvers(AccessRequest $request): Collection
    {
        if ($request->target_type === AccessRequestTargetType::UserGroup) {
            $group = $request->userGroup;
            if ($group === null) return collect();
            return User::query()->where('is_active', true)->where('id', '<>', $request->user_id)->get()->filter(fn (User $user): bool => $this->isSuperAdmin($user) || $this->hasPermissionForUserGroup($user, 'users.access_requests.decide', $group))->values();
        }

        $target = match ($request->target_type instanceof AccessRequestTargetType ? $request->target_type->value : (string) $request->target_type) {
            'project' => Project::query()->find($request->project_id),
            'subscription' => AzureSubscription::query()->whereKey($request->subscription_id)->first(),
            default => BusinessUnit::query()->find($request->business_unit_id),
        };
        if (! $target) return collect();
        return User::query()->where('is_active', true)->where('id', '<>', $request->user_id)->get()->filter(fn (User $user): bool => $this->isSuperAdmin($user) || $this->hasPermission($user, 'users.access_requests.decide', $target))->values();
    }

    public function roleWeight(Role $role): int
    {
        return self::ROLE_WEIGHTS[$role->slug] ?? 0;
    }

    public function isSuperAdmin(User $user): bool { return $this->activeAssignments($user)->contains(fn (UserRoleAssignment $assignment): bool => $assignment->role->slug === 'super_admin'); }

    private function hasPermissionForUserGroup(User $user, string $permission, UserGroup $group): bool
    {
        if ($this->permissionAssignments($user, $permission)->contains(fn (UserRoleAssignment $assignment): bool => $assignment->role->slug === 'super_admin')) return true;
        if ($this->permissionAssignments($user, $permission)->contains(fn (UserRoleAssignment $assignment): bool => $this->resourceAccessAppliesToGroup($assignment->resourceAccess, $group))) return true;

        return $this->groups($user)->contains(function (UserGroup $approverGroup) use ($permission, $group): bool {
            return $approverGroup->role->permissions->contains(fn ($item): bool => $item->slug === $permission && $item->is_active)
                && $this->groupsOverlap($approverGroup, $group);
        });
    }

    private function groupsOverlap(UserGroup $first, UserGroup $second): bool
    {
        $firstProjectIds = $first->projects->pluck('id');
        return $second->projects->contains(fn (Project $project): bool => $firstProjectIds->contains($project->getKey()));
    }

    private function activeAssignments(User $user): Collection
    {
        $userId = (int) $user->getKey();
        if (! isset($this->assignmentCache[$userId])) $this->assignmentCache[$userId] = UserRoleAssignment::query()->with(['role.permissions', 'resourceAccess'])->where('user_id', $user->getKey())->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->get();
        return $this->assignmentCache[$userId];
    }

    private function permissionAssignments(User $user, string $permission): Collection
    {
        $key = (int) $user->getKey() . ':' . $permission;
        if (! isset($this->permissionAssignmentCache[$key])) $this->permissionAssignmentCache[$key] = $this->activeAssignments($user)->filter(fn (UserRoleAssignment $assignment): bool => $assignment->role->permissions->contains(fn ($item): bool => $item->slug === $permission && $item->is_active));
        return $this->permissionAssignmentCache[$key];
    }

    private function groups(User $user): Collection
    {
        $userId = (int) $user->getKey();
        if (! isset($this->groupCache[$userId])) {
            $this->groupCache[$userId] = UserGroup::query()->with(['role.permissions', 'projects'])->whereHas('users', fn (Builder $query): Builder => $query->whereKey($userId)->where(function (Builder $q): void { $q->whereNull('user_group_user.starts_at')->orWhere('user_group_user.starts_at', '<=', now()); })->where(function (Builder $q): void { $q->whereNull('user_group_user.expires_at')->orWhere('user_group_user.expires_at', '>', now()); }))->get();
        }
        return $this->groupCache[$userId];
    }

    private function groupPermissionsApply(User $user, string $permission, BusinessUnit|Project|AzureSubscription $target): bool
    {
        return $this->groups($user)->contains(function (UserGroup $group) use ($permission, $target): bool {
            return $group->role->permissions->contains(fn ($item): bool => $item->slug === $permission && $item->is_active) && $this->groupApplies($group, $target);
        });
    }

    private function groupApplies(UserGroup $group, BusinessUnit|Project|AzureSubscription $target): bool
    {
        if ($target instanceof BusinessUnit) return false;
        if ($target instanceof Project) return $group->projects->contains(fn (Project $project): bool => (int) $project->getKey() === (int) $target->getKey());
        return $target->projects->contains(fn (Project $project): bool => $group->projects->contains(fn (Project $groupProject): bool => (int) $groupProject->getKey() === (int) $project->getKey()));
    }

    private function resourceAccessAppliesToGroup(?UserResourceAccess $access, UserGroup $group): bool
    {
        if ($access === null) return false;
        if ($access->access_type === 'project') return $group->projects()->whereKey($access->project_id)->exists();
        if ($access->access_type === 'business_unit') return $group->projects()->where('business_unit_id', $access->business_unit_id)->exists();
        if ($access->access_type === 'subscription') return $group->projects()->whereHas('subscriptions', fn (Builder $q): Builder => $q->where('azure_subscriptions.subscription_id', $access->subscription_id))->exists();
        return false;
    }

    private function resolveTarget(AccessRequestTargetType|string $type, BusinessUnit|Project|AzureSubscription|int|string $target): BusinessUnit|Project|AzureSubscription
    {
        $type = $type instanceof AccessRequestTargetType ? $type->value : (string) $type;
        return match ($type) { 'business_unit', 'team' => $target instanceof BusinessUnit ? $target : BusinessUnit::query()->findOrFail($target), 'project' => $target instanceof Project ? $target : Project::query()->findOrFail($target), 'subscription' => $target instanceof AzureSubscription ? $target : AzureSubscription::query()->whereKey($target)->firstOrFail(), default => throw new \InvalidArgumentException("Unsupported access target type [{$type}]."), };
    }

    private function resourceAccessApplies(?UserResourceAccess $access, BusinessUnit|Project|AzureSubscription $target): bool
    {
        if ($access === null) return false;
        if ($target instanceof BusinessUnit) return $access->access_type === 'business_unit' && (int) $access->business_unit_id === (int) $target->getKey();
        if ($target instanceof Project) { if ($access->access_type === 'project') return (int) $access->project_id === (int) $target->getKey(); return $access->access_type === 'business_unit' && (int) $access->business_unit_id === (int) $target->business_unit_id; }
        if ($access->access_type === 'subscription') return (string) $access->subscription_id === (string) $target->getKey();
        return $target->projects->contains(function (Project $project) use ($access): bool { if ($access->access_type === 'project') return (int) $access->project_id === (int) $project->getKey(); return $access->access_type === 'business_unit' && (int) $access->business_unit_id === (int) $project->business_unit_id; });
    }
}
