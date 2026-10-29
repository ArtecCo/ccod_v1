<?php

declare(strict_types=1);

use App\Services\AzureSecurityScoreSyncService;
use App\Services\AzureSecuritySyncService;
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

fwrite(STDOUT, "Synchronizing Azure security data...\n");

try {
    /** @var AzureSecurityScoreSyncService $scores */
    $scores = $app->make(AzureSecurityScoreSyncService::class);
    /** @var AzureSecuritySyncService $assessments */
    $assessments = $app->make(AzureSecuritySyncService::class);

    $scoreStats = $scores->sync(null);
    $assessmentStats = $assessments->sync(null);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Security synchronization failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, PHP_EOL . "Secure score\n");
foreach ($scoreStats as $key => $value) {
    fwrite(STDOUT, str_pad(ucfirst((string) $key), 15) . ': ' . $value . PHP_EOL);
}

fwrite(STDOUT, PHP_EOL . "Unhealthy assessments\n");
foreach ($assessmentStats as $key => $value) {
    fwrite(STDOUT, str_pad(ucfirst((string) $key), 15) . ': ' . $value . PHP_EOL);
}

fwrite(STDOUT, PHP_EOL . "Azure security synchronization completed.\n");
exit(0);
