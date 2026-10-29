<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AzureCostManagementThrottle
{
    private const MAX_ATTEMPTS = 4;
    private const DEFAULT_DELAY_SECONDS = 2;
    private const MAX_DELAY_SECONDS = 30;

    private const RATE_LIMIT_HEADERS = [
        'x-ms-ratelimit-microsoft.costmanagement-qpu-consumed',
        'x-ms-ratelimit-microsoft.costmanagement-qpu-remaining',
        'x-ms-ratelimit-microsoft.costmanagement-qpu-retry-after',
        'x-ms-ratelimit-microsoft.costmanagement-entity-retry-after',
        'x-ms-ratelimit-microsoft.costmanagement-tenant-retry-after',
        'x-ms-ratelimit-microsoft.costmanagement-client-retry-after',
        'x-ms-ratelimit-microsoft.consumption-retry-after',
        'Retry-After',
    ];

    private const RETRY_AFTER_HEADERS = [
        'x-ms-ratelimit-microsoft.consumption-retry-after',
        'x-ms-ratelimit-microsoft.costmanagement-qpu-retry-after',
        'x-ms-ratelimit-microsoft.costmanagement-entity-retry-after',
        'x-ms-ratelimit-microsoft.costmanagement-tenant-retry-after',
        'x-ms-ratelimit-microsoft.costmanagement-client-retry-after',
        'Retry-After',
    ];

    public function post(PendingRequest $request, string $url, array $payload): Response
    {
        return $this->send(fn () => $request->post($url, $payload), 'POST', $url);
    }

    public function get(PendingRequest $request, string $url): Response
    {
        return $this->send(fn () => $request->get($url), 'GET', $url);
    }

    private function send(callable $request, string $method, string $url): Response
    {
        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            /** @var Response $response */
            $response = $request();
            $this->logRateLimitHeaders($response, $method, $url, $attempt);

            if ($response->status() !== 429 || $attempt === self::MAX_ATTEMPTS) {
                return $response;
            }

            $delay = $this->delaySeconds($response, $attempt);
            Log::warning('Azure Cost Management request throttled; retrying.', [
                'method' => $method,
                'url' => $url,
                'attempt' => $attempt,
                'max_attempts' => self::MAX_ATTEMPTS,
                'delay_seconds' => $delay,
                'rate_limit_headers' => $this->rateLimitHeaders($response),
            ]);

            sleep($delay);
        }

        throw new RuntimeException('Azure Cost Management request retry loop ended unexpectedly.');
    }

    private function logRateLimitHeaders(Response $response, string $method, string $url, int $attempt): void
    {
        $headers = $this->rateLimitHeaders($response);

        Log::info('Azure Cost Management rate-limit diagnostics.', [
            'method' => $method,
            'url' => $url,
            'attempt' => $attempt,
            'status' => $response->status(),
            'rate_limit_headers' => $headers,
        ]);
    }

    private function rateLimitHeaders(Response $response): array
    {
        $headers = [];

        foreach (self::RATE_LIMIT_HEADERS as $header) {
            $value = $response->header($header);

            if ($value !== null && $value !== '') {
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    private function delaySeconds(Response $response, int $attempt): int
    {
        foreach (self::RETRY_AFTER_HEADERS as $header) {
            $retryAfter = $response->header($header);

            if (is_numeric($retryAfter)) {
                return min(self::MAX_DELAY_SECONDS, max(1, (int) ceil((float) $retryAfter)));
            }
        }

        return min(self::MAX_DELAY_SECONDS, self::DEFAULT_DELAY_SECONDS * (2 ** ($attempt - 1)));
    }
}
