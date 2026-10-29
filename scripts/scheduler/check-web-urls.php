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
    $urls = SubscriptionWebUrl::query()->where('is_active', true)->orderBy('id')->get();

    if ($urls->isEmpty()) {
        fwrite(STDOUT, "No active web URLs found.\n");
        exit(0);
    }

    foreach ($urls as $url) {
        $monitor->check($url);
        $status = $url->is_live ? 'LIVE' : 'DOWN';
        $http = $url->http_status ?? '—';
        fwrite(STDOUT, "{$status} | {$http} | {$url->url}\n");
    }
} catch (Throwable $exception) {
    fwrite(STDERR, 'Web URL check failed: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

exit(0);
