<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection(config('activitylog.database_connection'))
            ->table(config('activitylog.table_name', 'activity_log'), function (Blueprint $table): void {
                // Activity subjects can use either integer IDs or UUIDs.
                // Store the polymorphic ID as a string so UUID-backed models
                // such as Laravel's DatabaseNotification are supported.
                $table->char('subject_id', 36)->nullable()->change();
            });
    }

    public function down(): void
    {
        Schema::connection(config('activitylog.database_connection'))
            ->table(config('activitylog.table_name', 'activity_log'), function (Blueprint $table): void {
                $table->unsignedBigInteger('subject_id')->nullable()->change();
            });
    }
};
