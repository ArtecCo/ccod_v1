<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('azure_security_assessments', function (Blueprint $table): void {
            // Existing indexes cover subscription + state + severity and
            // subscription + state + status_changed_at. The Security overview
            // additionally filters by state and aggregates MAX(synced_at).
            $table->index(
                ['subscription_id', 'state', 'synced_at'],
                'idx_az_sec_sub_state_synced',
            );
        });
    }

    public function down(): void
    {
        Schema::table('azure_security_assessments', function (Blueprint $table): void {
            $table->dropIndex('idx_az_sec_sub_state_synced');
        });
    }
};
