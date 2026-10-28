<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AzureAlertSyncService;
use Illuminate\Console\Command;

class SyncAzureAlerts extends Command
{
    protected $signature = 'azure:sync-alerts
                            {--subscription= : Sync only one Azure subscription}
                            {--hours=24 : Look back this many hours for modified alerts}';

    protected $description = 'Synchronize Azure Monitor alerts';

    public function handle(AzureAlertSyncService $sync): int
    {
        $hours = (int) $this->option('hours');

        if ($hours < 1) {
            $this->error('The --hours option must be at least 1.');

            return self::FAILURE;
        }

        $this->info('Synchronizing Azure Monitor alerts...');

        try {
            $stats = $sync->sync($this->option('subscription'), $hours);
        } catch (\Throwable $exception) {
            $this->error('Alert synchronization failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->line('Subscriptions: '.$stats['subscriptions']);
        $this->line('Discovered: '.$stats['discovered']);
        $this->line('Created: '.$stats['created']);
        $this->line('Updated: '.$stats['updated']);
        $this->line('Unchanged: '.$stats['unchanged']);

        $this->info('Azure alert synchronization completed.');

        return self::SUCCESS;
    }
}
