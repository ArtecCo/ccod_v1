<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = 'azure_security_assessments';
        $index = 'azure_security_subscription_state_synced_idx';

        if (Schema::hasTable($table) && ! Schema::getConnection()->getSchemaBuilder()->hasIndex($table, $index)) {
            Schema::table($table, function (Blueprint $blueprint) use ($index): void {
                $blueprint->index(['subscription_id', 'state', 'synced_at'], $index);
            });
        }
    }

    public function down(): void
    {
        $table = 'azure_security_assessments';
        $index = 'azure_security_subscription_state_synced_idx';

        if (Schema::hasTable($table) && Schema::getConnection()->getSchemaBuilder()->hasIndex($table, $index)) {
            Schema::table($table, function (Blueprint $blueprint) use ($index): void {
                $blueprint->dropIndex($index);
            });
        }
    }
};
