<?php

declare(strict_types=1);

/**
 * Unified scheduler runner for Windows Task Scheduler.
 *
 * Runs all six lightweight scheduler scripts sequentially, records the
 * duration and result of each module, and writes a CSV report for every run.
 */

$basePath = dirname(__DIR__, 2);
$schedulerPath = __DIR__;
$reportDirectory = $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'scheduler' . DIRECTORY_SEPARATOR . 'reports';

if (! is_dir($reportDirectory) && ! mkdir($reportDirectory, 0775, true) && ! is_dir($reportDirectory)) {
    fwrite(STDERR, "Unable to create scheduler report directory: {$reportDirectory}" . PHP_EOL);
    exit(1);
}

$runStartedAt = microtime(true);
$runId = date('Ymd-His');
$reportPath = $reportDirectory . DIRECTORY_SEPARATOR . "sync-{$runId}.csv";
$latestReportPath = $reportDirectory . DIRECTORY_SEPARATOR . 'latest.csv';

$modules = [
    ['name' => 'Subscriptions', 'script' => 'sync-subscriptions.php'],
    ['name' => 'Resources', 'script' => 'sync-resources.php'],
    ['name' => 'Costs', 'script' => 'sync-costs.php'],
    ['name' => 'Security', 'script' => 'sync-security.php'],
    ['name' => 'Alerts', 'script' => 'sync-alerts.php'],
    ['name' => 'Web URLs', 'script' => 'check-web-urls.php'],
];

$rows = [];
$anyFailure = false;

fwrite(STDOUT, PHP_EOL . "CCOD unified Azure synchronization" . PHP_EOL);
fwrite(STDOUT, "=================================" . PHP_EOL);
fwrite(STDOUT, "Run ID: {$runId}" . PHP_EOL . PHP_EOL);

foreach ($modules as $module) {
    $scriptPath = $schedulerPath . DIRECTORY_SEPARATOR . $module['script'];
    $startedAt = microtime(true);
    $started = date('Y-m-d H:i:s P');

    fwrite(STDOUT, "[START] {$module['name']}" . PHP_EOL);

    $stdout = '';
    $stderr = '';
    $exitCode = 1;

    if (! is_file($scriptPath)) {
        $stderr = "Runner script not found: {$scriptPath}";
    } else {
        $command = '"' . str_replace('"', '\\"', PHP_BINARY) . '" "' . str_replace('"', '\\"', $scriptPath) . '"';

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes, $basePath);

        if (is_resource($process)) {
            fclose($pipes[0]);
            $stdout = stream_get_contents($pipes[1]) ?: '';
            fclose($pipes[1]);
            $stderr = stream_get_contents($pipes[2]) ?: '';
            fclose($pipes[2]);
            $exitCode = proc_close($process);
        } else {
            $stderr = 'Unable to start runner process.';
        }
    }

    $endedAt = microtime(true);
    $ended = date('Y-m-d H:i:s P');
    $duration = $endedAt - $startedAt;
    $status = $exitCode === 0 ? 'SUCCESS' : 'FAILED';

    if ($exitCode !== 0) {
        $anyFailure = true;
    }

    if ($stdout !== '') {
        fwrite(STDOUT, $stdout);
    }

    if ($stderr !== '') {
        fwrite(STDERR, $stderr);
    }

    fwrite(STDOUT, sprintf(
        "[%s] %s — %.3f seconds (exit code %d)" . PHP_EOL . PHP_EOL,
        $status,
        $module['name'],
        $duration,
        $exitCode,
    ));

    $rows[] = [
        $runId,
        $module['name'],
        $started,
        $ended,
        number_format($duration, 3, '.', ''),
        $exitCode,
        $status,
        trim(preg_replace('/\s+/', ' ', $stderr) ?? ''),
    ];
}

$totalDuration = microtime(true) - $runStartedAt;
$overallStatus = $anyFailure ? 'FAILED' : 'SUCCESS';

$header = [
    'run_id',
    'module',
    'started_at',
    'ended_at',
    'duration_seconds',
    'exit_code',
    'status',
    'error',
];

$writeReport = static function (string $path) use ($header, $rows): void {
    $handle = fopen($path, 'wb');

    if ($handle === false) {
        throw new RuntimeException("Unable to write report: {$path}");
    }

    fputcsv($handle, $header);

    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }

    fclose($handle);
};

try {
    $writeReport($reportPath);
    $writeReport($latestReportPath);
} catch (Throwable $exception) {
    fwrite(STDERR, 'Unable to write scheduler report: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, "=================================" . PHP_EOL);
fwrite(STDOUT, sprintf("Overall status: %s" . PHP_EOL, $overallStatus));
fwrite(STDOUT, sprintf("Total duration: %.3f seconds" . PHP_EOL, $totalDuration));
fwrite(STDOUT, "Report: {$reportPath}" . PHP_EOL);
fwrite(STDOUT, "Latest report: {$latestReportPath}" . PHP_EOL);

exit($anyFailure ? 1 : 0);
