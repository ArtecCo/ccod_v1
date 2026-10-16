<?php

use App\Services\RolePermissionService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $rules = RolePermissionService::viewPermissionForActions();

        foreach ($rules as $actionSlug => $viewSlug) {
            $actionId = DB::table('permissions')->where('slug', $actionSlug)->value('id');
            $viewId = DB::table('permissions')->where('slug', $viewSlug)->value('id');

            if (! $actionId || ! $viewId) continue;

            $roleIds = DB::table('permission_role')->where('permission_id', $actionId)->pluck('role_id');

            foreach ($roleIds as $roleId) {
                DB::table('permission_role')->insertOrIgnore([
                    'role_id' => $roleId,
                    'permission_id' => $viewId,
                ]);
            }
        }
    }

    public function down(): void
    {
        // View permissions are intentionally retained because they may have been
        // assigned explicitly after this migration ran.
    }
};
