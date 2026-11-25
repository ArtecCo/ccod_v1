<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('azure_alerts', function (Blueprint $table): void {
            $table->index(
                ['subscription_id', 'fire_time'],
                'azure_alerts_subscription_fire_time_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('azure_alerts', function (Blueprint $table): void {
            $table->dropIndex('azure_alerts_subscription_fire_time_index');
        });
    }
};
