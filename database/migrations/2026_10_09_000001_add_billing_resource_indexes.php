<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing_resources', function (Blueprint $table): void {
            $table->index(
                ['subscription_id', 'name'],
                'billing_resources_subscription_name_index'
            );

            $table->index(
                ['subscription_id', 'resource_type'],
                'billing_resources_subscription_type_index'
            );

            $table->index(
                ['subscription_id', 'region'],
                'billing_resources_subscription_region_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('billing_resources', function (Blueprint $table): void {
            $table->dropIndex('billing_resources_subscription_name_index');
            $table->dropIndex('billing_resources_subscription_type_index');
            $table->dropIndex('billing_resources_subscription_region_index');
        });
    }
};
