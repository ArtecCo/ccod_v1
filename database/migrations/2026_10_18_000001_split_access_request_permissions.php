<?php

use App\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            $view = Permission::query()->updateOrCreate(
                ['slug' => 'users.access_requests.view'],
                ['name' => 'View access requests', 'category' => 'Access Requests', 'is_active' => true]
            );

            $decide = Permission::query()->updateOrCreate(
                ['slug' => 'users.access_requests.decide'],
                ['name' => 'Take decision', 'category' => 'Access Requests', 'is_active' => true]
            );

            $legacy = Permission::query()->where('slug', 'users.roles.assign')->first();

            if ($legacy !== null) {
                $roleIds = DB::table('permission_role')
                    ->where('permission_id', $legacy->getKey())
                    ->pluck('role_id');

                foreach ($roleIds as $roleId) {
                    DB::table('permission_role')->insertOrIgnore([
                        ['permission_id' => $view->getKey(), 'role_id' => $roleId],
                        ['permission_id' => $decide->getKey(), 'role_id' => $roleId],
                    ]);
                }

                $legacy->forceFill(['is_active' => false])->save();
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            $legacy = Permission::query()->where('slug', 'users.roles.assign')->first();
            $view = Permission::query()->where('slug', 'users.access_requests.view')->first();
            $decide = Permission::query()->where('slug', 'users.access_requests.decide')->first();

            if ($legacy !== null) {
                $legacy->forceFill([
                    'name' => 'Manage access requests',
                    'category' => 'Roles & Permissions',
                    'is_active' => true,
                ])->save();
            }

            if ($view !== null) {
                DB::table('permission_role')->where('permission_id', $view->getKey())->delete();
                $view->delete();
            }

            if ($decide !== null) {
                DB::table('permission_role')->where('permission_id', $decide->getKey())->delete();
                $decide->delete();
            }
        });
    }
};
