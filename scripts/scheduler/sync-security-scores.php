<?php

declare(strict_types=1);

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

use App\Services\AzureSecurityScoreSyncService;

$startedAt = microtime(true);
fwrite(STDOUT, "Synchronizing Azure security scores...\n");

try {
    /** @var AzureSecurityScoreSyncService $sync */
    $sync = $app->make(AzureSecurityScoreSyncService::class);
    $stats = $sync->sync(null);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Security score synchronization failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

foreach ($stats as $key => $value) {
    fwrite(STDOUT, str_pad(ucfirst((string) $key), 15) . ': ' . $value . PHP_EOL);
}

fwrite(STDOUT, sprintf("Completed in %.2f seconds.\n", microtime(true) - $startedAt));
exit(0);
