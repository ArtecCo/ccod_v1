<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_rules', function (Blueprint $table): void {
            $table->foreignId('template_id')->nullable()->after('webhook_id')
                ->constrained('teams_webhook_templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('notification_rules', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('template_id');
        });
    }
};
