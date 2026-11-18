<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_module_syncs', function (Blueprint $table): void {
            $table->dateTime('last_partial_at')->nullable()->after('last_failed_at');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_module_syncs', function (Blueprint $table): void {
            $table->dropColumn('last_partial_at');
        });
    }
};
