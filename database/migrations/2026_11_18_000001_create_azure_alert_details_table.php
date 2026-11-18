<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('azure_alert_details', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('alert_id')->constrained('azure_alerts')->cascadeOnDelete()->unique();
            $table->string('subscription_id', 100)->index();
            $table->json('essentials')->nullable();
            $table->json('alert_context')->nullable();
            $table->json('raw_properties')->nullable();
            $table->timestamp('fetched_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_alert_details');
    }
};
