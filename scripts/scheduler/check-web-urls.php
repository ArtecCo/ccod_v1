<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;
use App\Models\SubscriptionWebUrl;
use App\Services\SubscriptionWebUrlMonitor;

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


$startedAt = microtime(true);

try {
    /** @var SubscriptionWebUrlMonitor $monitor */
    $monitor = $app->make(SubscriptionWebUrlMonitor::class);
    $syncTracker = $app->make(\App\Services\SubscriptionModuleSyncTracker::class);
    $query = SubscriptionWebUrl::query()->where('is_active', true);
    $trackedSubscriptionIds = (clone $query)->whereNotNull('subscription_id')->distinct()->pluck('subscription_id')->all();
    if ($trackedSubscriptionIds !== []) {
        $syncTracker->begin('web_urls', $trackedSubscriptionIds);
    }
    $total = (clone $query)->count();

    if ($total === 0) {
        fwrite(STDOUT, "No active web URLs found.\n");
        exit(0);
    }

    $projects = [];
    $unassigned = 0;
    $processed = 0;
    $live = 0;
    $down = 0;
    $checkErrors = 0;
    $concurrency = max(1, min(25, (int) (getenv('CCOD_URL_CHECK_CONCURRENCY') ?: 10)));

    fwrite(STDOUT, sprintf("Pinging %d active web URLs (concurrency: %d)...\n", $total, $concurrency));

    $query->with('subscription.projects:id,name')->orderBy('id')->chunkById(50, function ($urls) use ($monitor, &$projects, &$unassigned, &$processed, &$live, &$down, $total, $concurrency): void {
        $monitor->checkAvailabilityMany($urls, $concurrency);

        foreach ($urls as $url) {
            $projectNames = $url->subscription?->projects?->pluck('name')->filter()->values()->all() ?? [];
            if ($projectNames === []) {
                $unassigned++;
            }

            foreach ($projectNames as $projectName) {
                $projects[$projectName] ??= ['checked' => 0, 'live' => 0, 'down' => 0];
                $projects[$projectName]['checked']++;
                $url->is_live ? $projects[$projectName]['live']++ : $projects[$projectName]['down']++;
            }

            $url->is_live ? $live++ : $down++;
            if ($url->last_check_error !== null && $url->last_check_error !== '') {
                $checkErrors++;
            }
            $processed++;
        }

        fwrite(STDOUT, sprintf("[%d/%d] URLs pinged | Live: %d | Down: %d\n", $processed, $total, $live, $down));
    });

    ksort($projects, SORT_NATURAL | SORT_FLAG_CASE);
    fwrite(STDOUT, PHP_EOL . "URL availability by project\n");
    fwrite(STDOUT, str_repeat('-', 70) . PHP_EOL);
    fwrite(STDOUT, sprintf("%-40s %8s %8s %8s\n", 'Project', 'Checked', 'Live', 'Down'));
    fwrite(STDOUT, str_repeat('-', 70) . PHP_EOL);

    foreach ($projects as $projectName => $counts) {
        fwrite(STDOUT, sprintf("%-40s %8d %8d %8d\n", mb_strimwidth($projectName, 0, 40, '…'), $counts['checked'], $counts['live'], $counts['down']));
    }

    if ($unassigned > 0) {
        fwrite(STDOUT, str_repeat('-', 70) . PHP_EOL);
        fwrite(STDOUT, sprintf("%-40s %8d\n", 'Unassigned project', $unassigned));
    }

    fwrite(STDOUT, PHP_EOL . sprintf("Total active URLs pinged: %d\n", $processed));
    fwrite(STDOUT, sprintf("PING_SUCCESS: %d\n", $processed));
    fwrite(STDOUT, sprintf("PING_ERRORS: %d\n", $checkErrors));
    // Retain markers consumed by the full sync report for compatibility.
    fwrite(STDOUT, sprintf("CHECK_SUCCESS: %d\n", $processed));
    fwrite(STDOUT, sprintf("CHECK_ERRORS: %d\n", $checkErrors));
    fwrite(STDOUT, sprintf("PING_DURATION_SECONDS: %.2f\n", microtime(true) - $startedAt));
    if ($trackedSubscriptionIds !== []) {
        if ($checkErrors > 0) {
            $syncTracker->partial('web_urls', $trackedSubscriptionIds, "{$checkErrors} URL availability checks reported errors.");
        } else {
            $syncTracker->succeed('web_urls', $trackedSubscriptionIds);
        }
    }
    if ($checkErrors > 0) {
        exit($processed > 0 ? 2 : 1);
    }
} catch (Throwable $exception) {
    if (isset($syncTracker, $trackedSubscriptionIds) && $trackedSubscriptionIds !== []) {
        $syncTracker->fail('web_urls', $trackedSubscriptionIds, $exception->getMessage());
    }
    fwrite(STDERR, 'Web URL ping failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

exit(0);
