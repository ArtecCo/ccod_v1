<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['Documentation', 'View documentation', 'documentation.view'],
            ['Documentation', 'Create documentation', 'documentation.create'],
            ['Documentation', 'Edit documentation', 'documentation.edit'],
            ['Documentation', 'Delete documentation', 'documentation.delete'],
        ];

        foreach ($permissions as [$category, $name, $slug]) {
            $permissionId = DB::table('permissions')->where('slug', $slug)->value('id');

            if (! $permissionId) {
                $permissionId = DB::table('permissions')->insertGetId([
                    'name' => $name,
                    'slug' => $slug,
                    'category' => $category,
                    'description' => $name,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($slug === 'documentation.view') {
                DB::table('permission_role')
                    ->whereIn('role_id', DB::table('roles')->whereIn('slug', ['viewer', 'operator', 'customer', 'admin', 'super_admin'])->pluck('id'))
                    ->where('permission_id', $permissionId)
                    ->insertOrIgnore();
            }

            if (in_array($slug, ['documentation.create', 'documentation.edit', 'documentation.delete'], true)) {
                DB::table('permission_role')
                    ->whereIn('role_id', DB::table('roles')->whereIn('slug', ['admin', 'super_admin'])->pluck('id'))
                    ->where('permission_id', $permissionId)
                    ->insertOrIgnore();
            }
        }
    }

    public function down(): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('slug', ['documentation.view', 'documentation.create', 'documentation.edit', 'documentation.delete'])
            ->pluck('id');

        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('permissions')->whereIn('id', $permissionIds)->delete();
    }
};
