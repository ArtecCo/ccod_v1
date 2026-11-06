<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('azure_subscription_costs', function (Blueprint $table): void {
            // Dashboard cost queries filter by subscription + granularity,
            // then sort by period_start. The existing unique index orders
            // period_start before granularity and cannot serve that access path.
            $table->index(
                ['subscription_id', 'granularity', 'period_start'],
                'idx_az_cost_sub_gran_period',
            );
        });

        Schema::table('azure_security_assessments', function (Blueprint $table): void {
            // Supports per-subscription unhealthy/high-severity aggregates and
            // the latest-sync lookup used by the Security overview.
            $table->index(
                ['subscription_id', 'state', 'severity', 'synced_at'],
                'idx_az_sec_sub_state_sev_sync',
            );
        });
    }

    public function down(): void
    {
        Schema::table('azure_security_assessments', function (Blueprint $table): void {
            $table->dropIndex('idx_az_sec_sub_state_sev_sync');
        });

        Schema::table('azure_subscription_costs', function (Blueprint $table): void {
            $table->dropIndex('idx_az_cost_sub_gran_period');
        });
    }
};
