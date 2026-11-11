<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('project_subscriptions', 'environment')) {
            Schema::table('project_subscriptions', function (Blueprint $table): void {
                $table->dropColumn('environment');
            });
        }

        if (Schema::hasColumn('azure_subscriptions', 'environment')) {
            $indexes = collect(DB::select('SHOW INDEX FROM azure_subscriptions'))
                ->pluck('Key_name')
                ->unique()
                ->all();

            Schema::table('azure_subscriptions', function (Blueprint $table) use ($indexes): void {
                if (in_array('azure_subscriptions_application_environment_unique', $indexes, true)) {
                    $table->dropUnique('azure_subscriptions_application_environment_unique');
                }

                if (in_array('azure_subscriptions_application_environment_index', $indexes, true)) {
                    $table->dropIndex('azure_subscriptions_application_environment_index');
                }

                $table->dropColumn('environment');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('project_subscriptions', 'environment')) {
            Schema::table('project_subscriptions', function (Blueprint $table): void {
                $table->string('environment', 20)->nullable();
            });
        }

        if (! Schema::hasColumn('azure_subscriptions', 'environment')) {
            Schema::table('azure_subscriptions', function (Blueprint $table): void {
                $table->string('environment', 20)->nullable();
            });
        }
    }
};
