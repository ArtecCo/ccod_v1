<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'user_access_grants',
            'user_subscription_access_overrides',
            'user_role_assignments',
            'user_resource_access',
        ] as $table) {
            if (Schema::hasTable($table)) {
                Schema::drop($table);
            }
        }

        Schema::table('access_requests', function (Blueprint $table): void {
            if (Schema::hasColumn('access_requests', 'business_unit_id')) {
                $table->dropForeign(['business_unit_id']);
                $table->dropColumn('business_unit_id');
            }

            if (Schema::hasColumn('access_requests', 'subscription_id')) {
                $table->dropForeign(['subscription_id']);
                $table->dropColumn('subscription_id');
            }
        });
    }

    public function down(): void
    {
        throw new \RuntimeException('The legacy direct-access schema is intentionally not restorable.');
    }
};
