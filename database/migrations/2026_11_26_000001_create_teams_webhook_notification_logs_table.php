<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams_webhook_notification_logs', function (Blueprint $table): void {
            $table->id();
            // Keep a snapshot so history survives webhook deletion or renaming.
            $table->unsignedBigInteger('webhook_id')->nullable()->index();
            $table->string('webhook_name', 120)->nullable();
            $table->string('event_key', 150)->index();
            $table->string('status', 20)->index(); // success, retrying, failed
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->text('error_message')->nullable();
            $table->boolean('is_test')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams_webhook_notification_logs');
    }
};
