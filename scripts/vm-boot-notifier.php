<?php

declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'cloud_ops_dashboard';
const DB_USER = 'ccod_system_tasks';
const DB_PASSWORD = 'YOUR_STRONG_PASSWORD';

const UTC_TIMEZONE = 'UTC';
const IST_TIMEZONE = 'Asia/Kolkata';

date_default_timezone_set(IST_TIMEZONE);

function getBootTime(): DateTimeImmutable
{
    $command = 'powershell.exe -NoProfile -NonInteractive -Command ' .
        '"(Get-CimInstance Win32_OperatingSystem).LastBootUpTime.ToString(' .
        '\'yyyy-MM-dd HH:mm:ss\'' .
        ')"';

    $output = shell_exec($command);

    if (! $output) {
        throw new RuntimeException('Unable to determine Windows boot time.');
    }

    $output = trim($output);

    // Windows VM clock is UTC.
    $bootTimeUtc = DateTimeImmutable::createFromFormat(
        'Y-m-d H:i:s',
        $output,
        new DateTimeZone(UTC_TIMEZONE)
    );

    if (! $bootTimeUtc) {
        throw new RuntimeException(
            'Unable to parse Windows boot time: ' . $output
        );
    }

    // Convert the actual UTC boot time to Indian Standard Time.
    return $bootTimeUtc->setTimezone(
        new DateTimeZone(IST_TIMEZONE)
    );
}

function createUuid(): string
{
    $data = random_bytes(16);

    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf(
        '%s%s-%s-%s-%s-%s%s%s',
        str_split(bin2hex($data), 4)
    );
}

$pdo = null;

try {
    $bootTime = getBootTime();

    $bootTimeText = $bootTime->format(
        'd F Y, h:i:s A \I\S\T'
    );

    $pdo = new PDO(
        sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_NAME
        ),
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $users = $pdo->query(
        'SELECT id
         FROM users
         WHERE is_active = 1'
    )->fetchAll();

    $insert = $pdo->prepare(
        'INSERT INTO notifications
            (
                id,
                type,
                notifiable_type,
                notifiable_id,
                data,
                read_at,
                created_at,
                updated_at
            )
         VALUES
            (
                :id,
                :type,
                :notifiable_type,
                :notifiable_id,
                :data,
                NULL,
                :created_at,
                :updated_at
            )'
    );

    // Database timestamps are stored using the VM's UTC clock.
    $nowUtc = new DateTimeImmutable(
        'now',
        new DateTimeZone(UTC_TIMEZONE)
    );

    $data = json_encode(
        [
            'title' => 'CCOD server started',
            'message' => "The CCOD server was started at {$bootTimeText}.",
            'type' => 'system',
            'severity' => 'info',
            'action_url' => null,
            'action_label' => null,
        ],
        JSON_THROW_ON_ERROR
    );

    $pdo->beginTransaction();

    $count = 0;

    foreach ($users as $user) {
        $insert->execute([
            'id' => createUuid(),
            'type' => 'App\\Notifications\\ClientPortalNotification',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $user['id'],
            'data' => $data,
            'created_at' => $nowUtc->format('Y-m-d H:i:s'),
            'updated_at' => $nowUtc->format('Y-m-d H:i:s'),
        ]);

        $count++;
    }

    $pdo->commit();

    echo sprintf(
        "[%s IST] Boot notification created for %d active user(s). Boot time: %s\n",
        (new DateTimeImmutable(
            'now',
            new DateTimeZone(IST_TIMEZONE)
        ))->format('Y-m-d H:i:s'),
        $count,
        $bootTimeText
    );

    exit(0);

} catch (Throwable $e) {

    if ($pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    file_put_contents(
        __DIR__ . DIRECTORY_SEPARATOR . 'vm-boot-notification-error.log',
        sprintf(
            "[%s IST] %s\n%s\n\n",
            (new DateTimeImmutable(
                'now',
                new DateTimeZone(IST_TIMEZONE)
            ))->format('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getTraceAsString()
        ),
        FILE_APPEND
    );

    exit(1);
}