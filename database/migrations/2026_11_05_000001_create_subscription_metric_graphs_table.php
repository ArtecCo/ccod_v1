<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_metric_graphs', function (Blueprint $table): void {
            $table->id();
            $table->string('subscription_id', 36);
            $table->string('resource_id', 255);
            $table->string('title', 160);
            $table->json('series');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index(['subscription_id', 'position']);
            $table->foreign('subscription_id')->references('subscription_id')->on('azure_subscriptions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_metric_graphs');
    }
};
