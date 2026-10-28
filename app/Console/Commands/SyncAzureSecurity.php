<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AzureSecurityScoreControlSyncService;
use App\Services\AzureSecurityScoreSyncService;
use App\Services\AzureSecuritySyncService;
use Illuminate\Console\Command;

class SyncAzureSecurity extends Command
{
    protected $signature = 'azure:sync-security {--subscription= : Sync only one Azure subscription}';

    protected $description = 'Synchronize Defender for Cloud secure scores, score controls, and unhealthy security assessments';

    public function handle(
        AzureSecurityScoreSyncService $scores,
        AzureSecurityScoreControlSyncService $controls,
        AzureSecuritySyncService $assessments,
    ): int {
        $this->info('Synchronizing Azure security data...');

        $scoreStats = $scores->sync($this->option('subscription'));
        $controlStats = $controls->sync($this->option('subscription'));
        $assessmentStats = $assessments->sync($this->option('subscription'));

        $this->newLine();
        $this->info('Secure score');
        foreach ($scoreStats as $key => $value) {
            $this->line(str_pad(ucfirst($key), 15) . ': ' . $value);
        }

        $this->newLine();
        $this->info('Secure score controls');
        foreach ($controlStats as $key => $value) {
            $this->line(str_pad(ucfirst($key), 15) . ': ' . $value);
        }

        $this->newLine();
        $this->info('Unhealthy assessments');
        foreach ($assessmentStats as $key => $value) {
            $this->line(str_pad(ucfirst($key), 15) . ': ' . $value);
        }

        $this->newLine();
        $this->info('Azure security synchronization completed.');

        return self::SUCCESS;
    }
}
