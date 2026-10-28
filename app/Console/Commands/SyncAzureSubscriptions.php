<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncAzureSubscriptions extends Command
{
    protected $signature = 'azure:sync-subscriptions';
    protected $description = 'Synchronize Azure subscriptions';
}
