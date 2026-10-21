<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_requests', function (Blueprint $table): void {
            $table->foreignId('user_group_id')
                ->nullable()
                ->after('user_id')
                ->constrained('user_groups')
                ->nullOnDelete();

            $table->index(['target_type', 'user_group_id']);
        });
    }

    public function down(): void
    {
        Schema::table('access_requests', function (Blueprint $table): void {
            $table->dropIndex(['target_type', 'user_group_id']);
            $table->dropConstrainedForeignId('user_group_id');
        });
    }
};
