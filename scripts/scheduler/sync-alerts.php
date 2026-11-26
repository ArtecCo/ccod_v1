<?php

declare(strict_types=1);

use App\Services\AzureAlertSyncService;
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
    /** @var AzureAlertSyncService $sync */
    $sync = $app->make(AzureAlertSyncService::class);
    $stats = $sync->sync(null);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Alert synchronization failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, 'Subscriptions: ' . ($stats['subscriptions'] ?? 0) . PHP_EOL);
fwrite(STDOUT, 'Azure alerts discovered (Fired + Resolved): ' . ($stats['discovered'] ?? 0) . PHP_EOL);
fwrite(STDOUT, 'Created: ' . ($stats['created'] ?? 0) . PHP_EOL);
fwrite(STDOUT, 'Updated: ' . ($stats['updated'] ?? 0) . PHP_EOL);
fwrite(STDOUT, 'Unchanged Fired alerts: ' . ($stats['unchanged'] ?? 0) . PHP_EOL);
fwrite(STDOUT, 'Resolved alerts processed: ' . ($stats['resolved'] ?? 0) . PHP_EOL);
fwrite(STDOUT, 'Deleted local alert records: ' . ($stats['deleted'] ?? 0) . PHP_EOL);

exit(0);
