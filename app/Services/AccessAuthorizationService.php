<?php

namespace App\Services;

use App\Models\AccessRequest;
use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroup;
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

    private array $groupCache = [];

    public function hasPermission(User $user, string $permission, BusinessUnit|Project|AzureSubscription|null $target = null): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->groups($user)->contains(function (UserGroup $group) use ($permission, $target): bool {
            if (! $group->role->permissions->contains(fn ($item): bool => $item->slug === $permission && $item->is_active)) {
                return false;
            }

            return $target === null || $this->groupApplies($group, $target);
        });
    }

    public function effectiveRole(User $user, string $targetType, BusinessUnit|Project|AzureSubscription|int|string $target): ?Role
    {
        $resolved = $this->resolveTarget($targetType, $target);

        return $this->groups($user)
            ->filter(fn (UserGroup $group): bool => $this->groupApplies($group, $resolved))
            ->sortByDesc(fn (UserGroup $group): int => $this->roleWeight($group->role))
            ->first()?->role;
    }

    public function hasAnyResourceAccess(User $user): bool
    {
        return $this->isSuperAdmin($user)
            || $this->groups($user)->contains(fn (UserGroup $group): bool => $group->projects->isNotEmpty());
    }

    public function hasResourceAccess(User $user, BusinessUnit|Project|AzureSubscription $target): bool
    {
        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->groups($user)->contains(fn (UserGroup $group): bool => $this->groupApplies($group, $target));
    }

    public function accessibleSubscriptionQuery(User $user): Builder
    {
        $query = AzureSubscription::query()
            ->with('projects.businessUnit')
            ->orderBy('display_name');

        if ($this->isSuperAdmin($user)) {
            return $query;
        }

        $groups = $this->groups($user);

        if ($groups->isEmpty()) {
            return $query->where('subscription_id', '__no_group_access__');
        }

        $projectIds = $groups
            ->flatMap(fn (UserGroup $group): Collection => $group->projects->pluck('id'))
            ->unique()
            ->values()
            ->all();

        if ($projectIds === []) {
            return $query->where('subscription_id', '__no_group_access__');
        }

        return $query->whereHas('projects', fn (Builder $projectQuery): Builder => $projectQuery->whereIn('projects.id', $projectIds));
    }

    public function accessibleSubscriptions(User $user): Collection
    {
        $subscriptions = $this->accessibleSubscriptionQuery($user)->get();
        $groups = $this->groups($user);

        if ($this->isSuperAdmin($user)) {
            $role = $groups->sortByDesc(fn (UserGroup $group): int => $this->roleWeight($group->role))->first()?->role;

            return $subscriptions->map(function (AzureSubscription $subscription) use ($role): AzureSubscription {
                $subscription->setAttribute('access_role', $role);
                $subscription->setAttribute('access_sources', ['global: Super Admin']);
                return $subscription;
            });
        }

        $groupsByProject = [];
        foreach ($groups as $group) {
            foreach ($group->projects as $project) {
                $groupsByProject[(string) $project->getKey()][] = $group;
            }
        }

        return $subscriptions->map(function (AzureSubscription $subscription) use ($groupsByProject): AzureSubscription {
            $matchesById = [];

            foreach ($subscription->projects as $project) {
                foreach ($groupsByProject[(string) $project->getKey()] ?? [] as $group) {
                    $matchesById[(string) $group->getKey()] = $group;
                }
            }

            $matches = collect(array_values($matchesById));
            $role = $matches->sortByDesc(fn (UserGroup $group): int => $this->roleWeight($group->role))->first()?->role;
            $sources = $matches
                ->map(fn (UserGroup $group): string => 'group: '.$group->name.' ('.$group->role->name.')')
                ->unique()
                ->values()
                ->all();

            $subscription->setAttribute('access_role', $role);
            $subscription->setAttribute('access_sources', $sources);
            return $subscription;
        })->values();
    }

    public function canRequestUserGroup(User $user, UserGroup $group): bool
    {
        if (! $user->is_active || ! $group->role->is_active) {
            return false;
        }

        return ! $group->users()
            ->whereKey($user->getKey())
            ->where(function (Builder $query): void {
                $query->whereNull('user_group_user.starts_at')->orWhere('user_group_user.starts_at', '<=', now());
            })
            ->where(function (Builder $query): void {
                $query->whereNull('user_group_user.expires_at')->orWhere('user_group_user.expires_at', '>', now());
            })
            ->exists();
    }

    public function canApprove(User $approver, AccessRequest $request): bool
    {
        if ($approver->getKey() === $request->user_id || $request->userGroup === null) {
            return false;
        }

        if ($this->isSuperAdmin($approver)) {
            return true;
        }

        $requestedGroup = $request->userGroup;

        return $this->groups($approver)->contains(function (UserGroup $approverGroup) use ($requestedGroup): bool {
            return $approverGroup->role->permissions->contains(
                fn ($permission): bool => $permission->slug === 'users.access_requests.decide' && $permission->is_active
            ) && $this->groupsOverlap($approverGroup, $requestedGroup);
        });
    }

    public function approvers(AccessRequest $request): Collection
    {
        if ($request->userGroup === null) {
            return collect();
        }

        $requestedProjectIds = $request->userGroup->projects()->pluck('projects.id');

        return User::query()
            ->where('is_active', true)
            ->where('id', '<>', $request->user_id)
            ->whereHas('userGroups', function (Builder $groupQuery) use ($requestedProjectIds): void {
                $groupQuery->where(function (Builder $query) use ($requestedProjectIds): void {
                    $query->whereHas('role', fn (Builder $roleQuery): Builder => $roleQuery
                        ->where('slug', 'super_admin')
                        ->where('is_active', true))
                        ->orWhere(function (Builder $query) use ($requestedProjectIds): void {
                            $query->whereHas('role.permissions', fn (Builder $permissionQuery): Builder => $permissionQuery
                                ->where('permissions.slug', 'users.access_requests.decide')
                                ->where('permissions.is_active', true))
                                ->when($requestedProjectIds->isNotEmpty(), fn (Builder $query): Builder => $query->whereHas(
                                    'projects',
                                    fn (Builder $projectQuery): Builder => $projectQuery->whereIn('projects.id', $requestedProjectIds),
                                ), fn (Builder $query): Builder => $query->whereRaw('1 = 0'));
                        });
                });

                $groupQuery->where(function (Builder $query): void {
                    $query->whereNull('user_group_user.starts_at')->orWhere('user_group_user.starts_at', '<=', now());
                })->where(function (Builder $query): void {
                    $query->whereNull('user_group_user.expires_at')->orWhere('user_group_user.expires_at', '>', now());
                });
            })
            ->get()
            ->values();
    }

    public function roleWeight(Role $role): int
    {
        return self::ROLE_WEIGHTS[$role->slug] ?? 0;
    }

    public function isSuperAdmin(User $user): bool
    {
        return $this->groups($user)->contains(fn (UserGroup $group): bool => $group->role->slug === 'super_admin');
    }

    private function groups(User $user): Collection
    {
        $userId = (int) $user->getKey();

        if (! isset($this->groupCache[$userId])) {
            $this->groupCache[$userId] = UserGroup::query()
                ->with(['role.permissions', 'projects'])
                ->whereHas('users', function (Builder $query) use ($userId): void {
                    $query->whereKey($userId)
                        ->where(function (Builder $q): void {
                            $q->whereNull('user_group_user.starts_at')->orWhere('user_group_user.starts_at', '<=', now());
                        })
                        ->where(function (Builder $q): void {
                            $q->whereNull('user_group_user.expires_at')->orWhere('user_group_user.expires_at', '>', now());
                        });
                })
                ->whereHas('role', fn (Builder $query): Builder => $query->where('is_active', true))
                ->get();
        }

        return $this->groupCache[$userId];
    }

    private function groupsOverlap(UserGroup $first, UserGroup $second): bool
    {
        $firstProjectIds = $first->projects->pluck('id');

        return $second->projects->contains(fn (Project $project): bool => $firstProjectIds->contains($project->getKey()));
    }

    private function groupApplies(UserGroup $group, BusinessUnit|Project|AzureSubscription $target): bool
    {
        if ($target instanceof BusinessUnit) {
            return false;
        }

        if ($target instanceof Project) {
            return $group->projects->contains(fn (Project $project): bool => (int) $project->getKey() === (int) $target->getKey());
        }

        return $target->projects->contains(fn (Project $project): bool => $group->projects->contains(fn (Project $groupProject): bool => (int) $groupProject->getKey() === (int) $project->getKey()));
    }

    private function resolveTarget(string $targetType, BusinessUnit|Project|AzureSubscription|int|string $target): BusinessUnit|Project|AzureSubscription
    {
        return match ($targetType) {
            'business_unit', 'team' => $target instanceof BusinessUnit ? $target : BusinessUnit::query()->findOrFail($target),
            'project' => $target instanceof Project ? $target : Project::query()->findOrFail($target),
            'subscription' => $target instanceof AzureSubscription ? $target : AzureSubscription::query()->whereKey($target)->firstOrFail(),
            default => throw new \InvalidArgumentException('Unsupported authorization target type.'),
        };
    }
}
