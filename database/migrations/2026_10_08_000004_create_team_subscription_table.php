<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_subscription', function (Blueprint $table): void {
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('subscription_id', 100);
            $table->timestamps();
            $table->primary(['team_id', 'subscription_id']);
            $table->foreign('subscription_id')
                ->references('subscription_id')
                ->on('azure_subscriptions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_subscription');
    }
};
