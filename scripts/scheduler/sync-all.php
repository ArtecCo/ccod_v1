<?php

declare(strict_types=1);

/**
 * Unified scheduler runner for Windows Task Scheduler.
 *
 * Runs all six lightweight scheduler scripts sequentially, streams their
 * console output, records timing/results, and writes a human-readable HTML
 * report for every run.
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
$reportPath = $reportDirectory . DIRECTORY_SEPARATOR . "sync-{$runId}.html";
$latestReportPath = $reportDirectory . DIRECTORY_SEPARATOR . 'latest.html';

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

foreach ($rows as $index => $row) {
    $statusClass = $row['status'] === 'SUCCESS' ? 'success' : 'failed';
    $statusLabel = $row['status'] === 'SUCCESS' ? 'SUCCESS' : 'FAILED';
    $output = $row['stdout'];
    if ($row['stderr'] !== '') {
        $output .= ($output !== '' ? PHP_EOL . PHP_EOL : '') . '[stderr]' . PHP_EOL . $row['stderr'];
    }
    if ($output === '') {
        $output = 'No console output was produced by this module.';
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

            <details class="console" open>
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

$html = <<<'HTML'
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CCOD Azure Sync Report - __RUN_ID__</title>
<style>
:root {
    color-scheme: dark;
    --bg: #080a0d;
    --surface: #0f1217;
    --surface-2: #14181f;
    --border: #252b34;
    --text: #e7ebf0;
    --muted: #8e98a7;
    --success: #45d483;
    --success-bg: rgba(69, 212, 131, .10);
    --danger: #ff6678;
    --danger-bg: rgba(255, 102, 120, .10);
    --accent: #8aa4ff;
    --mono: "Cascadia Mono", "Cascadia Code", Consolas, monospace;
}
* { box-sizing: border-box; }
body {
    margin: 0;
    background: var(--bg);
    color: var(--text);
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    line-height: 1.5;
}
.container { width: min(1180px, calc(100% - 40px)); margin: 0 auto; padding: 42px 0 60px; }
.header { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; margin-bottom: 28px; }
.eyebrow { color: var(--muted); font-size: 12px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
h1 { margin: 5px 0 8px; font-size: clamp(28px, 4vw, 42px); letter-spacing: -.03em; }
.subtitle { color: var(--muted); margin: 0; font-size: 14px; }
.status { display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border); border-radius: 999px; padding: 7px 12px; font-size: 11px; font-weight: 800; letter-spacing: .08em; }
.status.success { color: var(--success); background: var(--success-bg); border-color: rgba(69, 212, 131, .25); }
.status.failed { color: var(--danger); background: var(--danger-bg); border-color: rgba(255, 102, 120, .25); }
.summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 34px; }
.metric { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; padding: 18px; }
.metric span { display: block; color: var(--muted); font-size: 12px; margin-bottom: 5px; }
.metric strong { display: block; font-size: 22px; letter-spacing: -.02em; }
.metric.total strong { color: var(--accent); }
.metric.good strong { color: var(--success); }
.metric.bad strong { color: var(--danger); }
.section-title { margin: 0 0 12px; font-size: 15px; }
.timeline { overflow: hidden; background: var(--surface); border: 1px solid var(--border); border-radius: 10px; margin-bottom: 34px; }
table { width: 100%; border-collapse: collapse; }
th, td { text-align: left; padding: 12px 15px; border-bottom: 1px solid var(--border); font-size: 13px; }
th { color: var(--muted); font-size: 11px; text-transform: uppercase; letter-spacing: .08em; }
tr:last-child td { border-bottom: 0; }
.dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 8px; }
.dot.success { background: var(--success); box-shadow: 0 0 8px rgba(69,212,131,.35); }
.dot.failed { background: var(--danger); box-shadow: 0 0 8px rgba(255,102,120,.35); }
.modules { display: grid; gap: 14px; }
.module-card { background: var(--surface); border: 1px solid var(--border); border-radius: 10px; overflow: hidden; }
.module-card.success { border-left: 3px solid var(--success); }
.module-card.failed { border-left: 3px solid var(--danger); }
.module-head { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 18px 20px; }
.module-title { display: flex; align-items: center; gap: 13px; min-width: 0; }
.module-number { color: var(--muted); font-family: var(--mono); font-size: 12px; }
.module-title h2 { margin: 0; font-size: 17px; }
.module-script { color: var(--muted); font-family: var(--mono); font-size: 11px; margin-top: 2px; }
.module-meta { display: grid; grid-template-columns: repeat(4, 1fr); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: var(--surface-2); }
.module-meta div { padding: 12px 16px; border-right: 1px solid var(--border); }
.module-meta div:last-child { border-right: 0; }
.module-meta span { display: block; color: var(--muted); font-size: 10px; text-transform: uppercase; letter-spacing: .08em; margin-bottom: 3px; }
.module-meta strong { font-family: var(--mono); font-size: 12px; font-weight: 600; }
.console { background: #07090c; }
.console summary { cursor: pointer; color: var(--muted); padding: 10px 16px; font-size: 11px; border-bottom: 1px solid var(--border); user-select: none; }
.console pre { margin: 0; padding: 16px; overflow-x: auto; color: #cbd2dc; font-family: var(--mono); font-size: 12px; line-height: 1.65; white-space: pre-wrap; word-break: break-word; }
.footer { margin-top: 28px; color: var(--muted); font-size: 11px; text-align: center; }
@media (max-width: 800px) {
    .container { width: min(100% - 24px, 1180px); padding-top: 24px; }
    .header { flex-direction: column; }
    .summary { grid-template-columns: repeat(2, 1fr); }
    .module-meta { grid-template-columns: repeat(2, 1fr); }
    .module-meta div:nth-child(2) { border-right: 0; }
    .module-meta div:nth-child(-n+2) { border-bottom: 1px solid var(--border); }
}
@media (max-width: 520px) {
    .summary { grid-template-columns: 1fr; }
    .module-meta { grid-template-columns: 1fr; }
    .module-meta div { border-right: 0; border-bottom: 1px solid var(--border); }
    .module-meta div:last-child { border-bottom: 0; }
}
</style>
</head>
<body>
<div class="container">
    <header class="header">
        <div>
            <div class="eyebrow">CCOD · Azure Operations</div>
            <h1>Synchronization Report</h1>
            <p class="subtitle">Run __RUN_ID__ · Completed __COMPLETED_AT__</p>
        </div>
        <div class="status __OVERALL_CLASS__">__OVERALL_STATUS__</div>
    </header>

    <section class="summary">
        <div class="metric total"><span>Total elapsed</span><strong>__TOTAL_DURATION__</strong></div>
        <div class="metric"><span>Modules</span><strong>__MODULE_COUNT__</strong></div>
        <div class="metric good"><span>Successful</span><strong>__SUCCESS_COUNT__</strong></div>
        <div class="metric bad"><span>Failed</span><strong>__FAILURE_COUNT__</strong></div>
    </section>

    <h2 class="section-title">Execution timeline</h2>
    <section class="timeline">
        <table>
            <thead><tr><th>#</th><th>Module</th><th>Status</th><th>Elapsed</th><th>Completed</th></tr></thead>
            <tbody>__TIMELINE_ROWS__</tbody>
        </table>
    </section>

    <h2 class="section-title">Module details</h2>
    <div class="modules">
        __MODULE_CARDS__
    </div>

    <div class="footer">Generated by CCOD unified scheduler · __GENERATED_AT__</div>
</div>
</body>
</html>
HTML;

$html = str_replace(
    [
        '__RUN_ID__',
        '__COMPLETED_AT__',
        '__GENERATED_AT__',
        '__OVERALL_CLASS__',
        '__OVERALL_STATUS__',
        '__TOTAL_DURATION__',
        '__MODULE_COUNT__',
        '__SUCCESS_COUNT__',
        '__FAILURE_COUNT__',
        '__TIMELINE_ROWS__',
        '__MODULE_CARDS__',
    ],
    [
        $escape($runId),
        $escape($completedAt),
        $escape($generatedAt),
        $overallClass,
        $overallStatus,
        $escape($formatDuration($totalDuration)),
        (string) count($rows),
        (string) $successCount,
        (string) $failureCount,
        $timelineRows,
        $moduleCards,
    ],
    $html,
);

try {
    if (file_put_contents($reportPath, $html, LOCK_EX) === false) {
        throw new RuntimeException("Unable to write report: {$reportPath}");
    }

    if (file_put_contents($latestReportPath, $html, LOCK_EX) === false) {
        throw new RuntimeException("Unable to write latest report: {$latestReportPath}");
    }
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
