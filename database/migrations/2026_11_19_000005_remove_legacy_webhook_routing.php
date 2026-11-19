<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams_webhooks', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('template_id');
            $table->dropIndex(['category']);
            $table->dropColumn([
                'category',
                'event_keys',
                'conditions',
                'condition_mode',
                'title_template',
                'message_template',
            ]);
        });

        Schema::table('teams_webhook_templates', function (Blueprint $table): void {
            $table->dropIndex(['category', 'is_default']);
            $table->dropIndex(['category']);
            $table->dropColumn(['category', 'is_default']);
        });

        Schema::dropIfExists('teams_webhook_categories');
    }

    public function down(): void
    {
        // Removed legacy routing and category configuration is intentionally not restored.
    }
};
