<?php

namespace App\Console\Commands;

use App\Services\AzureResourceSyncService;
use Illuminate\Console\Command;

class SyncAzureResources extends Command
{
    protected $signature = 'azure:sync-resources';
    protected $description = 'Discover and synchronize all Azure resources';

    public function handle(AzureResourceSyncService $syncService): int
    {
        $this->info('Azure resource synchronization');
        $this->line('==============================');

        try {
            $stats = $syncService->sync();
        } catch (\Throwable $exception) {
            $this->error('Synchronization failed: ' . $exception->getMessage());
            return self::FAILURE;
        }

        foreach (['subscriptions', 'discovered', 'created', 'updated', 'unchanged'] as $key) {
            $this->line(ucfirst($key) . ': ' . $stats[$key]);
        }

        return self::SUCCESS;
    }
}
