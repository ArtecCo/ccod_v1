<?php

namespace App\Console\Commands;

use App\Models\AzureSubscription;
use App\Models\SubscriptionWebUrl;
use App\Services\SubscriptionWebUrlMonitor;
use Illuminate\Console\Command;

class CheckSubscriptionWebUrls extends Command
{
    protected $signature = 'subscriptions:check-web-urls {subscription? : Azure subscription ID}';

    protected $description = 'Check active web URLs for one subscription or all subscriptions';

    public function handle(SubscriptionWebUrlMonitor $monitor): int
    {
        $query = SubscriptionWebUrl::query()->where('is_active', true);

        if ($this->argument('subscription') !== null) {
            $subscriptionId = $this->argument('subscription');

            if (! AzureSubscription::query()->whereKey($subscriptionId)->exists()) {
                $this->error("Azure subscription [{$subscriptionId}] was not found.");

                return self::FAILURE;
            }

            $query->where('subscription_id', $subscriptionId);
        }

        $urls = $query->orderBy('id')->get();

        if ($urls->isEmpty()) {
            $this->info('No active web URLs found.');

            return self::SUCCESS;
        }

        foreach ($urls as $url) {
            $monitor->check($url);

            $status = $url->is_live ? 'LIVE' : 'DOWN';
            $http = $url->http_status ?? '—';
            $this->line("{$status} | {$http} | {$url->url}");
        }

        return self::SUCCESS;
    }
}
