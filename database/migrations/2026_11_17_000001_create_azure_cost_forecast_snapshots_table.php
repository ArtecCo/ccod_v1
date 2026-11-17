<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('azure_cost_forecast_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->string('subscription_id', 100);
            $table->date('forecast_period');
            $table->decimal('forecast_amount', 20, 6)->default(0);
            $table->string('currency', 3)->nullable();
            $table->date('captured_on');
            $table->timestamp('captured_at');
            $table->timestamp('synced_at')->nullable();

            $table->index(['subscription_id', 'captured_on'], 'cost_forecast_snapshot_retention_idx');
            $table->index(['subscription_id', 'forecast_period'], 'cost_forecast_snapshot_period_idx');
            $table->unique(
                ['subscription_id', 'forecast_period', 'captured_on'],
                'cost_forecast_snapshot_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_cost_forecast_snapshots');
    }
};
