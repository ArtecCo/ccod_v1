<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AzureSecuritySyncService;
use Illuminate\Console\Command;

class SyncAzureSecurity extends Command
{
    protected $signature = 'azure:sync-security {--subscription= : Sync only one Azure subscription}';

    protected $description = 'Synchronize Microsoft Defender for Cloud security assessments from Azure Resource Graph';

    public function handle(AzureSecuritySyncService $sync): int
    {
        $this->info('Synchronizing Azure security assessments...');

        $stats = $sync->sync($this->option('subscription'));

        foreach ($stats as $key => $value) {
            $this->line(str_pad(ucfirst($key), 15) . ': ' . $value);
        }

        $this->newLine();
        $this->info('Azure security synchronization completed.');

        return self::SUCCESS;
    }
}
