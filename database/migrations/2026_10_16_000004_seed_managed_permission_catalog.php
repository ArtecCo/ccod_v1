<?php

use App\Services\RolePermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (RolePermissionService::managedPermissions() as $slug => $definition) {
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
            ->whereIn('slug', array_keys(RolePermissionService::managedPermissions()))
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
        $permissionIds = DB::table('permissions')
            ->whereIn('slug', array_keys(RolePermissionService::managedPermissions()))
            ->pluck('id');

        $roleIds = DB::table('roles')
            ->whereIn('slug', ['admin', 'super_admin'])
            ->pluck('id');

        DB::table('permission_role')
            ->whereIn('role_id', $roleIds)
            ->whereIn('permission_id', $permissionIds)
            ->delete();

        DB::table('permissions')
            ->whereIn('id', $permissionIds)
            ->delete();
    }
};
