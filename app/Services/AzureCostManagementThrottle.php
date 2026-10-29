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
        $retryAfter = $response->header('Retry-After');

        if (is_numeric($retryAfter)) {
            return min(self::MAX_DELAY_SECONDS, max(1, (int) $retryAfter));
        }

        return min(self::MAX_DELAY_SECONDS, self::DEFAULT_DELAY_SECONDS * (2 ** ($attempt - 1)));
    }
}
