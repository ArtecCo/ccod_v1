<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('azure_alert_notification_events', function (Blueprint $table): void {
            $table->id();
            $table->char('alert_id_hash', 64);
            $table->text('alert_id');
            $table->string('event_key', 100);
            $table->timestamp('processed_at');
            $table->timestamps();
            $table->unique(['alert_id_hash', 'event_key'], 'azure_alert_notification_events_alert_event_unique');
            $table->index('processed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_alert_notification_events');
    }
};
