<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('notification_rules', 'template_id')) {
            Schema::table('notification_rules', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('template_id');
            });
        }

        Schema::dropIfExists('teams_webhook_templates');
    }

    public function down(): void
    {
        // Template records are intentionally retired and cannot be reconstructed.
    }
};
