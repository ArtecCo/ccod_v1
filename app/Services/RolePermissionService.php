<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Str;

class RolePermissionService
{
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

        return $permissions
            ->filter(function (Permission $permission) use ($grantedSlugs): bool {
                $viewPermission = self::viewPermissionFor($permission->slug);

                return $permission->slug === $viewPermission
                    || in_array($viewPermission, $grantedSlugs, true);
            })
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    public function sync(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($this->normalizePermissionIds($permissionIds));
    }

    public static function viewPermissionFor(string $slug): string
    {
        $prefix = Str::before($slug, '.');

        return match ($prefix) {
            'monitoring' => 'alerts.view',
            default => $prefix . '.view',
        };
    }
}
