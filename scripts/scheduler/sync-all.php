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
        <section class="module-card panel">
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
$primaryPalette = $themeColors['primary'] ?? [];
$grayPalette = $themeColors['gray'] ?? [];
$successPalette = $themeColors['success'] ?? [];
$dangerPalette = $themeColors['danger'] ?? [];
$cssColor = static fn (array $palette, int $shade, string $fallback): string => (string) ($palette[$shade] ?? $palette[(string) $shade] ?? $fallback);
$themeVariables = [
    '--app-accent' => $cssColor($primaryPalette, 600, '#d97706'),
    '--app-accent-hover' => $cssColor($primaryPalette, 700, '#b45309'),
    '--app-accent-soft' => $cssColor($primaryPalette, 50, '#fffbeb'),
    '--app-accent-border' => $cssColor($primaryPalette, 200, '#fde68a'),
    '--app-bg' => $cssColor($grayPalette, 50, '#fafafa'),
    '--app-surface' => '#ffffff',
    '--app-surface-muted' => $cssColor($grayPalette, 50, '#fafafa'),
    '--app-border' => $cssColor($grayPalette, 200, '#e4e4e7'),
    '--app-text' => $cssColor($grayPalette, 950, '#09090b'),
    '--app-muted' => $cssColor($grayPalette, 500, '#71717a'),
    '--app-success' => $cssColor($successPalette, 700, '#15803d'),
    '--app-success-bg' => $cssColor($successPalette, 50, '#f0fdf4'),
    '--app-success-border' => $cssColor($successPalette, 200, '#bbf7d0'),
    '--app-danger' => $cssColor($dangerPalette, 700, '#b91c1c'),
    '--app-danger-bg' => $cssColor($dangerPalette, 50, '#fef2f2'),
    '--app-danger-border' => $cssColor($dangerPalette, 200, '#fecaca'),
    '--app-radius' => (string) ($stylingSettings?->corner_radius ?: '0.375rem'),
];
$themeCss = implode(';', array_map(
    static fn (string $name, string $value): string => $name . ':' . $value,
    array_keys($themeVariables),
    array_values($themeVariables),
));

$html = <<<'HTML'
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CCOD Azure Sync Report - __RUN_ID__</title>
<style>
:root { __THEME_CSS__; --font-sans: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
* { box-sizing: border-box; }
html { color-scheme: light; }
body { margin: 0; background: var(--app-bg); color: var(--app-text); font-family: var(--font-sans); font-size: 14px; line-height: 1.5; -webkit-font-smoothing: antialiased; }
.container { width: min(1180px, calc(100% - 48px)); margin: 0 auto; padding: 36px 0 48px; }
.header { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; margin-bottom: 22px; }
.brandline { display: flex; align-items: center; gap: 9px; color: var(--app-muted); font-size: 12px; font-weight: 600; letter-spacing: .025em; }
.brand-mark { width: 8px; height: 8px; border-radius: 3px; background: var(--app-accent); box-shadow: 0 0 0 4px var(--app-accent-soft); }
h1 { margin: 9px 0 4px; font-size: clamp(24px, 3vw, 30px); font-weight: 650; letter-spacing: -.035em; line-height: 1.2; }
.subtitle { margin: 0; color: var(--app-muted); font-size: 13px; }
.status { display: inline-flex; align-items: center; gap: 7px; border: 1px solid var(--app-border); border-radius: var(--app-radius); padding: 7px 11px; font-size: 11px; font-weight: 650; white-space: nowrap; }
.status::before { content: ""; width: 7px; height: 7px; border-radius: 50%; background: currentColor; }
.status.success { color: var(--app-success); background: var(--app-success-bg); border-color: var(--app-success-border); }
.status.failed { color: var(--app-danger); background: var(--app-danger-bg); border-color: var(--app-danger-border); }
.panel { background: var(--app-surface); border: 1px solid var(--app-border); border-radius: var(--app-radius); box-shadow: 0 1px 2px rgb(0 0 0 / .04); }
.summary { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 26px; }
.metric { min-height: 92px; padding: 16px 18px; }
.metric span { display: block; margin-bottom: 7px; color: var(--app-muted); font-size: 12px; font-weight: 500; }
.metric strong { display: block; color: var(--app-text); font-size: 23px; font-weight: 650; letter-spacing: -.025em; line-height: 1.2; }
.metric.total strong { color: var(--app-accent); }
.metric.good strong { color: var(--app-success); }
.metric.bad strong { color: var(--app-danger); }
.section { margin-bottom: 26px; }
.section-title { margin: 0 0 11px; font-size: 15px; font-weight: 650; letter-spacing: -.01em; }
.table-wrap { overflow: hidden; }
table { width: 100%; border-collapse: collapse; }
th, td { padding: 11px 15px; border-bottom: 1px solid var(--app-border); text-align: left; font-size: 12px; }
th { background: var(--app-surface-muted); color: var(--app-muted); font-size: 10px; font-weight: 650; letter-spacing: .055em; text-transform: uppercase; }
tr:last-child td { border-bottom: 0; }
.dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 8px; vertical-align: 1px; }
.dot.success { background: var(--app-success); }
.dot.failed { background: var(--app-danger); }
.web-coverage table td:nth-child(n+2), .web-coverage table th:nth-child(n+2) { text-align: right; }
.modules { display: grid; gap: 12px; }
.module-card { overflow: hidden; }
.module-head { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 15px 18px; }
.module-title { display: flex; align-items: center; gap: 12px; min-width: 0; }
.module-number { display: grid; place-items: center; width: 30px; height: 30px; flex: 0 0 30px; border-radius: calc(var(--app-radius) * .75); background: var(--app-accent-soft); color: var(--app-accent); font-size: 11px; font-weight: 700; }
.module-title h2 { margin: 0; font-size: 14px; font-weight: 650; }
.module-script { margin-top: 2px; color: var(--app-muted); font-size: 11px; font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; }
.module-meta { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); border-top: 1px solid var(--app-border); background: var(--app-surface-muted); }
.module-meta div { min-width: 0; padding: 10px 14px; border-right: 1px solid var(--app-border); }
.module-meta div:last-child { border-right: 0; }
.module-meta span { display: block; margin-bottom: 3px; color: var(--app-muted); font-size: 10px; }
.module-meta strong { display: block; overflow-wrap: anywhere; font-size: 11px; font-weight: 550; }
.console { border-top: 1px solid var(--app-border); }
.console summary { cursor: pointer; padding: 10px 15px; color: var(--app-muted); font-size: 12px; font-weight: 550; user-select: none; }
.console[open] summary { border-bottom: 1px solid var(--app-border); }
.console pre { max-height: 480px; overflow: auto; margin: 0; padding: 15px 17px; background: var(--app-surface-muted); color: var(--app-text); font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace; font-size: 11px; line-height: 1.65; white-space: pre-wrap; overflow-wrap: anywhere; }
.footer { display: flex; justify-content: space-between; gap: 12px; margin-top: 24px; padding-top: 14px; border-top: 1px solid var(--app-border); color: var(--app-muted); font-size: 11px; }
@media (max-width: 760px) { .container { width: min(100% - 24px, 1180px); padding-top: 22px; } .header { flex-direction: column; } .summary { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px; } .metric { min-height: 82px; padding: 13px 14px; } .metric strong { font-size: 20px; } .module-meta { grid-template-columns: repeat(2, minmax(0, 1fr)); } .module-meta div:nth-child(2) { border-right: 0; } .module-meta div:nth-child(-n+2) { border-bottom: 1px solid var(--app-border); } .table-wrap { overflow-x: auto; } .footer { flex-direction: column; } }
@media print { @page { size: A4 portrait; margin: 12mm; } body { background: #fff; } .container { width: 100%; padding: 0; } .panel, .module-card { box-shadow: none; break-inside: avoid; } .summary { gap: 8px; margin-bottom: 16px; } .metric { min-height: auto; padding: 10px 12px; } .module-card { break-inside: avoid; } .console summary { display: none; } .console pre { max-height: none; } .status, .module-number, th, .module-meta { print-color-adjust: exact; -webkit-print-color-adjust: exact; } }</style>
</head>
<body>
<div class="container">
    <header class="header">
        <div>
            <div class="brandline"><span class="brand-mark"></span><span>__BRAND_NAME__</span><span>·</span><span>Azure Operations</span></div>
            <h1>Synchronization Report</h1>
            <p class="subtitle">Run __RUN_ID__ · Completed __COMPLETED_AT__</p>
        </div>
        <div class="status __OVERALL_CLASS__">__OVERALL_STATUS__</div>
    </header>

    <section class="summary">
        <div class="metric panel total"><span>Total elapsed</span><strong>__TOTAL_DURATION__</strong></div>
        <div class="metric panel"><span>Modules</span><strong>__MODULE_COUNT__</strong></div>
        <div class="metric panel good"><span>Successful</span><strong>__SUCCESS_COUNT__</strong></div>
        <div class="metric panel bad"><span>Failed</span><strong>__FAILURE_COUNT__</strong></div>
    </section>

    <h2 class="section-title">Execution timeline</h2>
    <section class="timeline panel table-wrap">
        <table>
            <thead><tr><th>#</th><th>Module</th><th>Status</th><th>Elapsed</th><th>Completed</th></tr></thead>
            <tbody>__TIMELINE_ROWS__</tbody>
        </table>
    </section>

    <section class="web-coverage section">
        <h2 class="section-title">Web URL coverage by project</h2>
        <table>
            <thead><tr><th>Project</th><th>Links checked</th><th>Live</th><th>Down</th></tr></thead>
            <tbody>__WEB_URL_ROWS__</tbody>
        </table>
    </section>

    <h2 class="section-title">Module details</h2>
    <section class="modules">__MODULE_CARDS__</section>

    <footer class="footer">Generated by CCOD scheduler · __GENERATED_AT__</footer>
</div>
</body>
</html>
HTML;

$replacements = [
    '__BRAND_NAME__' => $escape($brandName),
    '__THEME_CSS__' => $escape($themeCss),
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
