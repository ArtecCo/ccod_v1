<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('event_key')->unique();
            $table->boolean('enabled')->default(false);
            $table->string('recipient_type');
            $table->json('recipient_ids')->nullable();
            $table->string('title', 150)->nullable();
            $table->text('message')->nullable();
            $table->string('type')->default('general');
            $table->string('severity')->default('info');
            $table->string('action_label', 80)->nullable();
            $table->string('action_url', 2048)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('developers')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('developers')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_rules');
    }
};
