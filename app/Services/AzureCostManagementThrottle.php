<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use RuntimeException;

class AzureCostManagementThrottle
{
    private const MAX_ATTEMPTS = 4;
    private const DEFAULT_DELAY_SECONDS = 2;
    private const MAX_DELAY_SECONDS = 30;

    /**
     * Azure Cost Management exposes throttle-specific retry headers.
     * The Consumption header is the documented retry-after header for 429s;
     * the Cost Management QPU/entity/tenant/client headers are also used by
     * Cost Management for more specific throttling conditions.
     */
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
        return $this->send(fn () => $request->post($url, $payload));
    }

    public function get(PendingRequest $request, string $url): Response
    {
        return $this->send(fn () => $request->get($url));
    }

    private function send(callable $request): Response
    {
        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            /** @var Response $response */
            $response = $request();

            if ($response->status() !== 429 || $attempt === self::MAX_ATTEMPTS) {
                return $response;
            }

            sleep($this->delaySeconds($response, $attempt));
        }

        throw new RuntimeException('Azure Cost Management request retry loop ended unexpectedly.');
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
