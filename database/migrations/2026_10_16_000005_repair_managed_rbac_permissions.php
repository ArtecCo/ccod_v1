<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['dashboard.view', 'Open home route', 'Dashboard'],
            ['documentation.view', 'View documents', 'Documentation'],
            ['documentation.create', 'Create documents', 'Documentation'],
            ['documentation.edit', 'Edit documents', 'Documentation'],
            ['documentation.delete', 'Delete documents', 'Documentation'],
            ['subscriptions.view', 'View subscription', 'Subscriptions'],
            ['subscriptions.create', 'Create subscription', 'Subscriptions'],
            ['subscriptions.edit', 'Edit subscription', 'Subscriptions'],
            ['subscriptions.delete', 'Delete subscription', 'Subscriptions'],
            ['subscriptions.sync', 'Manual Sync', 'Subscriptions'],
            ['alerts.view', 'View alerts', 'Alerts'],
            ['alerts.export', 'Export alerts', 'Alerts'],
            ['alerts.sync', 'Manual Sync', 'Alerts'],
            ['projects.view', 'View projects', 'Projects'],
            ['projects.create', 'Create projects', 'Projects'],
            ['projects.edit', 'Update/Edit projects', 'Projects'],
            ['projects.delete', 'Delete projects', 'Projects'],
            ['resources.view', 'View resources', 'Resources'],
            ['resources.export', 'Export resources', 'Resources'],
            ['resources.sync', 'Manual Sync', 'Resources'],
            ['security.view', 'View security', 'Security'],
            ['security.export', 'Export security', 'Security'],
            ['security.sync', 'Manual Sync', 'Security'],
            ['tickets.view', 'View tickets', 'Tickets'],
            ['tickets.create', 'Create tickets', 'Tickets'],
            ['tickets.update', 'Update/Edit tickets', 'Tickets'],
            ['tickets.assign', 'Assign tickets', 'Tickets'],
            ['tickets.close', 'Close tickets', 'Tickets'],
            ['tickets.export', 'Export tickets', 'Tickets'],
            ['urls.view', 'View URLs', 'URLs'],
            ['urls.create', 'Add URL', 'URLs'],
            ['urls.edit', 'Update/Edit URL', 'URLs'],
            ['urls.delete', 'Delete URL', 'URLs'],
            ['urls.export', 'Export URLs', 'URLs'],
            ['roles.view', 'View roles and permissions', 'Roles & Permissions'],
            ['roles.create', 'Create roles', 'Roles & Permissions'],
            ['roles.edit', 'Update/Edit roles', 'Roles & Permissions'],
            ['roles.delete', 'Delete roles', 'Roles & Permissions'],
            ['users.view', 'View users', 'Users'],
            ['users.create', 'Create users', 'Users'],
            ['users.edit', 'Update/Edit users', 'Users'],
            ['users.delete', 'Delete users', 'Users'],
        ];

        $now = now();

        foreach ($permissions as [$slug, $name, $category]) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'category' => $category,
                    'description' => null,
                    'is_active' => true,
                    'updated_at' => $now,
                ],
            );
        }

        $managedSlugs = array_column($permissions, 0);
        $managedIds = DB::table('permissions')
            ->whereIn('slug', $managedSlugs)
            ->pluck('id');

        $adminRoleIds = DB::table('roles')
            ->whereIn('slug', ['admin', 'super_admin'])
            ->pluck('id');

        foreach ($adminRoleIds as $roleId) {
            foreach ($managedIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Intentionally retain these permissions. They are part of the active RBAC catalog.
    }
};
