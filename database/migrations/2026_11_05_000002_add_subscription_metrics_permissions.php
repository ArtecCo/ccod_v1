<?php

use App\Services\RolePermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['resources.metrics.view', 'resources.metrics.manage'] as $slug) {
            $definition = RolePermissionService::managedPermissions()[$slug];
            DB::table('permissions')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $definition['name'],
                    'category' => $definition['category'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ],
            );
        }

        $permissionIds = DB::table('permissions')
            ->whereIn('slug', ['resources.metrics.view', 'resources.metrics.manage'])
            ->where('is_active', true)
            ->pluck('id');

        foreach (DB::table('roles')->whereIn('slug', ['admin', 'super_admin'])->pluck('id') as $roleId) {
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
        // Keep permission assignments on rollback to avoid unexpectedly removing access.
    }
};
