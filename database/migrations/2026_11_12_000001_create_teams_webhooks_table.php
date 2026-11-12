<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teams_webhooks', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 120);
            $table->string('category', 40)->index();
            $table->string('destination_type', 20);
            $table->string('message_format', 30)->default('adaptive_card');
            $table->text('webhook_url');
            $table->string('title_template', 200)->nullable();
            $table->text('message_template');
            $table->boolean('enabled')->default(true)->index();
            $table->string('last_delivery_status', 20)->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams_webhooks');
    }
};
