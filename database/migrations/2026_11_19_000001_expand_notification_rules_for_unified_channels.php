<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_rules', function (Blueprint $table): void {
            $table->dropUnique(['event_key']);
            $table->index('event_key');
            $table->string('channel', 20)->default('in_app')->after('event_key');
            $table->json('conditions')->nullable()->after('recipient_ids');
            $table->string('condition_mode', 10)->default('all')->after('conditions');
            $table->foreignId('webhook_id')->nullable()->after('condition_mode')
                ->constrained('teams_webhooks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('notification_rules', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('webhook_id');
            $table->dropIndex(['event_key']);
            $table->dropColumn(['channel', 'conditions', 'condition_mode']);
            $table->unique('event_key');
        });
    }
};
