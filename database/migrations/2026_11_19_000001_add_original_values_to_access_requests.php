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

        // Older pending/rejected requests have not had their requested values overwritten.
        DB::table('access_requests')
            ->whereIn('status', ['pending', 'rejected'])
            ->update([
                'original_duration' => DB::raw('duration'),
                'original_requested_until' => DB::raw('requested_until'),
            ]);
    }

    public function down(): void
    {
        Schema::table('access_requests', function (Blueprint $table): void {
            $table->dropColumn(['original_duration', 'original_requested_until']);
        });
    }
};
