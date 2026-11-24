<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('ssl_reminder_rules') || ! Schema::hasColumn('ssl_reminder_rules', 'webhook_ids')) {
            return;
        }

        $now = now();
        $rules = DB::table('ssl_reminder_rules')->get(['threshold_days', 'severity', 'webhook_ids']);
        $created = [];

        foreach ($rules as $rule) {
            $webhookIds = is_array($rule->webhook_ids)
                ? $rule->webhook_ids
                : json_decode((string) $rule->webhook_ids, true);

            if (! is_array($webhookIds)) {
                continue;
            }

            $severity = match (strtolower((string) $rule->severity)) {
                'critical' => 'CRITICAL',
                'warning' => 'WARNING',
                'attention', 'reminder' => 'ATTENTION',
                default => null,
            };

            if ($severity === null) {
                continue;
            }

            foreach (array_unique(array_filter($webhookIds, 'is_numeric')) as $webhookId) {
                $webhookId = (int) $webhookId;

                if (! DB::table('teams_webhooks')->where('id', $webhookId)->exists()) {
                    continue;
                }

                $key = $webhookId.'|'.$severity.'|'.(int) $rule->threshold_days;
                if (isset($created[$key])) {
                    continue;
                }

                $conditions = [
                    ['field' => 'severity', 'operator' => 'equals', 'value_text' => $severity],
                    ['field' => 'ssl_reminder_threshold_days', 'operator' => 'equals', 'value_text' => (string) $rule->threshold_days],
                ];
                $encodedConditions = json_encode($conditions);

                $existingRules = DB::table('notification_rules')
                    ->where('event_key', 'web_url.ssl.expiring')
                    ->where('channel', 'webhook')
                    ->where('webhook_id', $webhookId)
                    ->get(['id', 'conditions']);

                $alreadyConfigured = $existingRules->contains(function ($existing) use ($conditions): bool {
                    $existingConditions = is_array($existing->conditions)
                        ? $existing->conditions
                        : json_decode((string) $existing->conditions, true);

                    return $existingConditions === $conditions;
                });

                if (! $alreadyConfigured) {
                    DB::table('notification_rules')->insert([
                        'event_key' => 'web_url.ssl.expiring',
                        'channel' => 'webhook',
                        'conditions' => $encodedConditions,
                        'condition_mode' => 'all',
                        'webhook_id' => $webhookId,
                        'enabled' => true,
                        'recipient_type' => 'all',
                        'recipient_ids' => null,
                        'type' => 'alert',
                        'severity' => 'warning',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                $created[$key] = true;
            }
        }

        // Keep the legacy JSON column for rollback and old application versions.
        // New application code no longer reads it for delivery.
    }

    public function down(): void
    {
        // The migrated notification rules are retained so rollback does not
        // remove destinations that may have been edited since this migration.
    }
};
