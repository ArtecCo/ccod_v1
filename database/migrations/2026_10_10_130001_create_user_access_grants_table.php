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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('target_type')->default(AccessRequestTargetType::Subscription->value);
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subscription_id')->nullable();
            $table->string('target_name');
            $table->string('role');
            $table->string('granted_by_type');
            $table->unsignedBigInteger('granted_by_id');
            $table->foreignId('source_request_id')->nullable()->constrained('access_requests')->nullOnDelete();
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'target_type']);
            $table->index(['team_id', 'expires_at']);
            $table->index(['subscription_id', 'expires_at']);
            $table->index(['granted_by_type', 'granted_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_access_grants');
    }
};
