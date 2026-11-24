<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ssl_reminder_rules', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('threshold_days');
            $table->string('severity', 20);
            $table->json('webhook_ids');
            $table->boolean('enabled')->default(true)->index();
            $table->timestamps();
            $table->unique('threshold_days');
        });

        Schema::create('ssl_reminder_deliveries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ssl_reminder_rule_id')->constrained('ssl_reminder_rules')->cascadeOnDelete();
            $table->foreignId('subscription_web_url_id')->constrained('subscription_web_urls')->cascadeOnDelete();
            $table->dateTime('certificate_expires_at');
            $table->timestamp('dispatched_at')->useCurrent();
            $table->timestamps();
            $table->unique(
                ['ssl_reminder_rule_id', 'subscription_web_url_id', 'certificate_expires_at'],
                'ssl_reminder_delivery_once_per_certificate',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ssl_reminder_deliveries');
        Schema::dropIfExists('ssl_reminder_rules');
    }
};
