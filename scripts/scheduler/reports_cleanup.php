<?php

declare(strict_types=1);

use App\Models\ReportExport;
use Illuminate\Foundation\Bootstrap\BootProviders;
use Illuminate\Foundation\Bootstrap\HandleExceptions;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;
use Illuminate\Foundation\Bootstrap\RegisterFacades;
use Illuminate\Foundation\Bootstrap\RegisterProviders;

$basePath = dirname(__DIR__, 2);
define('LARAVEL_START', microtime(true));
require $basePath.'/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require $basePath.'/bootstrap/app.php';
$app->bootstrapWith([
    LoadEnvironmentVariables::class,
    LoadConfiguration::class,
    HandleExceptions::class,
    RegisterFacades::class,
    RegisterProviders::class,
    BootProviders::class,
]);

$disk = Illuminate\Support\Facades\Storage::disk('local');
$cutoff = now();
$deletedFiles = 0;
$deletedReports = 0;
$errors = [];

ReportExport::query()
    ->whereNotNull('expires_at')
    ->where('expires_at', '<=', $cutoff)
    ->orderBy('id')
    ->chunkById(100, function ($reports) use ($disk, &$deletedFiles, &$deletedReports, &$errors): void {
        foreach ($reports as $report) {
            $directory = 'reports/'.$report->developer_id.'/'.$report->getKey();

            try {
                // Only delete paths belonging to this report's dedicated private directory.
                if ($disk->exists($directory)) {
                    $disk->deleteDirectory($directory);
                }

                $report->update([
                    'files' => null,
                    'status' => 'expired',
                ]);

                $deletedFiles++;
                $deletedReports++;
            } catch (Throwable $exception) {
                $errors[] = 'Report #'.$report->getKey().': '.$exception->getMessage();
            }
        }
    });

$logDirectory = $basePath.'/storage/logs';
if (! is_dir($logDirectory)) {
    mkdir($logDirectory, 0775, true);
}
$logPath = $logDirectory.'/reports-cleanup.log';
$lines = [
    '['.now()->toDateTimeString().'] Expired reports removed: '.$deletedReports,
    '['.now()->toDateTimeString().'] Private report directories removed: '.$deletedFiles,
];
foreach ($errors as $error) {
    $lines[] = '['.now()->toDateTimeString().'] ERROR '.$error;
}
file_put_contents($logPath, implode(PHP_EOL, $lines).PHP_EOL, FILE_APPEND | LOCK_EX);

fwrite(STDOUT, implode(PHP_EOL, $lines).PHP_EOL);
exit($errors === [] ? 0 : 1);
