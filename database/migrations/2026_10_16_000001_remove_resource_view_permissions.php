<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $resourceViewPermissions = [
        'projects.view',
        'applications.view',
        'environments.view',
        'subscriptions.view',
    ];

    public function up(): void
    {
        $permissionIds = DB::table('permissions')
            ->whereIn('slug', $this->resourceViewPermissions)
            ->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();

            DB::table('permissions')
                ->whereIn('id', $permissionIds)
                ->update(['is_active' => false, 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')
            ->whereIn('slug', $this->resourceViewPermissions)
            ->update(['is_active' => true, 'updated_at' => now()]);
    }
};
