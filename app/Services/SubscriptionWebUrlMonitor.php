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
                ->withOptions([
                    'allow_redirects' => [
                        'max' => 5,
                        'strict' => true,
                        'referer' => true,
                    ],
                ])
                ->withHeaders(['User-Agent' => 'CCOD Web URL Monitor/1.0'])
                ->get($webUrl->url);

            $status = $response->status();

            // A URL is considered live when the remote server responded.
            // 4xx responses still prove the host/application is reachable;
            // 5xx responses are treated as unavailable/unhealthy.
            $webUrl->is_live = $status >= 100 && $status < 500;
            $webUrl->http_status = $status;
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
            $this->clearSslData($webUrl);

            return;
        }

        $host = $parts['host'];
        $port = (int) ($parts['port'] ?? 443);
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'peer_name' => $host,
                'SNI_enabled' => true,
                'disable_compression' => true,
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
            $this->clearSslData($webUrl, false);

            return;
        }

        $options = stream_context_get_options($socket);
        fclose($socket);

        $certificate = $options['ssl']['peer_certificate'] ?? null;

        if ($certificate === null) {
            $this->clearSslData($webUrl, false);

            return;
        }

        $parsed = openssl_x509_parse($certificate);

        if ($parsed === false) {
            $this->clearSslData($webUrl, false);

            return;
        }

        $validFromTimestamp = $parsed['validFrom_time_t'] ?? null;
        $expiresTimestamp = $parsed['validTo_time_t'] ?? null;

        $validFrom = $validFromTimestamp !== null
            ? now()->createFromTimestamp((int) $validFromTimestamp)
            : null;
        $expiresAt = $expiresTimestamp !== null
            ? now()->createFromTimestamp((int) $expiresTimestamp)
            : null;

        $withinValidityPeriod = $validFrom !== null
            && $expiresAt !== null
            && now()->betweenIncluded($validFrom, $expiresAt);
        $hostnameMatches = function_exists('openssl_x509_check_host')
            ? openssl_x509_check_host($certificate, $host)
            : true;

        $webUrl->ssl_status = $withinValidityPeriod && $hostnameMatches;
        $webUrl->ssl_provider = $parsed['issuer']['O'] ?? $parsed['issuer']['CN'] ?? null;
        $webUrl->ssl_subject = $parsed['subject']['CN'] ?? null;
        $webUrl->ssl_issuer = $parsed['issuer']['CN'] ?? null;
        $webUrl->ssl_valid_from = $validFrom;
        $webUrl->ssl_expires_at = $expiresAt;
        $webUrl->ssl_days_remaining = $expiresAt === null
            ? null
            : (int) now()->diffInDays($expiresAt, false);
    }

    private function clearSslData(SubscriptionWebUrl $webUrl, ?bool $status = null): void
    {
        $webUrl->ssl_status = $status;
        $webUrl->ssl_provider = null;
        $webUrl->ssl_subject = null;
        $webUrl->ssl_issuer = null;
        $webUrl->ssl_valid_from = null;
        $webUrl->ssl_expires_at = null;
        $webUrl->ssl_days_remaining = null;
    }
}
