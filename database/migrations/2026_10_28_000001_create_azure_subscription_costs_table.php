<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('azure_subscription_costs', function (Blueprint $table): void {
            $table->id();
            $table->string('subscription_id', 36);
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('amount', 20, 6)->default(0);
            $table->string('currency', 3)->nullable();
            $table->string('granularity', 20)->default('monthly');
            $table->string('source', 100)->default('azure_cost_management');
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['subscription_id', 'period_start', 'granularity'],
                'azure_subscription_costs_period_unique'
            );
            $table->index(['subscription_id', 'period_start']);

            $table->foreign('subscription_id')
                ->references('subscription_id')
                ->on('azure_subscriptions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_subscription_costs');
    }
};
