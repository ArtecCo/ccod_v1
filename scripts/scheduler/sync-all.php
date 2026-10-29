<?php

declare(strict_types=1);

/**
 * Unified scheduler runner for Windows Task Scheduler.
 * Runs all six lightweight scheduler scripts sequentially and writes a
 * printer-friendly HTML report for every run.
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

$html = <<<'HTML'
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CCOD Azure Sync Report - __RUN_ID__</title>
<style>
:root {
    --bg: #ffffff;
    --surface: #ffffff;
    --surface-2: #f5f6f8;
    --border: #d9dde3;
    --text: #20252b;
    --muted: #66707c;
    --success: #147a43;
    --success-bg: #edf8f1;
    --danger: #b42336;
    --danger-bg: #fff0f2;
    --accent: #315f9b;
    --mono: "Cascadia Mono", "Cascadia Code", Consolas, monospace;
}
* { box-sizing: border-box; }
body { margin: 0; background: var(--bg); color: var(--text); font-family: Arial, Helvetica, sans-serif; line-height: 1.45; }
.container { width: min(1120px, calc(100% - 40px)); margin: 0 auto; padding: 34px 0 50px; }
.header { display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; margin-bottom: 24px; }
.eyebrow { color: var(--muted); font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
h1 { margin: 4px 0 6px; font-size: 32px; letter-spacing: -.02em; }
.subtitle { color: var(--muted); margin: 0; font-size: 13px; }
.status { display: inline-flex; align-items: center; justify-content: center; border: 1px solid var(--border); border-radius: 999px; padding: 6px 11px; font-size: 10px; font-weight: 800; letter-spacing: .08em; }
.status.success { color: var(--success); background: var(--success-bg); border-color: #bfe5cc; }
.status.failed { color: var(--danger); background: var(--danger-bg); border-color: #efc2c9; }
.summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 28px; }
.metric { background: var(--surface); border: 1px solid var(--border); border-radius: 7px; padding: 13px 15px; }
.metric span { display: block; color: var(--muted); font-size: 11px; margin-bottom: 3px; }
.metric strong { display: block; font-size: 19px; }
.metric.total strong { color: var(--accent); }
.metric.good strong { color: var(--success); }
.metric.bad strong { color: var(--danger); }
.section-title { margin: 0 0 9px; font-size: 14px; }
.timeline { overflow: hidden; border: 1px solid var(--border); border-radius: 7px; margin-bottom: 28px; }
table { width: 100%; border-collapse: collapse; }
th, td { text-align: left; padding: 9px 12px; border-bottom: 1px solid var(--border); font-size: 12px; }
th { background: var(--surface-2); color: var(--muted); font-size: 10px; text-transform: uppercase; letter-spacing: .07em; }
tr:last-child td { border-bottom: 0; }
.dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 7px; }
.dot.success { background: var(--success); }
.dot.failed { background: var(--danger); }
.web-coverage { margin-bottom: 28px; }
.web-coverage table { border: 1px solid var(--border); }
.web-coverage th, .web-coverage td { padding: 8px 11px; }
.web-coverage td:nth-child(n+2), .web-coverage th:nth-child(n+2) { text-align: right; }
.modules { display: grid; gap: 12px; }
.module-card { background: var(--surface); border: 1px solid var(--border); border-radius: 7px; overflow: hidden; break-inside: avoid; }
.module-card.success { border-left: 3px solid var(--success); }
.module-card.failed { border-left: 3px solid var(--danger); }
.module-head { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 14px 16px; }
.module-title { display: flex; align-items: center; gap: 11px; min-width: 0; }
.module-number { color: var(--muted); font-family: var(--mono); font-size: 11px; }
.module-title h2 { margin: 0; font-size: 15px; }
.module-script { color: var(--muted); font-family: var(--mono); font-size: 10px; margin-top: 2px; }
.module-meta { display: grid; grid-template-columns: repeat(4, 1fr); border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background: var(--surface-2); }
.module-meta div { padding: 9px 12px; border-right: 1px solid var(--border); }
.module-meta div:last-child { border-right: 0; }
.module-meta span { display: block; color: var(--muted); font-size: 9px; text-transform: uppercase; letter-spacing: .07em; margin-bottom: 2px; }
.module-meta strong { font-family: var(--mono); font-size: 10px; font-weight: 600; }
.console { background: #fafafa; }
.console summary { cursor: pointer; color: var(--muted); padding: 8px 12px; font-size: 10px; border-bottom: 1px solid var(--border); user-select: none; }
.console pre { margin: 0; padding: 12px; overflow-x: auto; color: #30363d; font-family: var(--mono); font-size: 10px; line-height: 1.55; white-space: pre-wrap; word-break: break-word; }
.footer { margin-top: 24px; color: var(--muted); font-size: 10px; text-align: center; }
@media (max-width: 800px) {
    .container { width: min(100% - 24px, 1120px); padding-top: 22px; }
    .header { flex-direction: column; }
    .summary { grid-template-columns: repeat(2, 1fr); }
    .module-meta { grid-template-columns: repeat(2, 1fr); }
    .module-meta div:nth-child(2) { border-right: 0; }
    .module-meta div:nth-child(-n+2) { border-bottom: 1px solid var(--border); }
}
@media print {
    @page { size: A4 portrait; margin: 12mm; }
    body { background: #fff; color: #000; font-size: 10pt; }
    .container { width: 100%; padding: 0; }
    .header { margin-bottom: 14px; }
    h1 { font-size: 22pt; }
    .summary { grid-template-columns: repeat(4, 1fr); margin-bottom: 16px; }
    .metric { padding: 8px 10px; border-radius: 0; }
    .timeline, .web-coverage { margin-bottom: 16px; }
    .module-card { border-radius: 0; box-shadow: none; }
    .modules { gap: 9px; }
    .console { background: #fff; }
    .console summary { display: none; }
    .console pre { padding: 8px; font-size: 8pt; }
    .module-card, .timeline, .web-coverage { break-inside: avoid; }
    .module-card { page-break-inside: avoid; }
    .status { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
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

    <section class="web-coverage">
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
exit($anyFailure ? 1 : 0);
