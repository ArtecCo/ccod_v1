<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;

class RolePermissionService
{
    /** @return array<string, array{category: string, name: string}> */
    public static function managedPermissions(): array
    {
        return [
            'dashboard.view' => ['category' => 'Dashboard', 'name' => 'Open home route'],
            'documentation.view' => ['category' => 'Documentation', 'name' => 'View documents'],
            'documentation.create' => ['category' => 'Documentation', 'name' => 'Create documents'],
            'documentation.edit' => ['category' => 'Documentation', 'name' => 'Edit documents'],
            'documentation.delete' => ['category' => 'Documentation', 'name' => 'Delete documents'],
            'subscriptions.create' => ['category' => 'Subscriptions', 'name' => 'Create subscription'],
            'subscriptions.view' => ['category' => 'Subscriptions', 'name' => 'View subscription'],
            'subscriptions.edit' => ['category' => 'Subscriptions', 'name' => 'Edit subscription'],
            'subscriptions.delete' => ['category' => 'Subscriptions', 'name' => 'Delete subscription'],
            'subscriptions.sync' => ['category' => 'Subscriptions', 'name' => 'Manual Sync'],
            'alerts.view' => ['category' => 'Alerts', 'name' => 'View alerts'],
            'alerts.export' => ['category' => 'Alerts', 'name' => 'Export alerts'],
            'alerts.sync' => ['category' => 'Alerts', 'name' => 'Manual Sync'],
            'projects.view' => ['category' => 'Projects', 'name' => 'View projects'],
            'projects.create' => ['category' => 'Projects', 'name' => 'Create projects'],
            'projects.edit' => ['category' => 'Projects', 'name' => 'Update/Edit projects'],
            'projects.delete' => ['category' => 'Projects', 'name' => 'Delete projects'],
            'resources.view' => ['category' => 'Resources', 'name' => 'View resources'],
            'resources.export' => ['category' => 'Resources', 'name' => 'Export resources'],
            'resources.sync' => ['category' => 'Resources', 'name' => 'Manual Sync'],
            'security.view' => ['category' => 'Security', 'name' => 'View security'],
            'security.export' => ['category' => 'Security', 'name' => 'Export security'],
            'security.sync' => ['category' => 'Security', 'name' => 'Manual Sync'],
            'tickets.view' => ['category' => 'Tickets', 'name' => 'View tickets'],
            'tickets.create' => ['category' => 'Tickets', 'name' => 'Create tickets'],
            'tickets.update' => ['category' => 'Tickets', 'name' => 'Update/Edit tickets'],
            'tickets.assign' => ['category' => 'Tickets', 'name' => 'Assign tickets'],
            'tickets.close' => ['category' => 'Tickets', 'name' => 'Close tickets'],
            'tickets.export' => ['category' => 'Tickets', 'name' => 'Export tickets'],
            'urls.view' => ['category' => 'URLs', 'name' => 'View URLs'],
            'urls.create' => ['category' => 'URLs', 'name' => 'Add URL'],
            'urls.edit' => ['category' => 'URLs', 'name' => 'Update/Edit URL'],
            'urls.delete' => ['category' => 'URLs', 'name' => 'Delete URL'],
            'urls.export' => ['category' => 'URLs', 'name' => 'Export URLs'],
            'roles.view' => ['category' => 'Roles & Permissions', 'name' => 'View roles and permissions'],
            'roles.create' => ['category' => 'Roles & Permissions', 'name' => 'Create roles'],
            'roles.edit' => ['category' => 'Roles & Permissions', 'name' => 'Update/Edit roles'],
            'roles.delete' => ['category' => 'Roles & Permissions', 'name' => 'Delete roles'],
            'users.view' => ['category' => 'Users', 'name' => 'View users'],
            'users.create' => ['category' => 'Users', 'name' => 'Create users'],
            'users.edit' => ['category' => 'Users', 'name' => 'Update/Edit users'],
            'users.delete' => ['category' => 'Users', 'name' => 'Delete users'],
        ];
    }

    /** @return array<string, string> */
    public static function viewPermissionForActions(): array
    {
        return [
            'documentation.create' => 'documentation.view',
            'documentation.edit' => 'documentation.view',
            'documentation.delete' => 'documentation.view',
            'subscriptions.create' => 'subscriptions.view',
            'subscriptions.edit' => 'subscriptions.view',
            'subscriptions.delete' => 'subscriptions.view',
            'subscriptions.sync' => 'subscriptions.view',
            'alerts.export' => 'alerts.view',
            'alerts.sync' => 'alerts.view',
            'projects.create' => 'projects.view',
            'projects.edit' => 'projects.view',
            'projects.delete' => 'projects.view',
            'resources.export' => 'resources.view',
            'resources.sync' => 'resources.view',
            'security.export' => 'security.view',
            'security.sync' => 'security.view',
            'tickets.create' => 'tickets.view',
            'tickets.update' => 'tickets.view',
            'tickets.assign' => 'tickets.view',
            'tickets.close' => 'tickets.view',
            'tickets.export' => 'tickets.view',
            'urls.create' => 'urls.view',
            'urls.edit' => 'urls.view',
            'urls.delete' => 'urls.view',
            'urls.export' => 'urls.view',
            'roles.create' => 'roles.view',
            'roles.edit' => 'roles.view',
            'roles.delete' => 'roles.view',
            'users.create' => 'users.view',
            'users.edit' => 'users.view',
            'users.delete' => 'users.view',
        ];
    }

    /** @param array<int|string> $permissionIds */
    public function normalizePermissionIds(array $permissionIds): array
    {
        $allowed = array_keys(self::managedPermissions());
        $ids = Permission::query()
            ->whereIn('id', collect($permissionIds)->filter()->map(static fn ($id): int => (int) $id)->unique())
            ->whereIn('slug', $allowed)
            ->where('is_active', true)
            ->get(['id', 'slug']);

        $slugs = $ids->pluck('slug')->all();

        foreach (self::viewPermissionForActions() as $action => $view) {
            if (in_array($action, $slugs, true) && ! in_array($view, $slugs, true)) {
                $slugs[] = $view;
            }
        }

        return Permission::query()
            ->whereIn('slug', $slugs)
            ->where('is_active', true)
            ->pluck('id')
            ->map(static fn ($id): int => (int) $id)
            ->values()
            ->all();
    }

    /** @param array<int|string> $permissionIds */
    public function sync(Role $role, array $permissionIds): void
    {
        $role->permissions()->sync($this->normalizePermissionIds($permissionIds));
    }

    public static function resourceViewPermissions(): array
    {
        return ['projects.view', 'applications.view', 'environments.view', 'subscriptions.view'];
    }
}
