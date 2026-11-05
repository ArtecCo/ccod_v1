<?php

declare(strict_types=1);

namespace App\\Services;

use Illuminate\\Http\\Client\\PendingRequest;
use Illuminate\\Http\\Client\\Response;
use Illuminate\\Support\\Facades\\Log;
use RuntimeException;

class AzureCostManagementThrottle
{
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
        $maxAttempts = max(1, (int) config('services.azure.retry_attempts', 6));

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            /** @var Response $response */
            $response = $request();
            $status = $response->status();

            if (! in_array($status, [408, 429, 500, 502, 503, 504], true) || $attempt === $maxAttempts) {
                return $response;
            }

            $delay = $this->retryDelaySeconds($response, $attempt);
            Log::warning('Azure API request will be retried.', [
                'method' => $method,
                'url' => $url,
                'status' => $status,
                'attempt' => $attempt,
                'max_attempts' => $maxAttempts,
                'delay_seconds' => $delay,
                'retry_headers' => $this->rateLimitHeaders($response),
            ]);

            sleep($delay);
        }

        throw new RuntimeException('Azure API retry loop ended unexpectedly.');
    }

    private function retryDelaySeconds(Response $response, int $attempt): int
    {
        $delays = [];

        foreach ([
            'x-ms-ratelimit-microsoft.consumption-retry-after',
            'x-ms-ratelimit-microsoft.costmanagement-qpu-retry-after',
            'x-ms-ratelimit-microsoft.costmanagement-entity-retry-after',
            'x-ms-ratelimit-microsoft.costmanagement-tenant-retry-after',
            'x-ms-ratelimit-microsoft.costmanagement-client-retry-after',
            'Retry-After',
        ] as $header) {
            $value = $response->header($header);
            if (! is_string($value) || trim($value) === '') {
                continue;
            }

            $value = trim($value);
            if (is_numeric($value)) {
                $delays[] = max(1, (int) ceil((float) $value));
                continue;
            }

            $timestamp = strtotime($value);
            if ($timestamp !== false) {
                $delays[] = max(1, $timestamp - time());
            }
        }

        if ($delays !== []) {
            // Honor the longest server-provided delay; never shorten Retry-After.
            return max($delays);
        }

        $base = max(1, (int) config('services.azure.retry_base_delay', 2));
        $exponential = $base * (2 ** min(10, $attempt - 1));
        return (int) ceil($exponential + random_int(0, max(1, (int) ceil($exponential * 0.2))));
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
}
