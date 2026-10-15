<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;

class RolePermissionService
{
    public function normalizePermissionIds(array $permissionIds): array
    {
        return Permission::query()
            ->whereIn('id', collect($permissionIds)->filter()->map(static fn ($id): int => (int) $id)->unique())
            ->whereNotIn('slug', self::resourceViewPermissions())
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    public function sync(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($this->normalizePermissionIds($permissionIds));
    }

    public static function resourceViewPermissions(): array
    {
        return [
            'projects.view',
            'applications.view',
            'environments.view',
            'subscriptions.view',
        ];
    }
}
