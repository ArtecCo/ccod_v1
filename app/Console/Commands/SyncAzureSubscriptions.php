<?php

namespace App\Console\Commands;

use App\Services\AzureSubscriptionSyncService;
use Illuminate\Console\Command;

class SyncAzureSubscriptions extends Command
{
    protected $signature = 'azure:sync-subscriptions';
    protected $description = 'Discover and synchronize Azure subscriptions';

    public function handle(AzureSubscriptionSyncService $syncService): int
    {
        $this->info('Azure subscription synchronization');
        $this->line('================================');

        try {
            $stats = $syncService->sync();
        } catch (\Throwable $exception) {
            $this->error('Synchronization failed: ' . $exception->getMessage());
            return self::FAILURE;
        }

        foreach (['discovered', 'created', 'updated', 'unchanged', 'failed'] as $key) {
            $this->line(ucfirst($key) . ': ' . $stats[$key]);
        }

        return $stats['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
