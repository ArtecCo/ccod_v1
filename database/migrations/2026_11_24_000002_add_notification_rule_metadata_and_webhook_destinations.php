<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notification_rules', function (Blueprint $table): void {
            $table->string('rule_title', 150)->nullable();
            $table->text('rule_description')->nullable();
        });

        Schema::create('notification_rule_webhook', function (Blueprint $table): void {
            $table->foreignId('notification_rule_id')->constrained('notification_rules')->cascadeOnDelete();
            $table->foreignId('teams_webhook_id')->constrained('teams_webhooks')->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['notification_rule_id', 'teams_webhook_id'], 'notification_rule_webhook_primary');
        });

        $legacyAssignments = DB::table('notification_rules')
            ->whereNotNull('webhook_id')
            ->get(['id', 'webhook_id'])
            ->map(fn ($rule): array => [
                'notification_rule_id' => $rule->id,
                'teams_webhook_id' => $rule->webhook_id,
                'created_at' => now(),
                'updated_at' => now(),
            ])
            ->all();

        foreach (array_chunk($legacyAssignments, 500) as $chunk) {
            DB::table('notification_rule_webhook')->insertOrIgnore($chunk);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_rule_webhook');
        Schema::table('notification_rules', function (Blueprint $table): void {
            $table->dropColumn(['rule_title', 'rule_description']);
        });
    }
};
