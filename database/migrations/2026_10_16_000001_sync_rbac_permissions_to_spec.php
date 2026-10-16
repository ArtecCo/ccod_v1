<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['Dashboard', 'Open home route', 'dashboard.view', 'Whether the user can see the home page'],
            ['Documentation', 'View documents', 'documentation.view', 'Whether the user can view documentation'],
            ['Documentation', 'Create documents', 'documentation.create', 'Whether the user can create documentation'],
            ['Documentation', 'Edit documents', 'documentation.edit', 'Whether the user can edit documentation'],
            ['Documentation', 'Delete documents', 'documentation.delete', 'Whether the user can delete documentation'],
            ['Subscriptions', 'Create subscription', 'subscriptions.create', 'Whether the user can create a subscription'],
            ['Subscriptions', 'View subscription', 'subscriptions.view', 'Whether the user can view the subscriptions administration list'],
            ['Subscriptions', 'Edit subscription', 'subscriptions.edit', 'Whether the user can edit subscription metadata'],
            ['Subscriptions', 'Delete subscription', 'subscriptions.delete', 'Whether the user can delete a subscription'],
            ['Subscriptions', 'Manual Sync', 'subscriptions.sync', 'Whether the user can trigger subscription synchronization'],
            ['Alerts', 'View alerts', 'alerts.view', 'Whether the user can view alerts'],
            ['Alerts', 'Export alerts', 'alerts.export', 'Whether the user can export alerts'],
            ['Alerts', 'Manual Sync', 'alerts.sync', 'Whether the user can trigger alert synchronization'],
            ['Projects', 'View projects', 'projects.view', 'Whether the user can view projects'],
            ['Projects', 'Create projects', 'projects.create', 'Whether the user can create projects'],
            ['Projects', 'Update/Edit projects', 'projects.edit', 'Whether the user can edit projects'],
            ['Projects', 'Delete projects', 'projects.delete', 'Whether the user can delete projects'],
            ['Resources', 'View resources', 'resources.view', 'Whether the user can view resources'],
            ['Resources', 'Export resources', 'resources.export', 'Whether the user can export resources'],
            ['Resources', 'Manual Sync', 'resources.sync', 'Whether the user can trigger resource synchronization'],
            ['Security', 'View security', 'security.view', 'Whether the user can view security information'],
            ['Security', 'Export security', 'security.export', 'Whether the user can export security recommendations'],
            ['Security', 'Manual Sync', 'security.sync', 'Whether the user can trigger security synchronization'],
            ['Tickets', 'View tickets', 'tickets.view', 'Whether the user can view tickets'],
            ['Tickets', 'Create tickets', 'tickets.create', 'Whether the user can create tickets'],
            ['Tickets', 'Update/Edit tickets', 'tickets.update', 'Whether the user can update tickets'],
            ['Tickets', 'Assign tickets', 'tickets.assign', 'Whether the user can assign tickets'],
            ['Tickets', 'Close tickets', 'tickets.close', 'Whether the user can close tickets'],
            ['Tickets', 'Export tickets', 'tickets.export', 'Whether the user can export tickets'],
            ['URLs', 'View URLs', 'urls.view', 'Whether the user can view monitored URLs'],
            ['URLs', 'Add URL', 'urls.create', 'Whether the user can add URLs'],
            ['URLs', 'Update/Edit URL', 'urls.edit', 'Whether the user can edit URLs'],
            ['URLs', 'Delete URL', 'urls.delete', 'Whether the user can delete URLs'],
            ['URLs', 'Export URLs', 'urls.export', 'Whether the user can export URLs'],
            ['Roles & Permissions', 'View roles and permissions', 'roles.view', 'Whether the user can view roles and permissions'],
            ['Roles & Permissions', 'Create roles', 'roles.create', 'Whether the user can create roles'],
            ['Roles & Permissions', 'Update/Edit roles', 'roles.edit', 'Whether the user can edit roles'],
            ['Roles & Permissions', 'Delete roles', 'roles.delete', 'Whether the user can delete roles'],
            ['Users', 'View users', 'users.view', 'Whether the user can view users'],
            ['Users', 'Create users', 'users.create', 'Whether the user can create users'],
            ['Users', 'Update/Edit users', 'users.edit', 'Whether the user can edit users'],
            ['Users', 'Delete users', 'users.delete', 'Whether the user can delete users'],
        ];

        foreach ($permissions as [$category, $name, $slug, $description]) {
            DB::table('permissions')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'category' => $category,
                    'description' => $description,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }

        // These permissions are required by existing access-management functionality,
        // but are intentionally not exposed in the custom-role permission form.
        $internalPermissions = [
            'subscriptions.assign_environment',
            'subscriptions.assign_business_unit',
            'users.roles.assign',
            'users.scopes.assign',
            'roles.permissions.assign',
            'roles.scopes.assign',
            'system.settings.view',
            'system.settings.manage',
            'system.integrations.manage',
            'system.audit.manage',
            'system.configuration.manage',
            'authorization.configuration.manage',
            'authorization.system_roles.manage',
            'authorization.security.manage',
        ];

        DB::table('permissions')
            ->whereIn('slug', $internalPermissions)
            ->update(['is_active' => true, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('slug', [
                'subscriptions.sync',
                'alerts.export',
                'alerts.sync',
                'resources.sync',
                'security.export',
                'security.sync',
                'urls.export',
                'users.delete',
            ])
            ->delete();
    }
};
