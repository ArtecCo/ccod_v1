<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('azure_subscriptions', function (Blueprint $table): void {
            $table->foreignId('application_id')
                ->nullable()
                ->after('display_name')
                ->constrained('applications')
                ->nullOnDelete();

            $table->string('environment', 20)->nullable()->after('application_id');

            $table->index(['application_id', 'environment'], 'azure_subscriptions_application_environment_index');
            $table->unique(
                ['application_id', 'environment'],
                'azure_subscriptions_application_environment_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('azure_subscriptions', function (Blueprint $table): void {
            $table->dropUnique('azure_subscriptions_application_environment_unique');
            $table->dropForeign(['application_id']);
            $table->dropIndex('azure_subscriptions_application_environment_index');
            $table->dropColumn('application_id');
            $table->dropColumn('environment');
        });
    }
};
