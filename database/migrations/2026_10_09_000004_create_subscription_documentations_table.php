<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_documentations', function (Blueprint $table): void {
    $table->collation('utf8mb4_general_ci');
            $table->id();
            $table->string('subscription_id', 100)->unique();
            $table->json('blocks')->nullable();
            $table->timestamps();

            $table->foreign('subscription_id')
                ->references('subscription_id')
                ->on('azure_subscriptions')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_documentations');
    }
};
