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

            $roleSlugs = $slug === 'documentation.view'
                ? ['viewer', 'operator', 'customer', 'admin', 'super_admin']
                : ['admin', 'super_admin'];

            $roleIds = DB::table('roles')
                ->whereIn('slug', $roleSlugs)
                ->pluck('id');

            $rows = $roleIds->map(fn ($roleId): array => [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ])->all();

            if ($rows !== []) {
                DB::table('permission_role')->insertOrIgnore($rows);
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
