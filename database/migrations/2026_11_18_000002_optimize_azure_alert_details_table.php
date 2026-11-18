<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('azure_alert_details')) {
            return;
        }

        Schema::table('azure_alert_details', function (Blueprint $table): void {
            if (Schema::hasColumn('azure_alert_details', 'subscription_id')) {
                $table->dropIndex(['subscription_id']);
            }
        });

        Schema::table('azure_alert_details', function (Blueprint $table): void {
            foreach (['essentials', 'alert_context', 'subscription_id'] as $column) {
                if (Schema::hasColumn('azure_alert_details', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('azure_alert_details')) {
            return;
        }

        Schema::table('azure_alert_details', function (Blueprint $table): void {
            if (! Schema::hasColumn('azure_alert_details', 'subscription_id')) {
                $table->string('subscription_id', 100)->nullable()->index();
            }
            if (! Schema::hasColumn('azure_alert_details', 'essentials')) {
                $table->json('essentials')->nullable();
            }
            if (! Schema::hasColumn('azure_alert_details', 'alert_context')) {
                $table->json('alert_context')->nullable();
            }
        });
    }
};
