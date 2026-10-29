<?php

declare(strict_types=1);

use App\Models\SubscriptionWebUrl;
use App\Services\SubscriptionWebUrlMonitor;
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
    /** @var SubscriptionWebUrlMonitor $monitor */
    $monitor = $app->make(SubscriptionWebUrlMonitor::class);
    $urls = SubscriptionWebUrl::query()
        ->where('is_active', true)
        ->with('subscription.projects:id,name')
        ->orderBy('id')
        ->get();

    if ($urls->isEmpty()) {
        fwrite(STDOUT, "No active web URLs found.\n");
        exit(0);
    }

    $projects = [];
    $unassigned = 0;

    foreach ($urls as $url) {
        $monitor->check($url);

        $projectNames = $url->subscription?->projects?->pluck('name')->filter()->values()->all() ?? [];

        if ($projectNames === []) {
            $unassigned++;
            continue;
        }

        foreach ($projectNames as $projectName) {
            if (! isset($projects[$projectName])) {
                $projects[$projectName] = [
                    'checked' => 0,
                    'live' => 0,
                    'down' => 0,
                ];
            }

            $projects[$projectName]['checked']++;
            $url->is_live ? $projects[$projectName]['live']++ : $projects[$projectName]['down']++;
        }
    }

    ksort($projects, SORT_NATURAL | SORT_FLAG_CASE);

    fwrite(STDOUT, PHP_EOL . "Web URL checks by project" . PHP_EOL);
    fwrite(STDOUT, "-------------------------" . PHP_EOL);
    fwrite(STDOUT, sprintf("%-40s %8s %8s %8s\n", 'Project', 'Checked', 'Live', 'Down'));
    fwrite(STDOUT, str_repeat('-', 70) . PHP_EOL);

    foreach ($projects as $projectName => $counts) {
        fwrite(STDOUT, sprintf(
            "%-40s %8d %8d %8d\n",
            mb_strimwidth($projectName, 0, 40, '…'),
            $counts['checked'],
            $counts['live'],
            $counts['down'],
        ));
    }

    if ($unassigned > 0) {
        fwrite(STDOUT, str_repeat('-', 70) . PHP_EOL);
        fwrite(STDOUT, sprintf("%-40s %8d\n", 'Unassigned project', $unassigned));
    }

    fwrite(STDOUT, PHP_EOL . sprintf("Total active links checked: %d\n", $urls->count()));
} catch (Throwable $exception) {
    fwrite(STDERR, 'Web URL check failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

exit(0);
