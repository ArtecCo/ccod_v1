<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('azure_security_scores', function (Blueprint $table): void {
            $table->id();
            $table->string('subscription_id', 100);
            $table->string('initiative_name', 255)->nullable();
            $table->decimal('current_score', 12, 4)->default(0);
            $table->decimal('max_score', 12, 4)->default(0);
            $table->decimal('percentage_score', 8, 5)->default(0);
            $table->unsignedBigInteger('weight')->default(0);
            $table->timestamp('captured_at');
            $table->timestamp('synced_at')->nullable();

            $table->index('subscription_id', 'sec_score_subscription_idx');
            $table->index(['subscription_id', 'captured_at'], 'sec_score_subscription_captured_idx');
            $table->unique(['subscription_id', 'initiative_name', 'captured_at'], 'sec_score_snapshot_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_security_scores');
    }
};
