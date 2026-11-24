<?php

declare(strict_types=1);

$basePath = dirname(__DIR__, 2);

define('LARAVEL_START', microtime(true));
require $basePath.'/vendor/autoload.php';
$app = require $basePath.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$runPowerShell = static function (string $script): string {
    // Suppress PowerShell's first-use module progress output so it cannot corrupt captured JSON/text.
    $script = '$ProgressPreference = \'SilentlyContinue\';'."\n".$script;
    $encodedScript = base64_encode(iconv('UTF-8', 'UTF-16LE', $script));
    $command = 'powershell.exe -NoProfile -NonInteractive -ExecutionPolicy Bypass -EncodedCommand '.$encodedScript;
    $output = [];
    exec($command.' 2>&1', $output);

    return trim(implode(PHP_EOL, $output));
};

$serviceInfo = static function (string $service) use ($runPowerShell): array {
    $name = str_replace("'", "''", $service);
    $script = <<<'PS'
$service = Get-CimInstance Win32_Service -Filter "Name='__SERVICE_NAME__'"
if ($service) {
    $started = 'Not available'
    if ($service.ProcessId -gt 0) {
        $process = Get-CimInstance Win32_Process -Filter "ProcessId=$($service.ProcessId)"
        if ($process -and $process.CreationDate) { $ist = [TimeZoneInfo]::FindSystemTimeZoneById('India Standard Time'); $started = [TimeZoneInfo]::ConvertTime([datetime]$process.CreationDate, $ist).ToString("dd-MMM-yyyy hh:mm:ss tt 'IST'") }
    }
    [pscustomobject]@{ State=$service.State; StartedAt=$started } | ConvertTo-Json -Compress
}
PS;
    $script = str_replace('__SERVICE_NAME__', $name, $script);
    $data = json_decode($runPowerShell($script), true);

    return [
        'status' => is_array($data) ? (string) ($data['State'] ?? 'NOT DETECTED') : 'NOT DETECTED',
        'started_at' => is_array($data) ? (string) ($data['StartedAt'] ?? 'Not available') : 'Not available',
    ];
};

$phpScript = <<<'PS'
$appcmd = Join-Path $env:windir 'System32\inetsrv\appcmd.exe'
if (-not (Test-Path $appcmd)) {
    'NOT CONFIGURED|IIS appcmd.exe not found'
    exit
}

$fastCgiConfig = & $appcmd list config /section:system.webServer/fastCgi 2>$null | Out-String
$handlerConfig = & $appcmd list config /section:system.webServer/handlers 2>$null | Out-String

$hasFastCgi = $fastCgiConfig -match 'fullPath=.*php-cgi\.exe'
$hasPhpHandler = $handlerConfig -match 'FastCgiModule' -and $handlerConfig -match '\*\.php'

if ($hasFastCgi -and $hasPhpHandler) {
    'CONFIGURED|Managed by IIS FastCGI (on demand)'
} elseif ($hasFastCgi) {
    'PARTIALLY CONFIGURED|FastCGI PHP executable is registered, but the PHP handler mapping was not found'
} else {
    'NOT CONFIGURED|PHP FastCGI registration was not found in IIS'
}
PS;
$phpOutput = $runPowerShell($phpScript);
$phpWorkerScript = <<<'PS'
$process = Get-CimInstance Win32_Process -Filter "Name='w3wp.exe'" |
    Where-Object { $_.CommandLine -match '-ap\s+["'']?CCOD["'']?(\s|$)' } |
    Sort-Object CreationDate |
    Select-Object -First 1
if ($process -and $process.CreationDate) {
    [TimeZoneInfo]::ConvertTime([datetime]$process.CreationDate, [TimeZoneInfo]::FindSystemTimeZoneById('India Standard Time')).ToString("dd-MMM-yyyy hh:mm:ss tt 'IST'")
} else {
    'Not available'
}
PS;
$phpWorkerStartedAt = $runPowerShell($phpWorkerScript);

$phpParts = explode('|', trim($phpOutput), 2);
$php = [
    'Status' => trim($phpParts[0] ?? '') ?: 'NOT CONFIGURED',
    'StartedAt' => trim($phpParts[1] ?? '') ?: 'Not available',
];

$queueScript = <<<'PS'
$process = Get-CimInstance Win32_Process | Where-Object { $_.Name -ieq 'php.exe' -and $_.CommandLine -match 'artisan queue:work' } | Sort-Object CreationDate | Select-Object -First 1
$result = if ($process) { [pscustomobject]@{Status='RUNNING';StartedAt=[TimeZoneInfo]::ConvertTime([datetime]$process.CreationDate, [TimeZoneInfo]::FindSystemTimeZoneById('India Standard Time')).ToString("dd-MMM-yyyy hh:mm:ss tt 'IST'")} } else { [pscustomobject]@{Status='NOT RUNNING';StartedAt='Not available'} }
$result | ConvertTo-Json -Compress
PS;
$queue = json_decode($runPowerShell($queueScript), true) ?: ['Status' => 'NOT RUNNING', 'StartedAt' => 'Not available'];

$bootTimeScript = <<<'PS'
$bootTime = (Get-CimInstance Win32_OperatingSystem).LastBootUpTime
if ($bootTime) {
    [TimeZoneInfo]::ConvertTime([datetime]$bootTime, [TimeZoneInfo]::FindSystemTimeZoneById('India Standard Time')).ToString("dd-MMM-yyyy hh:mm:ss tt 'IST'")
} else {
    'Not available'
}
PS;
$serverBootTime = $runPowerShell($bootTimeScript);

$mysql = $serviceInfo('MySQL2610');
$w3svc = $serviceInfo('W3SVC');
$was = $serviceInfo('WAS');

$context = [
    'server_boot_time' => $serverBootTime !== '' ? $serverBootTime : 'Not available',
    'iis_status' => ($w3svc['status'] === 'Running' && $was['status'] === 'Running') ? 'RUNNING' : 'NOT RUNNING',
    'iis_started_at' => $w3svc['started_at'],
    'php_status' => $php['Status'],
    'php_started_at' => $php['StartedAt'],
    'iis_worker_started_at' => $phpWorkerStartedAt !== '' ? $phpWorkerStartedAt : 'Not available',
    'mysql_status' => strtoupper($mysql['status']),
    'mysql_started_at' => $mysql['started_at'],
    'queue_status' => $queue['Status'],
    'queue_started_at' => $queue['StartedAt'],
];

$app->make(App\Services\NotificationRuleService::class)->trigger('server.started', $context);
