<?php

namespace App\Services;

use App\Models\SubscriptionWebUrl;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Throwable;

class SubscriptionWebUrlMonitor
{
    public function check(SubscriptionWebUrl $webUrl): SubscriptionWebUrl
    {
        $startedAt = microtime(true);

        try {
            $response = Http::timeout(10)
                ->connectTimeout(5)
                ->withHeaders(['User-Agent' => 'CCOD Web URL Monitor/1.0'])
                ->get($webUrl->url);

            $webUrl->is_live = $response->successful();
            $webUrl->http_status = $response->status();
            $webUrl->response_time_ms = (int) round((microtime(true) - $startedAt) * 1000);
            $webUrl->last_check_error = null;
        } catch (Throwable $exception) {
            $webUrl->is_live = false;
            $webUrl->http_status = null;
            $webUrl->response_time_ms = (int) round((microtime(true) - $startedAt) * 1000);
            $webUrl->last_check_error = $exception->getMessage();
        }

        $this->checkSsl($webUrl);
        $webUrl->last_checked_at = now();
        $webUrl->save();

        return $webUrl->refresh();
    }

    private function checkSsl(SubscriptionWebUrl $webUrl): void
    {
        $parts = parse_url($webUrl->url);

        if (($parts['scheme'] ?? null) !== 'https' || empty($parts['host'])) {
            $webUrl->ssl_status = null;
            $webUrl->ssl_provider = null;
            $webUrl->ssl_subject = null;
            $webUrl->ssl_issuer = null;
            $webUrl->ssl_valid_from = null;
            $webUrl->ssl_expires_at = null;
            $webUrl->ssl_days_remaining = null;

            return;
        }

        $host = $parts['host'];
        $port = (int) ($parts['port'] ?? 443);
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => true,
                'verify_peer_name' => true,
                'peer_name' => $host,
            ],
        ]);

        $socket = @stream_socket_client(
            "ssl://{$host}:{$port}",
            $errno,
            $error,
            5,
            STREAM_CLIENT_CONNECT,
            $context,
        );

        if ($socket === false) {
            $webUrl->ssl_status = false;
            $webUrl->ssl_provider = null;
            $webUrl->ssl_subject = null;
            $webUrl->ssl_issuer = null;
            $webUrl->ssl_valid_from = null;
            $webUrl->ssl_expires_at = null;
            $webUrl->ssl_days_remaining = null;

            return;
        }

        $options = stream_context_get_options($socket);
        fclose($socket);

        $certificate = $options['ssl']['peer_certificate'] ?? null;

        if ($certificate === null) {
            $webUrl->ssl_status = false;

            return;
        }

        $parsed = openssl_x509_parse($certificate);

        if ($parsed === false) {
            $webUrl->ssl_status = false;

            return;
        }

        $validFrom = isset($parsed['validFrom_time_t'])
            ? now()->createFromTimestamp($parsed['validFrom_time_t'])
            : null;
        $expiresAt = isset($parsed['validTo_time_t'])
            ? now()->createFromTimestamp($parsed['validTo_time_t'])
            : null;

        $webUrl->ssl_status = $expiresAt === null || $expiresAt->isFuture();
        $webUrl->ssl_provider = $parsed['issuer']['O'] ?? $parsed['issuer']['CN'] ?? null;
        $webUrl->ssl_subject = $parsed['subject']['CN'] ?? null;
        $webUrl->ssl_issuer = $parsed['issuer']['CN'] ?? null;
        $webUrl->ssl_valid_from = $validFrom;
        $webUrl->ssl_expires_at = $expiresAt;
        $webUrl->ssl_days_remaining = $expiresAt === null ? null : now()->diffInDays($expiresAt, false);
    }
}
