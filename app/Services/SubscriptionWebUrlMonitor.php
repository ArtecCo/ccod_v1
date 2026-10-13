<?php

namespace App\Services;

use App\Models\SubscriptionWebUrl;
use Carbon\Carbon;
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
                    'allow_redirects' => ['max' => 5, 'strict' => true, 'referer' => true],
                ])
                ->withHeaders(['User-Agent' => 'CCOD Web URL Monitor/1.0'])
                ->get($webUrl->url);

            $status = $response->status();
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
        if (strtolower($parts['scheme'] ?? '') !== 'https' || empty($parts['host'])) {
            $this->clearSslData($webUrl);
            return;
        }

        $host = $parts['host'];
        $port = (int) ($parts['port'] ?? 443);

        if ($this->checkSslWithCurl($webUrl, $host, $port)) {
            return;
        }

        $this->checkSslWithStream($webUrl, $host, $port);
    }

    private function checkSslWithCurl(SubscriptionWebUrl $webUrl, string $host, int $port): bool
    {
        if (! function_exists('curl_init') || ! defined('CURLINFO_CERTINFO') || ! defined('CURLOPT_CERTINFO')) {
            return false;
        }

        $curl = curl_init("https://{$host}:{$port}/");
        if ($curl === false) {
            return false;
        }

        curl_setopt_array($curl, [
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'CCOD Web URL Monitor/1.0',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_CERTINFO => true,
        ]);

        curl_exec($curl);
        $certificates = curl_getinfo($curl, CURLINFO_CERTINFO);
        curl_close($curl);

        if (! is_array($certificates) || ! isset($certificates[0]) || ! is_array($certificates[0])) {
            return false;
        }

        $values = [];
        foreach ($certificates[0] as $line) {
            if (! is_string($line)) {
                continue;
            }
            $separator = strpos($line, ':');
            if ($separator === false) {
                continue;
            }
            $values[trim(substr($line, 0, $separator))] = trim(substr($line, $separator + 1));
        }

        $validFrom = $this->parseCertificateDate($values['Start date'] ?? null);
        $expiresAt = $this->parseCertificateDate($values['Expire date'] ?? null);
        $subject = $this->extractDistinguishedNameValue($values['Subject'] ?? null, 'CN');
        $provider = $this->extractDistinguishedNameValue($values['Issuer'] ?? null, 'O')
            ?? $this->extractDistinguishedNameValue($values['Issuer'] ?? null, 'CN');
        $issuer = $this->extractDistinguishedNameValue($values['Issuer'] ?? null, 'CN');

        if ($validFrom === null && $expiresAt === null && $subject === null && $issuer === null) {
            return false;
        }

        $this->applyCertificateData($webUrl, $validFrom, $expiresAt, $provider, $subject, $issuer, $host);
        return true;
    }

    private function checkSslWithStream(SubscriptionWebUrl $webUrl, string $host, int $port): void
    {
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

        $socket = @stream_socket_client("ssl://{$host}:{$port}", $errno, $error, 10, STREAM_CLIENT_CONNECT, $context);
        if ($socket === false) {
            $this->clearSslData($webUrl, false);
            return;
        }

        $options = stream_context_get_options($socket);
        $certificate = $options['ssl']['peer_certificate'] ?? null;
        if ($certificate === null) {
            $params = stream_context_get_params($socket);
            $certificate = $params['options']['ssl']['peer_certificate'] ?? null;
        }

        if ($certificate === null) {
            fclose($socket);
            $this->clearSslData($webUrl, false);
            return;
        }

        $parsed = openssl_x509_parse($certificate);
        fclose($socket);
        if ($parsed === false) {
            $this->clearSslData($webUrl, false);
            return;
        }

        $validFrom = isset($parsed['validFrom_time_t']) ? Carbon::createFromTimestamp((int) $parsed['validFrom_time_t']) : null;
        $expiresAt = isset($parsed['validTo_time_t']) ? Carbon::createFromTimestamp((int) $parsed['validTo_time_t']) : null;

        $hostnameMatches = function_exists('openssl_x509_check_host') ? openssl_x509_check_host($certificate, $host) : true;
        $this->applyCertificateData(
            $webUrl,
            $validFrom,
            $expiresAt,
            $parsed['issuer']['O'] ?? $parsed['issuer']['CN'] ?? null,
            $parsed['subject']['CN'] ?? null,
            $parsed['issuer']['CN'] ?? null,
            $host,
            $hostnameMatches,
        );
    }

    private function applyCertificateData(
        SubscriptionWebUrl $webUrl,
        ?Carbon $validFrom,
        ?Carbon $expiresAt,
        ?string $provider,
        ?string $subject,
        ?string $issuer,
        string $host,
        ?bool $hostnameMatches = null,
    ): void {
        $hostnameMatches ??= true;
        $webUrl->ssl_status = $validFrom !== null
            && $expiresAt !== null
            && now()->betweenIncluded($validFrom, $expiresAt)
            && $hostnameMatches;
        $webUrl->ssl_provider = $provider;
        $webUrl->ssl_subject = $subject;
        $webUrl->ssl_issuer = $issuer;
        $webUrl->ssl_valid_from = $validFrom;
        $webUrl->ssl_expires_at = $expiresAt;
        $webUrl->ssl_days_remaining = $expiresAt === null ? null : (int) now()->diffInDays($expiresAt, false);
    }

    private function parseCertificateDate(?string $value): ?Carbon
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }

    private function extractDistinguishedNameValue(?string $value, string $key): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (preg_match('/(?:^|,\s*)'.preg_quote($key, '/').'\s*=\s*(?:"([^"]+)"|([^,]+))/i', $value, $matches)) {
            return trim($matches[1] !== '' ? $matches[1] : $matches[2]);
        }
        return null;
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
