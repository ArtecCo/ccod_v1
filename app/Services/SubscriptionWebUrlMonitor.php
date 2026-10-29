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
        $parts = parse_url($webUrl->url);
        $isHttps = strtolower($parts['scheme'] ?? '') === 'https' && ! empty($parts['host']);

        if ($isHttps) {
            $this->checkHttps($webUrl, $parts, $startedAt);
        } else {
            $this->checkHttp($webUrl, $startedAt);
        }

        $webUrl->last_checked_at = now();
        $webUrl->save();

        return $webUrl->refresh();
    }

    private function checkHttps(SubscriptionWebUrl $webUrl, array $parts, float $startedAt): void
    {
        $host = $parts['host'];
        $port = (int) ($parts['port'] ?? 443);

        try {
            if (! function_exists('curl_init')) {
                throw new \RuntimeException('PHP cURL extension is not available.');
            }

            $curl = curl_init($webUrl->url);
            if ($curl === false) {
                throw new \RuntimeException('Unable to initialize cURL.');
            }

            $options = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_USERAGENT => 'CCOD Web URL Monitor/1.0',
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ];

            if (defined('CURLOPT_CERTINFO') && defined('CURLINFO_CERTINFO')) {
                $options[CURLOPT_CERTINFO] = true;
            }

            curl_setopt_array($curl, $options);

            curl_exec($curl);
            $error = curl_error($curl);
            $info = curl_getinfo($curl);
            $certificates = defined('CURLINFO_CERTINFO') ? curl_getinfo($curl, CURLINFO_CERTINFO) : null;
            curl_close($curl);

            $webUrl->response_time_ms = (int) round((microtime(true) - $startedAt) * 1000);
            $status = (int) ($info['http_code'] ?? 0);
            $webUrl->http_status = $status > 0 ? $status : null;
            $webUrl->is_live = $status >= 100 && $status < 500;
            $webUrl->last_check_error = $error !== '' ? $error : null;

            $certificatePem = $this->extractLeafCertificate($certificates);
            if ($certificatePem !== null) {
                $this->applyCertificateData($webUrl, $certificatePem, $host);
            } else {
                $this->clearSslData($webUrl, $status === 0 ? false : null);
            }
        } catch (Throwable $exception) {
            $webUrl->is_live = false;
            $webUrl->http_status = null;
            $webUrl->response_time_ms = (int) round((microtime(true) - $startedAt) * 1000);
            $webUrl->last_check_error = $exception->getMessage();
            $this->clearSslData($webUrl, false);
        }
    }

    private function checkHttp(SubscriptionWebUrl $webUrl, float $startedAt): void
    {
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
            $this->clearSslData($webUrl);
        } catch (Throwable $exception) {
            $webUrl->is_live = false;
            $webUrl->http_status = null;
            $webUrl->response_time_ms = (int) round((microtime(true) - $startedAt) * 1000);
            $webUrl->last_check_error = $exception->getMessage();
            $this->clearSslData($webUrl);
        }
    }

    private function extractLeafCertificate(mixed $certificates): ?string
    {
        if (! is_array($certificates) || ! isset($certificates[0]) || ! is_array($certificates[0])) {
            return null;
        }

        $certificate = $certificates[0]['Cert'] ?? null;

        return is_string($certificate) && trim($certificate) !== '' ? $certificate : null;
    }

    private function applyCertificateData(SubscriptionWebUrl $webUrl, string $certificatePem, string $host): void
    {
        if (! function_exists('openssl_x509_parse')) {
            $this->clearSslData($webUrl);
            return;
        }

        $parsed = openssl_x509_parse($certificatePem);
        if ($parsed === false) {
            $this->clearSslData($webUrl);
            return;
        }

        $validFrom = isset($parsed['validFrom_time_t'])
            ? Carbon::createFromTimestamp((int) $parsed['validFrom_time_t'])
            : null;
        $expiresAt = isset($parsed['validTo_time_t'])
            ? Carbon::createFromTimestamp((int) $parsed['validTo_time_t'])
            : null;
        $hostnameMatches = function_exists('openssl_x509_check_host')
            ? openssl_x509_check_host($certificatePem, $host)
            : true;

        $webUrl->ssl_status = $validFrom !== null
            && $expiresAt !== null
            && now()->betweenIncluded($validFrom, $expiresAt)
            && $hostnameMatches;
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
