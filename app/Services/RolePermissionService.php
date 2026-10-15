<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Collection;

class RolePermissionService
{
    /**
     * Normalize a role's permission IDs so module actions can only exist when
     * the corresponding module view permission is granted.
     *
     * This is the persistence-side invariant. The UI also mirrors it, but the
     * final permission set is always normalized immediately before sync().
     */
    public function normalizePermissionIds(array $permissionIds): array
    {
        $permissionIds = collect($permissionIds)
            ->filter()
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->values();

        if ($permissionIds->isEmpty()) {
            return [];
        }

        $permissions = Permission::query()
            ->whereIn('id', $permissionIds)
            ->get(['id', 'slug']);

        $grantedSlugs = $permissions->pluck('slug')->all();

        $allowedSlugs = $permissions
            ->filter(function (Permission $permission) use ($grantedSlugs): bool {
                $viewPermission = self::viewPermissionFor($permission->slug);

                return $permission->slug === $viewPermission
                    || in_array($viewPermission, $grantedSlugs, true);
            })
            ->pluck('slug')
            ->all();

        return $permissions
            ->filter(fn (Permission $permission): bool => in_array($permission->slug, $allowedSlugs, true))
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    public function sync(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($this->normalizePermissionIds($permissionIds));
    }

    /**
     * Return the view permission that gates a permission's module.
     *
     * Most modules use the first slug segment (for example cost.export ->
     * cost.view). The two compound categories use their own resource view
     * permissions, while monitoring.* is governed by alerts.view because the
     * permission catalogue has no monitoring.view permission.
     */
    public static function viewPermissionFor(string $slug): string
    {
        $prefix = Str::before($slug, '.');

        return match ($prefix) {
            'monitoring' => 'alerts.view',
            default => $prefix . '.view',
        };
    }
}
