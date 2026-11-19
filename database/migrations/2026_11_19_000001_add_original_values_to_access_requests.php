<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('access_requests', function (Blueprint $table): void {
            $table->string('original_duration')->nullable()->after('duration');
            $table->timestamp('original_requested_until')->nullable()->after('requested_until');
        });
    }

    public function down(): void
    {
        Schema::table('access_requests', function (Blueprint $table): void {
            $table->dropColumn(['original_duration', 'original_requested_until']);
        });
    }
};
