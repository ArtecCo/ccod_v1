<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_module_syncs', function (Blueprint $table): void {
            $table->id();
            $table->string('subscription_id', 100);
            $table->string('module', 40);
            $table->string('status', 20)->default('pending');
            $table->dateTime('last_started_at')->nullable();
            $table->dateTime('last_successful_at')->nullable();
            $table->dateTime('last_failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
            $table->unique(['subscription_id', 'module']);
            $table->index(['module', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_module_syncs');
    }
};
