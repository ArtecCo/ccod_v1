<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consolidated_cost_histories', function (Blueprint $table): void {
            $table->id();
            $table->char('scope_hash', 64);
            $table->string('scope_filter', 100)->default('all');
            $table->date('period_start');
            $table->decimal('actual_cost', 20, 6)->nullable();
            $table->decimal('budget_total', 20, 6)->nullable();
            $table->decimal('forecast_total', 20, 6)->nullable();
            $table->decimal('mom_difference', 20, 6)->nullable();
            $table->decimal('mom_percentage', 12, 4)->nullable();
            $table->string('currency', 3)->nullable();
            $table->unsignedInteger('subscription_count')->default(0);
            $table->timestamp('captured_at')->nullable();
            $table->timestamps();

            $table->unique(['scope_hash', 'period_start'], 'consolidated_cost_scope_period_unique');
            $table->index(['scope_hash', 'period_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consolidated_cost_histories');
    }
};
