<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_subscriptions', function (Blueprint $table): void {
            $table->json('tags')->nullable()->after('environment');
            $table->dropUnique('project_subscriptions_project_environment_unique');
            $table->unique(['project_id', 'subscription_id'], 'project_subscriptions_project_subscription_unique');
        });

        DB::table('project_subscriptions')
            ->select(['id', 'environment'])
            ->whereNotNull('environment')
            ->orderBy('id')
            ->get()
            ->each(function (object $assignment): void {
                DB::table('project_subscriptions')
                    ->where('id', $assignment->id)
                    ->update(['tags' => json_encode([(string) $assignment->environment])]);
            });

        Schema::table('project_subscriptions', function (Blueprint $table): void {
            $table->string('environment', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('project_subscriptions', function (Blueprint $table): void {
            $table->dropUnique('project_subscriptions_project_subscription_unique');
            $table->unique(['project_id', 'environment'], 'project_subscriptions_project_environment_unique');
            $table->dropColumn('tags');
        });
    }
};
