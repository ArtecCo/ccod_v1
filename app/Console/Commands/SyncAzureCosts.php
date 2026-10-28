<?php

namespace App\Console\Commands;

use App\Models\AzureSubscription;
use App\Services\AzureCostSyncService;
use Illuminate\Console\Command;

class SyncAzureCosts extends Command
{
    protected $signature = 'azure:sync-costs
                            {--subscription= : Sync one subscription ID only}
                            {--historical : Force refresh of the rolling 12-month history}';

    protected $description = 'Synchronize Azure subscription cost history and current MTD spend';

    public function handle(AzureCostSyncService $syncService): int
    {
        $query = AzureSubscription::query();

        if ($subscriptionId = $this->option('subscription')) {
            $query->where('subscription_id', $subscriptionId);
        }

        $subscriptions = $query->get();

        if ($subscriptions->isEmpty()) {
            $this->warn('No Azure subscriptions found to synchronize.');
            return self::SUCCESS;
        }

        foreach ($subscriptions as $subscription) {
            $this->line(sprintf(
                'Syncing %s (%s)...',
                $subscription->display_name,
                $subscription->subscription_id,
            ));

            try {
                $result = $syncService->syncSubscription(
                    $subscription,
                    (bool) $this->option('historical'),
                );

                $this->info(sprintf(
                    '  %s sync complete: %d month(s), MTD %s %s',
                    strtoupper($result['mode']),
                    $result['months'],
                    $result['current_mtd'] === null ? '—' : number_format((float) $result['current_mtd'], 2),
                    $result['currency'] ?? '',
                ));
            } catch (\Throwable $exception) {
                $this->error(sprintf(
                    '  Failed: %s',
                    $exception->getMessage(),
                ));
            }
        }

        return self::SUCCESS;
    }
}
