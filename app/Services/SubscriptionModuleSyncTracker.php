<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SubscriptionModuleSyncTracker
{
    public function begin(string $module, array $subscriptionIds): void
    {
        $now = now();

        foreach ($subscriptionIds as $subscriptionId) {
            DB::table('subscription_module_syncs')->updateOrInsert(
                ['subscription_id' => $subscriptionId, 'module' => $module],
                ['status' => 'running', 'last_started_at' => $now, 'error_message' => null, 'updated_at' => $now, 'created_at' => $now],
            );
        }
    }

    public function succeed(string $module, array $subscriptionIds): void
    {
        $now = now();

        DB::table('subscription_module_syncs')
            ->where('module', $module)
            ->whereIn('subscription_id', $subscriptionIds)
            ->update(['status' => 'success', 'last_successful_at' => $now, 'error_message' => null, 'updated_at' => $now]);
    }

    public function fail(string $module, array $subscriptionIds, string $message): void
    {
        $now = now();

        DB::table('subscription_module_syncs')
            ->where('module', $module)
            ->whereIn('subscription_id', $subscriptionIds)
            ->update(['status' => 'failed', 'last_failed_at' => $now, 'error_message' => mb_substr($message, 0, 4000), 'updated_at' => $now]);
    }

    public function latest(string $subscriptionId, string $module): ?object
    {
        return DB::table('subscription_module_syncs')
            ->where('subscription_id', $subscriptionId)
            ->where('module', $module)
            ->first();
    }
}
