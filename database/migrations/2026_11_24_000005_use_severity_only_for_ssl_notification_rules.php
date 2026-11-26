<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('notification_rules')
            ->where('event_key', 'web_url.ssl.expiring')
            ->where('channel', 'webhook')
            ->orderBy('id')
            ->get(['id', 'conditions'])
            ->each(function ($rule): void {
                $conditions = is_array($rule->conditions)
                    ? $rule->conditions
                    : json_decode((string) $rule->conditions, true);

                if (! is_array($conditions)) {
                    return;
                }

                $hasSeverity = collect($conditions)->contains(
                    fn (array $condition): bool => ($condition['field'] ?? null) === 'severity',
                );
                $hasLegacyThreshold = collect($conditions)->contains(
                    fn (array $condition): bool => ($condition['field'] ?? null) === 'ssl_reminder_threshold_days',
                );

                if (! $hasSeverity || ! $hasLegacyThreshold) {
                    return;
                }

                $conditions = array_values(array_filter(
                    $conditions,
                    fn (array $condition): bool => ($condition['field'] ?? null) !== 'ssl_reminder_threshold_days',
                ));

                DB::table('notification_rules')->where('id', $rule->id)->update([
                    'conditions' => json_encode($conditions),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        // Severity-only routing is the intended model; restoring duplicate
        // threshold conditions would reintroduce the old coupling.
    }
};
