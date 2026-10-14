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
        Schema::rename('team_user', 'business_unit_user');
        Schema::rename('team_subscription', 'business_unit_subscription');

        Schema::table('business_unit_user', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'business_unit_id');
        });

        Schema::table('business_unit_subscription', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'business_unit_id');
        });

        Schema::table('access_requests', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'business_unit_id');
        });

        Schema::table('user_access_grants', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'business_unit_id');
        });

        Schema::table('access_grant_revocations', function (Blueprint $table): void {
            $table->renameColumn('team_id', 'business_unit_id');
        });

        Schema::table('projects', function (Blueprint $table): void {
            $table->foreignId('business_unit_id')->nullable()->after('id')->constrained('business_units')->nullOnDelete();
            $table->index('business_unit_id');
        });

        DB::table('projects')
            ->where('name', 'MA-BDO')
            ->update(['business_unit_id' => DB::table('business_units')->where('name', 'MA-BDO')->value('id')]);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropForeign(['business_unit_id']);
            $table->dropIndex(['business_unit_id']);
            $table->dropColumn('business_unit_id');
        });

        Schema::table('access_grant_revocations', function (Blueprint $table): void {
            $table->renameColumn('business_unit_id', 'team_id');
        });

        Schema::table('user_access_grants', function (Blueprint $table): void {
            $table->renameColumn('business_unit_id', 'team_id');
        });

        Schema::table('access_requests', function (Blueprint $table): void {
            $table->renameColumn('business_unit_id', 'team_id');
        });

        Schema::table('business_unit_subscription', function (Blueprint $table): void {
            $table->renameColumn('business_unit_id', 'team_id');
        });

        Schema::table('business_unit_user', function (Blueprint $table): void {
            $table->renameColumn('business_unit_id', 'team_id');
        });

        Schema::rename('business_unit_subscription', 'team_subscription');
        Schema::rename('business_unit_user', 'team_user');
        Schema::rename('business_units', 'teams');
    }
};
