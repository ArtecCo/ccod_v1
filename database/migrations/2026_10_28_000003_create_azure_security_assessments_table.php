<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('azure_security_assessments', function (Blueprint $table): void {
            $table->id();
            $table->string('assessment_id', 1024);
            $table->string('subscription_id', 100);
            $table->string('resource_id', 1024)->nullable();
            $table->string('recommendation_id', 255)->nullable();
            $table->string('recommendation_name', 1024)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('severity', 50)->nullable();
            $table->text('description')->nullable();
            $table->text('remediation_description')->nullable();
            $table->string('assessment_type', 100)->nullable();
            $table->string('implementation_effort', 100)->nullable();
            $table->string('resource_source', 100)->nullable();
            $table->string('policy_definition_id', 1024)->nullable();
            $table->json('categories')->nullable();
            $table->json('threats')->nullable();
            $table->string('user_impact', 50)->nullable();
            $table->string('status_cause', 255)->nullable();
            $table->text('status_description')->nullable();
            $table->dateTime('first_evaluated_at')->nullable();
            $table->dateTime('status_changed_at')->nullable();
            $table->string('portal_link', 2048)->nullable();
            $table->timestamp('synced_at')->nullable();

            $table->index('subscription_id');
            $table->index(['subscription_id', 'state']);
            $table->index(['subscription_id', 'severity']);
            $table->index(['subscription_id', 'status_changed_at']);
            $table->index('assessment_id', 'assessment_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('azure_security_assessments');
    }
};
