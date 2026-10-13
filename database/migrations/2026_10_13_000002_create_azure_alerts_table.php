<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('azure_alerts', function (Blueprint $table): void {
            $table->id();
            $table->string('azure_alert_id', 1024)->nullable();
            $table->string('name');
            $table->string('severity', 10);
            $table->text('affected_resource')->nullable();
            $table->string('target_resource_type')->nullable();
            $table->string('alert_condition', 50)->nullable();
            $table->string('user_response', 50)->nullable();
            $table->string('monitor_service')->nullable();
            $table->string('signal_type')->nullable();
            $table->dateTime('fire_time')->nullable();
            $table->dateTime('last_modified_time')->nullable();
            $table->string('subscription_id', 100);
            $table->string('target_resource_group')->nullable();
            $table->string('suppression_status', 50)->nullable();
            $table->timestamps();

            $table->unique('azure_alert_id');
            $table->index('subscription_id');
            $table->index('severity');
            $table->index('fire_time');
            $table->index('last_modified_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_alerts');
    }
};
