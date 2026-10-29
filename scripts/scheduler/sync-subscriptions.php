<?php

declare(strict_types=1);

use App\Services\AzureSubscriptionSyncService;
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

fwrite(STDOUT, "Azure subscription synchronization\n================================\n");

try {
    /** @var AzureSubscriptionSyncService $sync */
    $sync = $app->make(AzureSubscriptionSyncService::class);
    $stats = $sync->sync();
} catch (Throwable $exception) {
    fwrite(STDERR, 'Synchronization failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

foreach (['discovered', 'created', 'updated', 'unchanged', 'failed'] as $key) {
    fwrite(STDOUT, ucfirst($key) . ': ' . ($stats[$key] ?? 0) . PHP_EOL);
}

exit(($stats['failed'] ?? 0) > 0 ? 1 : 0);
