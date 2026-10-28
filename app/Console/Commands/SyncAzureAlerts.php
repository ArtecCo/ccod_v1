<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AzureAlertSyncService;
use Illuminate\Console\Command;

class SyncAzureAlerts extends Command
{
    protected $signature = 'azure:sync-alerts
                            {--subscription= : Sync only one Azure subscription}';

    protected $description = 'Synchronize active Azure Monitor alerts';

    public function handle(AzureAlertSyncService $sync): int
    {
        $this->info('Synchronizing active Azure Monitor alerts...');

        try {
            $stats = $sync->sync($this->option('subscription'));
        } catch (\Throwable $exception) {
            $this->error('Alert synchronization failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->line('Subscriptions: '.$stats['subscriptions']);
        $this->line('Active alerts discovered: '.$stats['discovered']);
        $this->line('Created: '.$stats['created']);
        $this->line('Updated: '.$stats['updated']);
        $this->line('Unchanged: '.$stats['unchanged']);
        $this->line('Removed from active set: '.$stats['deleted']);

        $this->info('Active Azure alert synchronization completed.');

        return self::SUCCESS;
    }
}
