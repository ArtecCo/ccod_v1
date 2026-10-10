<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_subscription_access_overrides', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subscription_id');
            $table->string('override')->default('revoked');
            $table->string('revoked_by_type')->nullable();
            $table->unsignedBigInteger('revoked_by_id')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'subscription_id']);
            $table->index(['subscription_id', 'override']);
            $table->index(['revoked_by_type', 'revoked_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_subscription_access_overrides');
    }
};
