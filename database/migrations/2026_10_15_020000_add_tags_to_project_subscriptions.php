<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Keep the migration retry-safe because MySQL may have already
        // committed an earlier DDL statement before a later statement failed.
        if (! Schema::hasColumn('project_subscriptions', 'tags')) {
            Schema::table('project_subscriptions', function (Blueprint $table): void {
                $table->json('tags')->nullable()->after('environment');
            });
        }

        $indexes = DB::select('SHOW INDEX FROM `project_subscriptions`');
        $indexNames = collect($indexes)->pluck('Key_name')->unique()->all();

        // The existing (project_id, environment) unique index is also the
        // supporting index for the project_id foreign key. MySQL will not let
        // us drop it until another project_id-leading index exists.
        if (! in_array('project_subscriptions_project_subscription_unique', $indexNames, true)) {
            Schema::table('project_subscriptions', function (Blueprint $table): void {
                $table->unique(
                    ['project_id', 'subscription_id'],
                    'project_subscriptions_project_subscription_unique'
                );
            });
        }

        $indexes = DB::select('SHOW INDEX FROM `project_subscriptions`');
        $indexNames = collect($indexes)->pluck('Key_name')->unique()->all();

        if (in_array('project_subscriptions_project_environment_unique', $indexNames, true)) {
            Schema::table('project_subscriptions', function (Blueprint $table): void {
                $table->dropUnique('project_subscriptions_project_environment_unique');
            });
        }

        DB::table('project_subscriptions')
            ->select(['id', 'environment'])
            ->whereNotNull('environment')
            ->whereNull('tags')
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
