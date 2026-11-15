<?php

declare(strict_types=1);

/**
 * Unified scheduler runner for Windows Task Scheduler.
 * Runs all six lightweight scheduler scripts sequentially and writes a
 * printer-friendly HTML report for every run.
 */

$basePath = dirname(__DIR__, 2);

fwrite(STDOUT, PHP_EOL . "Initializing CCOD unified Azure synchronization..." . PHP_EOL);
fflush(STDOUT);

define('LARAVEL_START', microtime(true));
require $basePath . '/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require $basePath . '/bootstrap/app.php';
$app->bootstrapWith([
    \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    \Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    \Illuminate\Foundation\Bootstrap\RegisterFacades::class,
    \Illuminate\Foundation\Bootstrap\RegisterProviders::class,
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
]);

$syncTracker = $app->make(\App\Services\SubscriptionModuleSyncTracker::class);
$trackedSubscriptionIds = \App\Models\AzureSubscription::query()
    ->pluck('subscription_id')
    ->filter()
    ->values()
    ->all();

// Standalone CLI runners do not necessarily pass through Laravel's timezone bootstrap.
date_default_timezone_set('Asia/Kolkata');
$schedulerPath = __DIR__;
$reportDirectory = $basePath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'scheduler' . DIRECTORY_SEPARATOR . 'reports';

if (! is_dir($reportDirectory) && ! mkdir($reportDirectory, 0775, true) && ! is_dir($reportDirectory)) {
    fwrite(STDERR, "Unable to create scheduler report directory: {$reportDirectory}" . PHP_EOL);
    exit(1);
}

$runStartedAt = microtime(true);
$syncStartedAt = date('Y-m-d H:i:s P');
$runId = date('Ymd-His');
$reportPath = $reportDirectory . DIRECTORY_SEPARATOR . "sync-{$runId}.html";
$latestReportPath = $reportDirectory . DIRECTORY_SEPARATOR . 'latest.html';

try {
    $app->make(\App\Services\TeamsWebhookNotifier::class)->dispatch('sync.started', [
        'run_id' => $runId,
        'sync_started_at' => $syncStartedAt,
    ]);
} catch (\Throwable $exception) {
    fwrite(STDERR, 'Teams sync-start notification failed: '.$exception->getMessage().PHP_EOL);
}

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

fwrite(STDOUT, "Run ID: {$runId}" . PHP_EOL . PHP_EOL);
fflush(STDOUT);

foreach ($modules as $module) {
    $moduleKey = match ($module['name']) {
        'Subscriptions' => 'subscriptions',
        'Resources' => 'resources',
        'Costs' => 'costs',
        'Security' => 'security',
        'Alerts' => 'alerts',
        'Web URLs' => 'web_urls',
        default => null,
    };

    if ($moduleKey !== null) {
        $syncTracker->begin($moduleKey, $trackedSubscriptionIds);
    }

    $scriptPath = $schedulerPath . DIRECTORY_SEPARATOR . $module['script'];

    $startedAt = microtime(true);
    $started = date('Y-m-d H:i:s P');

    fwrite(STDOUT, "[START] {$module['name']}" . PHP_EOL);

    $stdout = '';
    $stderr = '';
    $exitCode = 1;
    $timedOut = false;

    if (! is_file($scriptPath)) {
        $stderr = "Runner script not found: {$scriptPath}";
    } else {
        $command = '"' . str_replace('"', '\"', PHP_BINARY) . '" "' . str_replace('"', '\"', $scriptPath) . '"';
        // Keep child output in files so neither output stream can block the child.
        // Read those files while the process runs so Task Scheduler/console output
        // identifies the exact operation that is taking time.
        $stdoutFile = tempnam(sys_get_temp_dir(), 'ccod-sync-out-');
        $stderrFile = tempnam(sys_get_temp_dir(), 'ccod-sync-err-');

        if ($stdoutFile === false || $stderrFile === false) {
            if (is_string($stdoutFile) && is_file($stdoutFile)) {
                unlink($stdoutFile);
            }
            if (is_string($stderrFile) && is_file($stderrFile)) {
                unlink($stderrFile);
            }

            $stderr = 'Unable to create temporary output files for the runner process.';
        } else {
            $descriptors = [
                0 => ['pipe', 'r'],
                1 => ['file', $stdoutFile, 'w'],
                2 => ['file', $stderrFile, 'w'],
            ];

            $process = proc_open($command, $descriptors, $pipes, $basePath);

            if (is_resource($process)) {
                fclose($pipes[0]);

                $stdoutReader = fopen($stdoutFile, 'rb');
                $stderrReader = fopen($stderrFile, 'rb');
                $timeoutSeconds = max(60, (int) (getenv('CCOD_SYNC_MODULE_TIMEOUT') ?: 1800));
                $deadline = microtime(true) + $timeoutSeconds;
                $lastStatus = null;

                do {
                    if (is_resource($stdoutReader)) {
                        $chunk = stream_get_contents($stdoutReader);
                        if (is_string($chunk) && $chunk !== '') {
                            $stdout .= $chunk;
                            fwrite(STDOUT, $chunk);
                        }
                    }

                    if (is_resource($stderrReader)) {
                        $chunk = stream_get_contents($stderrReader);
                        if (is_string($chunk) && $chunk !== '') {
                            $stderr .= $chunk;
                            fwrite(STDERR, $chunk);
                        }
                    }

                    $lastStatus = proc_get_status($process);
                    if (! ($lastStatus['running'] ?? false)) {
                        $exitCode = (int) ($lastStatus['exitcode'] ?? 1);
                        break;
                    }

                    if (microtime(true) >= $deadline) {
                        $timedOut = true;
                        $stderr .= PHP_EOL . "Module exceeded its {$timeoutSeconds}-second timeout; terminating child process." . PHP_EOL;
                        fwrite(STDERR, "[TIMEOUT] {$module['name']} exceeded {$timeoutSeconds} seconds; terminating." . PHP_EOL);
                        proc_terminate($process);
                        usleep(500000);

                        $lastStatus = proc_get_status($process);
                        if ($lastStatus['running'] ?? false) {
                            proc_terminate($process, 9);
                        }

                        $exitCode = 124;
                        break;
                    }

                    usleep(200000);
                } while (true);

                // Capture any bytes written between the last poll and process exit.
                if (is_resource($stdoutReader)) {
                    $chunk = stream_get_contents($stdoutReader);
                    if (is_string($chunk) && $chunk !== '') {
                        $stdout .= $chunk;
                        fwrite(STDOUT, $chunk);
                    }
                    fclose($stdoutReader);
                }

                if (is_resource($stderrReader)) {
                    $chunk = stream_get_contents($stderrReader);
                    if (is_string($chunk) && $chunk !== '') {
                        $stderr .= $chunk;
                        fwrite(STDERR, $chunk);
                    }
                    fclose($stderrReader);
                }

                $closedExitCode = proc_close($process);
                if (! $timedOut && $exitCode < 0 && $closedExitCode >= 0) {
                    $exitCode = $closedExitCode;
                }
                if ($timedOut) {
                    $exitCode = 124;
                }
            } else {
                $stderr = 'Unable to start runner process.';
            }

            if (is_file($stdoutFile)) {
                unlink($stdoutFile);
            }
            if (is_file($stderrFile)) {
                unlink($stderrFile);
            }
        }
    }

    $endedAt = microtime(true);
    $ended = date('Y-m-d H:i:s P');
    $duration = $endedAt - $startedAt;
    $status = $exitCode === 0 ? 'SUCCESS' : 'FAILED';

    if ($moduleKey !== null) {
        if ($exitCode === 0) {
            $syncTracker->succeed($moduleKey, $trackedSubscriptionIds);
        } else {
            $syncTracker->fail($moduleKey, $trackedSubscriptionIds, trim($stderr) !== '' ? trim($stderr) : "{$module['name']} sync exited with code {$exitCode}.");
        }
    }

    if ($exitCode !== 0) {

        $anyFailure = true;
    }

    // Child output is streamed to the console while the process runs.
    // Do not print the accumulated buffers again here.

    fwrite(STDOUT, sprintf(
        "[%s] %s — %.3f seconds (exit code %d)" . PHP_EOL . PHP_EOL,
        $status,
        $module['name'],
        $duration,
        $exitCode,
    ));

    $rows[] = [
        'name' => $module['name'],
        'script' => $module['script'],
        'started_at' => $started,
        'ended_at' => $ended,
        'duration' => $duration,
        'exit_code' => $exitCode,
        'status' => $status,
        'stdout' => trim($stdout),
        'stderr' => trim($stderr),
    ];
}

$totalDuration = microtime(true) - $runStartedAt;
$overallStatus = $anyFailure ? 'FAILED' : 'SUCCESS';
$completedAt = date('Y-m-d H:i:s P');

$formatDuration = static function (float $seconds): string {
    if ($seconds < 60) {
        return number_format($seconds, 3) . ' s';
    }

    $minutes = (int) floor($seconds / 60);
    $remaining = $seconds - ($minutes * 60);

    return sprintf('%dm %0.3fs', $minutes, $remaining);
};

$escape = static fn (string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$moduleCards = '';
$timelineRows = '';
$webUrlRows = '';

foreach ($rows as $index => $row) {
    $statusClass = $row['status'] === 'SUCCESS' ? 'success' : 'failed';
    $statusLabel = $row['status'];

    $output = $row['stdout'];
    if ($row['stderr'] !== '') {
        $output .= ($output !== '' ? PHP_EOL . PHP_EOL : '') . '[stderr]' . PHP_EOL . $row['stderr'];
    }
    if ($output === '') {
        $output = 'No console output was produced by this module.';
    }

    if ($row['name'] === 'Web URLs') {
        $lines = preg_split('/\R/', $row['stdout']) ?: [];
        foreach ($lines as $line) {
            if (preg_match('/^(.+?)\s+(\d+)\s+(\d+)\s+(\d+)$/', trim($line), $matches)) {
                $project = trim($matches[1]);
                if ($project !== 'Project' && ! str_starts_with($project, '---')) {
                    $webUrlRows .= sprintf(
                        '<tr><td>%s</td><td>%d</td><td>%d</td><td>%d</td></tr>',
                        $escape($project),
                        (int) $matches[2],
                        (int) $matches[3],
                        (int) $matches[4],
                    );
                }
            }
        }
    }

    $moduleCards .= sprintf(
        <<<'HTML'
        <section class="module-card %s">
            <div class="module-head">
                <div class="module-title">
                    <span class="module-number">%02d</span>
                    <div>
                        <h2>%s</h2>
                        <div class="module-script">%s</div>
                    </div>
                </div>
                <div class="status %s">%s</div>
            </div>
            <div class="module-meta">
                <div><span>Started</span><strong>%s</strong></div>
                <div><span>Ended</span><strong>%s</strong></div>
                <div><span>Elapsed</span><strong>%s</strong></div>
                <div><span>Exit code</span><strong>%d</strong></div>
            </div>
            <details class="console">
                <summary>Console output</summary>
                <pre>%s</pre>
            </details>
        </section>
        HTML,
        $statusClass,
        $index + 1,
        $escape($row['name']),
        $escape($row['script']),
        $statusClass,
        $statusLabel,
        $escape($row['started_at']),
        $escape($row['ended_at']),
        $escape($formatDuration($row['duration'])),
        $row['exit_code'],
        $escape($output),
    );

    $timelineRows .= sprintf(
        '<tr><td>%02d</td><td>%s</td><td><span class="dot %s"></span>%s</td><td>%s</td><td>%s</td></tr>',
        $index + 1,
        $escape($row['name']),
        $statusClass,
        $statusLabel,
        $escape($formatDuration($row['duration'])),
        $escape($row['ended_at']),
    );
}

$overallClass = $overallStatus === 'SUCCESS' ? 'success' : 'failed';
$successCount = count(array_filter($rows, static fn (array $row): bool => $row['status'] === 'SUCCESS'));
$failureCount = count($rows) - $successCount;
$generatedAt = date('Y-m-d H:i:s P');
$stylingSettings = \App\Support\FilamentStyling::settings();
$themeColors = \App\Support\FilamentStyling::colors();
$brandName = (string) ($stylingSettings?->brand_name ?: 'CCOD');
$cornerRadius = (string) ($stylingSettings?->corner_radius ?: '0.375rem');
$manifestPath = $basePath . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR . 'manifest.json';
$themeAsset = null;

if (is_file($manifestPath)) {
    $manifest = json_decode((string) file_get_contents($manifestPath), true);
    $themeFile = $manifest['resources/css/filament/developer/theme.css']['file'] ?? null;

    if (is_string($themeFile) && $themeFile !== '') {
        $themeAsset = rtrim((string) config('app.url'), '/') . '/build/' . ltrim($themeFile, '/');
    }
}

$cssVariables = [
    '--ccod-corner-radius' => $cornerRadius,
];

foreach (['primary' => 'primary', 'success' => 'success', 'warning' => 'warning', 'danger' => 'danger', 'info' => 'info', 'gray' => 'gray'] as $key => $prefix) {
    foreach (($themeColors[$key] ?? []) as $shade => $color) {
        if (is_numeric($shade) && is_string($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
            $cssVariables["--{$prefix}-{$shade}"] = $color;
        }
    }
}

$themeCss = implode(';', array_map(
    static fn (string $name, string $value): string => $name . ':' . $value,
    array_keys($cssVariables),
    array_values($cssVariables),
));

$html = <<<'HTML'
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>__BRAND_NAME__ · Azure synchronization report · __RUN_ID__</title>
__FILAMENT_THEME__
<style>
:root { __THEME_VARIABLES__; color-scheme: light; }
* { box-sizing: border-box; }
body { margin: 0; background: var(--gray-50, #fafafa); color: var(--gray-950, #09090b); font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; font-size: .875rem; line-height: 1.5; }
.report-shell { width: min(100% - 2rem, 88rem); margin-inline: auto; padding-block: 1.5rem 3rem; }
.report-header { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:1.25rem; }
.report-brand { color:var(--primary-600); font-size:.75rem; font-weight:600; }
.report-title { margin:.25rem 0; font-size:1.5rem; line-height:2rem; font-weight:600; letter-spacing:-.025em; }
.report-subtitle { color:var(--gray-500); font-size:.8125rem; }
.report-panel { background:white; border:1px solid var(--gray-200); border-radius:var(--ccod-corner-radius); overflow:hidden; }
.report-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:.75rem; margin-bottom:1.25rem; }
.report-stat { padding:1rem; }
.report-stat-label { color:var(--gray-500); font-size:.75rem; font-weight:500; }
.report-stat-value { margin-top:.25rem; font-size:1.25rem; line-height:1.75rem; font-weight:600; }
.report-stat-value.primary { color:var(--primary-600); }
.report-stat-value.success { color:var(--success-600); }
.report-stat-value.danger { color:var(--danger-600); }
.report-section { margin-top:1.25rem; }
.report-section-title { margin:0 0 .625rem; font-size:.9375rem; font-weight:600; }
.report-table-wrap { overflow-x:auto; }
.report-table { width:100%; border-collapse:collapse; text-align:left; }
.report-table th { padding:.7rem .875rem; background:var(--gray-50); color:var(--gray-500); font-size:.6875rem; font-weight:600; letter-spacing:.04em; text-transform:uppercase; white-space:nowrap; }
.report-table td { padding:.65rem .875rem; border-top:1px solid var(--gray-200); font-size:.8125rem; }
.report-status { display:inline-flex; align-items:center; gap:.375rem; border-radius:var(--ccod-corner-radius); padding:.2rem .55rem; font-size:.6875rem; font-weight:600; }
.report-status.success { background:var(--success-50); color:var(--success-700); }
.report-status.failed { background:var(--danger-50); color:var(--danger-700); }
.report-dot { width:.4rem; height:.4rem; border-radius:999px; background:currentColor; }
.report-module { margin-top:.75rem; }
.report-module-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.875rem 1rem; }
.report-module-name { display:flex; align-items:center; gap:.75rem; min-width:0; }
.report-module-number { color:var(--gray-400); font-variant-numeric:tabular-nums; font-size:.75rem; }
.report-module-title { font-size:.875rem; font-weight:600; }
.report-module-script { color:var(--gray-500); font-size:.6875rem; }
.report-module-meta { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); border-top:1px solid var(--gray-200); background:var(--gray-50); }
.report-module-meta > div { padding:.625rem .875rem; border-right:1px solid var(--gray-200); min-width:0; }
.report-module-meta > div:last-child { border-right:0; }
.report-meta-label { display:block; color:var(--gray-500); font-size:.6875rem; }
.report-meta-value { display:block; margin-top:.125rem; font-size:.75rem; font-weight:500; overflow-wrap:anywhere; }
.report-console { border-top:1px solid var(--gray-200); }
.report-console summary { cursor:pointer; padding:.625rem .875rem; color:var(--gray-600); font-size:.75rem; font-weight:500; }
.report-console pre { margin:0; padding:.875rem; overflow:auto; background:var(--gray-50); color:var(--gray-700); font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:.7rem; line-height:1.55; white-space:pre-wrap; overflow-wrap:anywhere; }
.report-footer { margin-top:1.5rem; color:var(--gray-500); font-size:.6875rem; text-align:center; }

.status { display:inline-flex; align-items:center; justify-content:center; border-radius:var(--ccod-corner-radius); padding:.2rem .55rem; font-size:.6875rem; font-weight:600; }
.status.success { background:var(--success-50); color:var(--success-700); }
.status.failed { background:var(--danger-50); color:var(--danger-700); }
.module-card { margin-top:.75rem; background:#fff; border:1px solid var(--gray-200); border-radius:var(--ccod-corner-radius); overflow:hidden; break-inside:avoid; }
.module-card.success { border-left:3px solid var(--success-600); }
.module-card.failed { border-left:3px solid var(--danger-600); }
.module-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:.875rem 1rem; }
.module-title { display:flex; align-items:center; gap:.75rem; min-width:0; }
.module-title h2 { margin:0; font-size:.875rem; font-weight:600; }
.module-number { color:var(--gray-400); font-variant-numeric:tabular-nums; font-size:.75rem; }
.module-script { color:var(--gray-500); font-size:.6875rem; }
.module-meta { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); border-top:1px solid var(--gray-200); background:var(--gray-50); }
.module-meta>div { padding:.625rem .875rem; border-right:1px solid var(--gray-200); min-width:0; }
.module-meta>div:last-child { border-right:0; }
.module-meta span { display:block; color:var(--gray-500); font-size:.6875rem; }
.module-meta strong { display:block; margin-top:.125rem; font-size:.75rem; font-weight:500; overflow-wrap:anywhere; }
.console { border-top:1px solid var(--gray-200); background:var(--gray-50); }
.console summary { cursor:pointer; padding:.625rem .875rem; color:var(--gray-600); font-size:.75rem; font-weight:500; }
.console pre { margin:0; padding:.875rem; overflow:auto; color:var(--gray-700); font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:.7rem; line-height:1.55; white-space:pre-wrap; overflow-wrap:anywhere; }
.dot { display:inline-block; width:.4rem; height:.4rem; margin-right:.375rem; border-radius:999px; background:currentColor; }
.dot.success { color:var(--success-600); }
.dot.failed { color:var(--danger-600); }

@media(max-width:700px) { .report-summary {grid-template-columns:repeat(2,minmax(0,1fr));} .report-header {flex-direction:column;} .report-module-meta {grid-template-columns:repeat(2,minmax(0,1fr));} .report-module-meta>div:nth-child(2) {border-right:0;} .report-module-meta>div:nth-child(-n+2) {border-bottom:1px solid var(--gray-200);} }
@media print { @page {size:A4 portrait;margin:12mm;} body {background:#fff;} .report-shell {width:100%;padding:0;} .report-panel {break-inside:avoid;} .report-console summary {display:none;} .report-console pre {max-height:none;} .report-module {break-inside:avoid;} }
</style>
</head>
<body>
<main class="report-shell">
    <header class="report-header">
        <div>
            <div class="report-brand">__BRAND_NAME__ · Azure Operations</div>
            <h1 class="report-title">Synchronization report</h1>
            <div class="report-subtitle">Run __RUN_ID__ · Completed __COMPLETED_AT__</div>
        </div>
        <span class="report-status __OVERALL_CLASS__">__OVERALL_STATUS__</span>
    </header>
    <section class="report-summary">
        <div class="report-panel report-stat"><div class="report-stat-label">Total elapsed</div><div class="report-stat-value primary">__TOTAL_DURATION__</div></div>
        <div class="report-panel report-stat"><div class="report-stat-label">Modules</div><div class="report-stat-value">__MODULE_COUNT__</div></div>
        <div class="report-panel report-stat"><div class="report-stat-label">Successful</div><div class="report-stat-value success">__SUCCESS_COUNT__</div></div>
        <div class="report-panel report-stat"><div class="report-stat-label">Failed</div><div class="report-stat-value danger">__FAILURE_COUNT__</div></div>
    </section>
    <section class="report-section">
        <h2 class="report-section-title">Execution timeline</h2>
        <div class="report-panel report-table-wrap"><table class="report-table"><thead><tr><th>#</th><th>Module</th><th>Status</th><th>Elapsed</th><th>Completed</th></tr></thead><tbody>__TIMELINE_ROWS__</tbody></table></div>
    </section>
    <section class="report-section">
        <h2 class="report-section-title">Web URL coverage by project</h2>
        <div class="report-panel report-table-wrap"><table class="report-table"><thead><tr><th>Project</th><th>Links checked</th><th>Live</th><th>Down</th></tr></thead><tbody>__WEB_URL_ROWS__</tbody></table></div>
    </section>
    <section class="report-section">
        <h2 class="report-section-title">Module details</h2>
        <div>__MODULE_CARDS__</div>
    </section>
    <footer class="report-footer">Generated by __BRAND_NAME__ scheduler · __GENERATED_AT__</footer>
</main>
</body>
</html>
HTML;

$filamentThemeLink = $themeAsset !== null
    ? '<link rel="stylesheet" href="' . htmlspecialchars($themeAsset, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">'
    : '';

$replacements = [
    '__BRAND_NAME__' => $escape($brandName),
    '__THEME_VARIABLES__' => $themeCss,
    '__FILAMENT_THEME__' => $filamentThemeLink,
    '__RUN_ID__' => $escape($runId),
    '__COMPLETED_AT__' => $escape($completedAt),
    '__GENERATED_AT__' => $escape($generatedAt),
    '__OVERALL_CLASS__' => $overallClass,
    '__OVERALL_STATUS__' => $escape($overallStatus),
    '__TOTAL_DURATION__' => $escape($formatDuration($totalDuration)),
    '__MODULE_COUNT__' => (string) count($rows),
    '__SUCCESS_COUNT__' => (string) $successCount,
    '__FAILURE_COUNT__' => (string) $failureCount,
    '__TIMELINE_ROWS__' => $timelineRows,
    '__WEB_URL_ROWS__' => $webUrlRows !== '' ? $webUrlRows : '<tr><td colspan="4">No project-level web URL results were reported.</td></tr>',
    '__MODULE_CARDS__' => $moduleCards,
];

$html = strtr($html, $replacements);

if (file_put_contents($reportPath, $html) === false || file_put_contents($latestReportPath, $html) === false) {
    fwrite(STDERR, "Unable to write scheduler HTML report." . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, "Report: {$reportPath}" . PHP_EOL);
fwrite(STDOUT, "Latest: {$latestReportPath}" . PHP_EOL);

try {
    $moduleStatuses = [];
    foreach ($rows as $row) {
        $key = match ($row['name']) {
            'Subscriptions' => 'subscriptions_status',
            'Resources' => 'resources_status',
            'Costs' => 'costs_status',
            'Security' => 'security_status',
            'Alerts' => 'alerts_status',
            'Web URLs' => 'web_urls_status',
            default => null,
        };
        if ($key !== null) {
            $moduleStatuses[$key] = $row['status'];
        }
    }

    $app->make(\App\Services\TeamsWebhookNotifier::class)->sendSyncStatus(array_merge([
        'overall_status' => $overallStatus,
        'sync_started_at' => $syncStartedAt,
        'completed_at' => $completedAt,
        'duration' => $formatDuration($totalDuration),
        'duration_seconds' => round($totalDuration, 3),
        'run_id' => $runId,
        'report_url' => rtrim((string) config('app.url'), '/') . '/sync-report/latest',
        'failed_modules' => array_values(array_map(static fn (array $row): string => $row['name'], array_filter($rows, static fn (array $row): bool => $row['status'] !== 'SUCCESS'))),
        'successful_modules' => array_values(array_map(static fn (array $row): string => $row['name'], array_filter($rows, static fn (array $row): bool => $row['status'] === 'SUCCESS'))),
        'error_summary' => implode(' | ', array_values(array_filter(array_map(static fn (array $row): string => trim($row['stderr']), array_filter($rows, static fn (array $row): bool => $row['status'] !== 'SUCCESS'))))),
    ], $moduleStatuses));
} catch (\Throwable $exception) {
    fwrite(STDERR, 'Teams sync notification failed: '.$exception->getMessage().PHP_EOL);
}

exit($anyFailure ? 1 : 0);
