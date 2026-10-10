<?php

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Enums\AccessRequestTargetType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('target_type')->default(AccessRequestTargetType::Subscription->value);
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subscription_id')->nullable();
            $table->string('target_name');
            $table->string('requested_role');
            $table->text('reason');
            $table->string('duration')->default(AccessRequestDuration::Permanent->value);
            $table->timestamp('requested_until')->nullable();
            $table->string('status')->default(AccessRequestStatus::Pending->value);
            $table->string('decided_by_type')->nullable();
            $table->unsignedBigInteger('decided_by_id')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('decision_reason')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['target_type', 'team_id']);
            $table->index(['target_type', 'subscription_id']);
            $table->index(['decided_by_type', 'decided_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_requests');
    }
};
