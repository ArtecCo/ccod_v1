<?php

use App\Services\RolePermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $managed = array_keys(RolePermissionService::managedPermissions());

        $rolePermissions = [
            'viewer' => [
                'projects.view',
                'subscriptions.view',
                'cost.view',
                'cost.breakdown.view',
                'resources.view',
                'alerts.view',
                'tickets.view',
                'urls.view',
                'security.view',
            ],
            'operator' => [
                'projects.view',
                'subscriptions.view',
                'cost.view',
                'cost.breakdown.view',
                'cost.export',
                'resources.view',
                'resources.export',
                'alerts.view',
                'tickets.view',
                'tickets.create',
                'tickets.update',
                'tickets.assign',
                'tickets.close',
                'urls.view',
                'security.view',
            ],
            'customer' => [
                'projects.view',
                'subscriptions.view',
                'cost.view',
                'cost.breakdown.view',
                'resources.view',
                'alerts.view',
                'tickets.view',
                'tickets.create',
                'urls.view',
                'security.view',
            ],
            'admin' => $managed,
            'super_admin' => $managed,
        ];

        foreach ($rolePermissions as $roleSlug => $slugs) {
            $roleId = DB::table('roles')->where('slug', $roleSlug)->value('id');

            if (! $roleId) {
                continue;
            }

            $permissionIds = DB::table('permissions')
                ->whereIn('slug', $slugs)
                ->where('is_active', true)
                ->pluck('id');

            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Permission assignments are retained so rolling back application code
        // cannot silently remove access from built-in roles.
    }
};
