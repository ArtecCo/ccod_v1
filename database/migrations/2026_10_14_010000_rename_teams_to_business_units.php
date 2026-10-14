<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('teams', 'business_units');

        Schema::table('team_user', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'business_unit_id');
        });

        Schema::table('team_subscription', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'business_unit_id');
        });

        Schema::table('access_requests', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'business_unit_id');
        });

        Schema::table('user_access_grants', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'business_unit_id');
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->foreignId('business_unit_id')
                ->nullable()
                ->after('id')
                ->constrained('business_units')
                ->nullOnDelete();
        });

        DB::table('projects')
            ->whereNull('business_unit_id')
            ->whereIn('name', DB::table('business_units')->pluck('name'))
            ->get(['id', 'name'])
            ->each(function (object $project): void {
                $businessUnitId = DB::table('business_units')
                    ->where('name', $project->name)
                    ->value('id');

                if ($businessUnitId !== null) {
                    DB::table('projects')
                        ->where('id', $project->id)
                        ->update(['business_unit_id' => $businessUnitId]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropForeign(['business_unit_id']);
            $table->dropColumn('business_unit_id');
        });

        Schema::table('user_access_grants', function (Blueprint $table): void {
            $table->renameColumn('business_unit_id', 'team_id');
        });

        Schema::table('access_requests', function (Blueprint $table): void {
            $table->renameColumn('business_unit_id', 'team_id');
        });

        Schema::table('team_subscription', function (Blueprint $table): void {
            $table->renameColumn('business_unit_id', 'team_id');
        });

        Schema::table('team_user', function (Blueprint $table): void {
            $table->renameColumn('business_unit_id', 'team_id');
        });

        Schema::rename('business_units', 'teams');
    }
};
