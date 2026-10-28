<?php

namespace App\Console\Commands;

use App\Models\AzureSubscription;
use App\Services\AzureBudgetSyncService;
use App\Services\AzureCostForecastSyncService;
use App\Services\AzureCostSyncService;
use App\Services\AzureResourceCostSyncService;
use Illuminate\Console\Command;

class SyncAzureCosts extends Command
{
    protected $signature = 'azure:sync-costs
                            {--subscription= : Sync one subscription ID only}
                            {--historical : Force refresh of the rolling 12-month history}';

    protected $description = 'Synchronize Azure costs, budgets, resource costs and forecast';

    public function handle(
        AzureCostSyncService $costSync,
        AzureBudgetSyncService $budgetSync,
        AzureResourceCostSyncService $resourceCostSync,
        AzureCostForecastSyncService $forecastSync,
    ): int {
        $query = AzureSubscription::query();

        if ($subscriptionId = $this->option('subscription')) {
            $query->where('subscription_id', $subscriptionId);
        }

        $subscriptions = $query->get();
        if ($subscriptions->isEmpty()) {
            $this->warn('No Azure subscriptions found to synchronize.');
            return self::SUCCESS;
        }

        $failed = false;

        foreach ($subscriptions as $subscription) {
            $this->line(sprintf('Syncing %s (%s)...', $subscription->display_name, $subscription->subscription_id));

            try {
                $result = $costSync->syncSubscription($subscription, (bool) $this->option('historical'));
                $this->info(sprintf(
                    '  Cost: %s sync complete: %d month(s), MTD %s %s, source rows %d',
                    strtoupper($result['mode']),
                    $result['months'],
                    $result['current_mtd'] === null ? '—' : number_format((float) $result['current_mtd'], 2),
                    $result['currency'] ?? '',
                    $result['source_rows'] ?? 0,
                ));
            } catch (\Throwable $exception) {
                $failed = true;
                $this->error('  Cost failed: '.$exception->getMessage());
            }

            try {
                $count = $budgetSync->sync($subscription);
                $this->info("  Budgets: {$count} synchronized.");
            } catch (\Throwable $exception) {
                $failed = true;
                $this->error('  Budget failed: '.$exception->getMessage());
            }

            try {
                $count = $resourceCostSync->sync($subscription);
                $this->info("  Resource costs: {$count} resources updated.");
            } catch (\Throwable $exception) {
                $failed = true;
                $this->error('  Resource cost failed: '.$exception->getMessage());
            }

            try {
                $synced = $forecastSync->sync($subscription);
                $this->info('  Forecast: '.($synced ? 'synchronized.' : 'no forecast value returned.'));
            } catch (\Throwable $exception) {
                $failed = true;
                $this->error('  Forecast failed: '.$exception->getMessage());
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
