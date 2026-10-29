<?php

declare(strict_types=1);

use App\Models\AzureSubscription;
use App\Services\AzureBudgetSyncService;
use App\Services\AzureCostForecastSyncService;
use App\Services\AzureCostSyncService;
use App\Services\AzureResourceCostSyncService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;

$basePath = dirname(__DIR__, 2);
define('LARAVEL_START', microtime(true));
require $basePath . '/vendor/autoload.php';

/** @var Application $app */
$app = require $basePath . '/bootstrap/app.php';
$app->bootstrapWith([
    LoadEnvironmentVariables::class,
    LoadConfiguration::class,
    HandleExceptions::class,
    RegisterFacades::class,
    RegisterProviders::class,
    BootProviders::class,
]);

try {
    $costSync = $app->make(AzureCostSyncService::class);
    $budgetSync = $app->make(AzureBudgetSyncService::class);
    $resourceCostSync = $app->make(AzureResourceCostSyncService::class);
    $forecastSync = $app->make(AzureCostForecastSyncService::class);

    $subscriptions = AzureSubscription::query()->get();

    if ($subscriptions->isEmpty()) {
        fwrite(STDOUT, "No Azure subscriptions found to synchronize.\n");
        exit(0);
    }

    $failed = false;

    foreach ($subscriptions as $subscription) {
        fwrite(STDOUT, sprintf("Syncing %s (%s)...\n", $subscription->display_name, $subscription->subscription_id));

        try {
            $result = $costSync->syncSubscription($subscription, false);
            fwrite(STDOUT, sprintf(
                "  Cost: %s sync complete: %d month(s), MTD %s %s, source rows %d\n",
                strtoupper($result['mode']),
                $result['months'],
                $result['current_mtd'] === null ? '—' : number_format((float) $result['current_mtd'], 2),
                $result['currency'] ?? '',
                $result['source_rows'] ?? 0,
            ));
        } catch (Throwable $exception) {
            $failed = true;
            fwrite(STDERR, '  Cost failed: ' . $exception->getMessage() . PHP_EOL);
        }

        try {
            $count = $budgetSync->sync($subscription);
            fwrite(STDOUT, "  Budgets: {$count} synchronized.\n");
        } catch (Throwable $exception) {
            $failed = true;
            fwrite(STDERR, '  Budget failed: ' . $exception->getMessage() . PHP_EOL);
        }

        try {
            $count = $resourceCostSync->sync($subscription);
            fwrite(STDOUT, "  Resource costs: {$count} resources updated.\n");
        } catch (Throwable $exception) {
            $failed = true;
            fwrite(STDERR, '  Resource cost failed: ' . $exception->getMessage() . PHP_EOL);
        }

        try {
            $synced = $forecastSync->sync($subscription);
            fwrite(STDOUT, '  Forecast: ' . ($synced ? 'synchronized.' : 'no forecast value returned.') . PHP_EOL);
        } catch (Throwable $exception) {
            $failed = true;
            fwrite(STDERR, '  Forecast failed: ' . $exception->getMessage() . PHP_EOL);
        }
    }
} catch (Throwable $exception) {
    fwrite(STDERR, 'Cost synchronization failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

exit($failed ? 1 : 0);
