<?php

/**
 * Shared Azure service-principal helper for local/VM diagnostic scripts.
 *
 * Usage: require __DIR__ . '/azure_client.php';
 *
 * These scripts intentionally read credentials from .env and never print
 * AZURE_CLIENT_SECRET or the bearer token.
 */

declare(strict_types=1);

use Dotenv\Dotenv;

$projectRoot = dirname(__DIR__, 2);

require_once $projectRoot . '/vendor/autoload.php';

Dotenv::createImmutable($projectRoot)->safeLoad();

function azureEnv(string $key): string
{
    $value = $_ENV[$key] ?? getenv($key) ?: '';

    if ($value === '') {
        throw new RuntimeException("Missing {$key} in .env");
    }

    return $value;
}

function azureAccessToken(): string
{
    $tenantId = azureEnv('AZURE_TENANT_ID');
    $clientId = azureEnv('AZURE_CLIENT_ID');
    $clientSecret = azureEnv('AZURE_CLIENT_SECRET');
    $timeout = (int) (($_ENV['AZURE_API_TIMEOUT'] ?? getenv('AZURE_API_TIMEOUT')) ?: 30);

    $ch = curl_init("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token");

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'https://management.azure.com/.default',
        ]),
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
    ]);

    $body = curl_exec($ch);
    $error = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($body === false) {
        throw new RuntimeException("Token request failed: {$error}");
    }

    $json = json_decode($body, true);

    if ($status < 200 || $status >= 300 || ! isset($json['access_token'])) {
        $message = $json['error_description'] ?? $json['error'] ?? 'Unknown Azure authentication error';
        throw new RuntimeException("Azure token request returned HTTP {$status}: {$message}");
    }

    return (string) $json['access_token'];
}

/**
 * @return array{status:int, headers:array<string,string>, body:array<string,mixed>}
 */
function azureGet(string $url, string $token): array
{
    $timeout = (int) (($_ENV['AZURE_API_TIMEOUT'] ?? getenv('AZURE_API_TIMEOUT')) ?: 30);

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
    ]);

    $rawBody = curl_exec($ch);
    $error = curl_error($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    curl_close($ch);

    if ($rawBody === false) {
        throw new RuntimeException("Azure request failed: {$error}");
    }

    $headerText = substr($rawBody, 0, $headerSize);
    $bodyText = substr($rawBody, $headerSize);
    $headers = [];

    foreach (preg_split('/\r\n|\r|\n/', $headerText) ?: [] as $line) {
        if (! str_contains($line, ':')) {
            continue;
        }

        [$name, $value] = explode(':', $line, 2);
        $headers[strtolower(trim($name))] = trim($value);
    }

    $body = json_decode($bodyText, true);

    if (! is_array($body)) {
        $body = ['raw' => $bodyText];
    }

    return [
        'status' => $status,
        'headers' => $headers,
        'body' => $body,
    ];
}

function azurePrintError(array $response): void
{
    $body = $response['body'];
    $message = $body['error']['message']
        ?? $body['error_description']
        ?? $body['message']
        ?? 'Unknown Azure API error';

    fwrite(STDERR, "Azure API returned HTTP {$response['status']}: {$message}\n");
}
