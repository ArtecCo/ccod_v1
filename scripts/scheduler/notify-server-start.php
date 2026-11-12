<?php

declare(strict_types=1);

$basePath = dirname(__DIR__, 2);

define('LARAVEL_START', microtime(true));
require $basePath.'/vendor/autoload.php';
$app = require $basePath.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$runPowerShell = static function (string $script): string {
    $command = 'powershell.exe -NoProfile -NonInteractive -ExecutionPolicy Bypass -Command '.escapeshellarg($script);
    $output = [];
    exec($command.' 2>&1', $output);

    return trim(implode(PHP_EOL, $output));
};

$serviceInfo = static function (string $service) use ($runPowerShell): array {
    $name = str_replace("'", "''", $service);
    $script = <<<PS
$service = Get-CimInstance Win32_Service -Filter "Name='$name'"
if ($service) {
    $started = 'Not available'
    if ($service.ProcessId -gt 0) {
        $process = Get-CimInstance Win32_Process -Filter "ProcessId=$($service.ProcessId)"
        if ($process -and $process.CreationDate) { $started = $process.CreationDate.ToString('yyyy-MM-dd HH:mm:ss') }
    }
    [pscustomobject]@{ State=$service.State; StartedAt=$started } | ConvertTo-Json -Compress
}
PS;
    $data = json_decode($runPowerShell($script), true);

    return [
        'status' => is_array($data) ? (string) ($data['State'] ?? 'NOT DETECTED') : 'NOT DETECTED',
        'started_at' => is_array($data) ? (string) ($data['StartedAt'] ?? 'Not available') : 'Not available',
    ];
};

$bootTime = $runPowerShell("(Get-CimInstance Win32_OperatingSystem).LastBootUpTime.ToString('yyyy-MM-dd HH:mm:ss')");
$phpScript = <<<'PS'
$process = Get-CimInstance Win32_Process | Where-Object { $_.Name -ieq 'php-cgi.exe' } | Sort-Object CreationDate | Select-Object -First 1
if ($process) { [pscustomobject]@{Status='RUNNING';StartedAt=$process.CreationDate.ToString('yyyy-MM-dd HH:mm:ss')} }
else { [pscustomobject]@{Status='NOT DETECTED';StartedAt='Not available'} } | ConvertTo-Json -Compress
PS;
$php = json_decode($runPowerShell($phpScript), true) ?: ['Status' => 'NOT DETECTED', 'StartedAt' => 'Not available'];

$queueScript = <<<'PS'
$process = Get-CimInstance Win32_Process | Where-Object { $_.Name -ieq 'php.exe' -and $_.CommandLine -match 'artisan queue:work' } | Sort-Object CreationDate | Select-Object -First 1
if ($process) { [pscustomobject]@{Status='RUNNING';StartedAt=$process.CreationDate.ToString('yyyy-MM-dd HH:mm:ss')} }
else { [pscustomobject]@{Status='NOT RUNNING';StartedAt='Not available'} } | ConvertTo-Json -Compress
PS;
$queue = json_decode($runPowerShell($queueScript), true) ?: ['Status' => 'NOT RUNNING', 'StartedAt' => 'Not available'];

$mysql = $serviceInfo('MySQL2610');
$w3svc = $serviceInfo('W3SVC');
$was = $serviceInfo('WAS');

$context = [
    'server_boot_time' => $bootTime !== '' ? $bootTime : 'Not available',
    'iis_status' => ($w3svc['status'] === 'Running' && $was['status'] === 'Running') ? 'RUNNING' : 'NOT RUNNING',
    'iis_started_at' => $w3svc['started_at'],
    'php_status' => $php['Status'],
    'php_started_at' => $php['StartedAt'],
    'mysql_status' => strtoupper($mysql['status']),
    'mysql_started_at' => $mysql['started_at'],
    'queue_status' => $queue['Status'],
    'queue_started_at' => $queue['StartedAt'],
];

$app->make(App\Services\TeamsWebhookNotifier::class)->sendServerStart($context);
