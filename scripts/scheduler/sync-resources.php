<?php

declare(strict_types=1);

use App\Services\AzureResourceSyncService;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;

$basePath = dirname(__DIR__, 2);

if (! defined('LARAVEL_START')) {
    define('LARAVEL_START', microtime(true));
}

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

fwrite(STDOUT, "Azure resource synchronization\n");
fwrite(STDOUT, "==============================\n");

try {
    /** @var AzureResourceSyncService $syncService */
    $syncService = $app->make(AzureResourceSyncService::class);
    $stats = $syncService->sync();
} catch (Throwable $exception) {
    fwrite(STDERR, 'Synchronization failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

foreach (['subscriptions', 'discovered', 'created', 'updated', 'unchanged'] as $key) {
    fwrite(STDOUT, ucfirst($key) . ': ' . ($stats[$key] ?? 0) . PHP_EOL);
}

exit(0);
