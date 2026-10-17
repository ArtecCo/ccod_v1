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
            'documentation.view' => ['category' => 'Documentation', 'name' => 'View documents'],
            'documentation.create' => ['category' => 'Documentation', 'name' => 'Create documents'],
            'documentation.edit' => ['category' => 'Documentation', 'name' => 'Edit documents'],
            'documentation.delete' => ['category' => 'Documentation', 'name' => 'Delete documents'],
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
            'cost.view' => ['category' => 'Cost', 'name' => 'View cost'],
            'cost.breakdown.view' => ['category' => 'Cost', 'name' => 'View cost breakdown'],
            'cost.export' => ['category' => 'Cost', 'name' => 'Export cost'],
            'cost.settings.manage' => ['category' => 'Cost', 'name' => 'Manage cost settings'],
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
            'users.access_requests.view' => ['category' => 'Access Requests', 'name' => 'View access requests'],
            'users.access_requests.decide' => ['category' => 'Access Requests', 'name' => 'Take decision'],
        ];
    }

    public static function hiddenSystemPermissions(): array
    {
        return [
            'dashboard.view',
            'subscriptions.assign_environment', 'subscriptions.assign_business_unit',
            'users.scopes.assign', 'roles.permissions.assign', 'roles.scopes.assign',
            'system.settings.view', 'system.settings.manage', 'system.integrations.manage', 'system.audit.manage',
            'system.configuration.manage', 'system.audit.manage', 'authorization.configuration.manage',
            'authorization.system_roles.manage', 'authorization.security.manage',
            'applications.view', 'applications.create', 'applications.edit', 'applications.delete',
            'environments.view', 'environments.create', 'environments.edit', 'environments.delete',
            'monitoring.history.view', 'monitoring.checks.trigger', 'alerts.acknowledge', 'alerts.close',
            'alerts.settings.manage', 'urls.check.trigger', 'urls.history.view', 'security.scores.view',
            'security.settings.manage', 'dashboard.metrics.view', 'dashboard.widgets.view', 'dashboard.export',
            'resources.metrics.view', 'tickets.disable',
        ];
    }

    /** @return array<string, string> */
    public static function viewPermissionForActions(): array
    {
        return [
            'documentation.create' => 'documentation.view', 'documentation.edit' => 'documentation.view', 'documentation.delete' => 'documentation.view',
            'subscriptions.edit' => 'subscriptions.view', 'subscriptions.delete' => 'subscriptions.view', 'subscriptions.sync' => 'subscriptions.view',
            'cost.breakdown.view' => 'cost.view', 'cost.export' => 'cost.view', 'cost.settings.manage' => 'cost.view',
            'alerts.export' => 'alerts.view', 'alerts.sync' => 'alerts.view',
            'projects.create' => 'projects.view', 'projects.edit' => 'projects.view', 'projects.delete' => 'projects.view',
            'resources.export' => 'resources.view', 'resources.sync' => 'resources.view',
            'security.export' => 'security.view', 'security.sync' => 'security.view',
            'tickets.create' => 'tickets.view', 'tickets.update' => 'tickets.view', 'tickets.assign' => 'tickets.view', 'tickets.close' => 'tickets.view', 'tickets.export' => 'tickets.view',
            'urls.create' => 'urls.view', 'urls.edit' => 'urls.view', 'urls.delete' => 'urls.view', 'urls.export' => 'urls.view',
            'roles.create' => 'roles.view', 'roles.edit' => 'roles.view', 'roles.delete' => 'roles.view',
            'users.create' => 'users.view', 'users.edit' => 'users.view', 'users.delete' => 'users.view',
            'users.access_requests.decide' => 'users.access_requests.view',
        ];
    }

    /** @return array<int, string> */
    public function effectiveManagedPermissionSlugs(Role $role): array
    {
        $managedSlugs = array_keys(self::managedPermissions());

        if ($role->slug === 'super_admin') {
            return $managedSlugs;
        }

        return $role->permissions()
            ->whereIn('permissions.slug', $managedSlugs)
            ->where('permissions.is_active', true)
            ->pluck('permissions.slug')
            ->map(static fn ($slug): string => (string) $slug)
            ->values()
            ->all();
    }

    /** @param array<int|string>|null $permissionValues */
    public function syncManagedCategory(Role $role, string $category, ?array $permissionValues): void
    {
        $managed = self::managedPermissions();
        $categorySlugs = collect($managed)->filter(fn (array $definition): bool => $definition['category'] === $category)->keys()->values();
        $selectedIds = collect($permissionValues ?? [])->map(fn ($value): int => (int) $value)->filter(fn (int $value): bool => $value > 0)->unique()->values();
        $selectedSlugs = Permission::query()->whereIn('id', $selectedIds->all())->whereIn('slug', $categorySlugs->all())->where('is_active', true)->pluck('slug')->all();
        foreach (self::viewPermissionForActions() as $action => $view) {
            if (in_array($action, $selectedSlugs, true) && $categorySlugs->contains($view)) $selectedSlugs[] = $view;
        }
        $selectedSlugs = array_values(array_unique($selectedSlugs));
        foreach ($selectedSlugs as $slug) {
            Permission::query()->updateOrCreate(['slug' => $slug], ['name' => $managed[$slug]['name'], 'category' => $managed[$slug]['category'], 'is_active' => true, 'updated_at' => now()]);
        }
        $categoryPermissionIds = Permission::query()->whereIn('slug', $categorySlugs->all())->pluck('id')->all();
        $selectedPermissionIds = Permission::query()->whereIn('slug', $selectedSlugs)->where('is_active', true)->pluck('id')->all();
        $role->permissions()->detach($categoryPermissionIds);
        $role->permissions()->syncWithoutDetaching($selectedPermissionIds);
    }

    /** @param array<int|string> $permissionValues */
    public function normalizePermissionIds(array $permissionValues): array
    {
        $managed = self::managedPermissions();
        $allowed = array_merge(array_keys($managed), self::hiddenSystemPermissions());
        $values = collect($permissionValues)->filter(fn ($value) => $value !== null && $value !== '')->unique()->values();
        $selectedSlugs = $values->filter(fn ($value) => is_string($value) && ! ctype_digit($value))->intersect($allowed)->values()->all();
        $selectedIds = $values->filter(fn ($value) => is_int($value) || (is_string($value) && ctype_digit($value)))->map(fn ($value): int => (int) $value)->unique()->values()->all();
        $existingById = Permission::query()->whereIn('id', $selectedIds)->whereIn('slug', $allowed)->where('is_active', true)->get(['id', 'slug']);
        $selectedSlugs = array_values(array_unique(array_merge($selectedSlugs, $existingById->pluck('slug')->all())));
        foreach ($selectedSlugs as $slug) {
            if (! isset($managed[$slug])) continue;
            Permission::query()->updateOrCreate(['slug' => $slug], ['name' => $managed[$slug]['name'], 'category' => $managed[$slug]['category'], 'is_active' => true, 'updated_at' => now()]);
        }
        foreach (self::viewPermissionForActions() as $action => $view) {
            if (in_array($action, $selectedSlugs, true) && ! in_array($view, $selectedSlugs, true)) $selectedSlugs[] = $view;
        }
        return Permission::query()->whereIn('slug', array_values(array_unique($selectedSlugs)))->where('is_active', true)->pluck('id')->map(static fn ($id): int => (int) $id)->values()->all();
    }

    public function sync(Role $role, array $permissionValues): void
    {
        $role->permissions()->sync($this->normalizePermissionIds($permissionValues));
    }

    public static function resourceViewPermissions(): array
    {
        return ['projects.view', 'applications.view', 'environments.view', 'subscriptions.view'];
    }
}
