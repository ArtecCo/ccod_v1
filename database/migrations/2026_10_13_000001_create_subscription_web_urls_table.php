<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_web_urls', function (Blueprint $table): void {
            $table->id();
            $table->string('subscription_id', 100);
            $table->string('url', 2048);
            $table->char('url_hash', 64);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_live')->nullable();
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->boolean('ssl_status')->nullable();
            $table->string('ssl_provider')->nullable();
            $table->string('ssl_subject')->nullable();
            $table->string('ssl_issuer')->nullable();
            $table->timestamp('ssl_valid_from')->nullable();
            $table->timestamp('ssl_expires_at')->nullable();
            $table->integer('ssl_days_remaining')->nullable();
            $table->timestamp('last_checked_at')->nullable();
            $table->text('last_check_error')->nullable();
            $table->timestamps();

            $table->unique(['subscription_id', 'url_hash']);
            $table->index(['subscription_id', 'is_active']);
            $table->foreign('subscription_id')
                ->references('subscription_id')
                ->on('azure_subscriptions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_web_urls');
    }
};
