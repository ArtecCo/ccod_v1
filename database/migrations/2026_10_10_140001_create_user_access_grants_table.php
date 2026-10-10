<?php

use App\Enums\AccessRequestTargetType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_access_grants', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('target_type');
            $table->foreignId('team_id')->nullable()->constrained('teams')->cascadeOnDelete();
            $table->string('subscription_id')->nullable();
            $table->string('target_name');
            $table->string('role');
            $table->string('granted_by_type')->nullable();
            $table->unsignedBigInteger('granted_by_id')->nullable();
            $table->foreignId('source_request_id')->nullable()->constrained('access_requests')->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('subscription_id')
                ->references('subscription_id')
                ->on('azure_subscriptions')
                ->cascadeOnDelete();

            $table->index(['target_type', 'team_id']);
            $table->index(['target_type', 'subscription_id']);
            $table->index(['user_id', 'expires_at']);
            $table->index(['granted_by_type', 'granted_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_access_grants');
    }
};
