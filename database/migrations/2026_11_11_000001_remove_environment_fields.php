<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('project_subscriptions', 'environment')) {
            Schema::table('project_subscriptions', function (Blueprint $table): void {
                $table->dropColumn('environment');
            });
        }

        if (Schema::hasColumn('azure_subscriptions', 'environment')) {
            Schema::table('azure_subscriptions', function (Blueprint $table): void {
                $table->dropColumn('environment');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('project_subscriptions', 'environment')) {
            Schema::table('project_subscriptions', function (Blueprint $table): void {
                $table->string('environment', 20)->nullable();
            });
        }

        if (! Schema::hasColumn('azure_subscriptions', 'environment')) {
            Schema::table('azure_subscriptions', function (Blueprint $table): void {
                $table->string('environment', 20)->nullable();
            });
        }
    }
};
