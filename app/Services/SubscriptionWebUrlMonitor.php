<?php

namespace App\Services;

use App\Models\SubscriptionWebUrl;
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

        if (! function_exists('curl_init') || ! defined('CURLINFO_CERTINFO')) {
            $this->clearSslData($webUrl, false);

            return;
        }

        $curl = curl_init($webUrl->url);

        if ($curl === false) {
            $this->clearSslData($webUrl, false);

            return;
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
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

        $certificate = is_array($certificates) && isset($certificates[0])
            ? $certificates[0]
            : null;

        if (! is_array($certificate)) {
            $this->clearSslData($webUrl, false);

            return;
        }

        $certificateData = $this->parseCertificateInfo($certificate);

        if ($certificateData === null) {
            $this->clearSslData($webUrl, false);

            return;
        }

        $validFrom = $certificateData['valid_from'];
        $expiresAt = $certificateData['expires_at'];

        $withinValidityPeriod = $validFrom !== null
            && $expiresAt !== null
            && now()->betweenIncluded($validFrom, $expiresAt);

        $webUrl->ssl_status = $withinValidityPeriod;
        $webUrl->ssl_provider = $certificateData['provider'];
        $webUrl->ssl_subject = $certificateData['subject'];
        $webUrl->ssl_issuer = $certificateData['issuer'];
        $webUrl->ssl_valid_from = $validFrom;
        $webUrl->ssl_expires_at = $expiresAt;
        $webUrl->ssl_days_remaining = $expiresAt === null
            ? null
            : (int) now()->diffInDays($expiresAt, false);
    }

    /**
     * @param array<int, string> $certificate
     * @return array<string, mixed>|null
     */
    private function parseCertificateInfo(array $certificate): ?array
    {
        $values = [];

        foreach ($certificate as $line) {
            if (! is_string($line)) {
                continue;
            }

            $separator = strpos($line, ':');

            if ($separator === false) {
                continue;
            }

            $key = trim(substr($line, 0, $separator));
            $value = trim(substr($line, $separator + 1));

            if ($key !== '') {
                $values[$key] = $value;
            }
        }

        $validFrom = $this->parseCertificateDate($values['Start date'] ?? null);
        $expiresAt = $this->parseCertificateDate($values['Expire date'] ?? null);
        $subject = $this->extractDistinguishedNameValue($values['Subject'] ?? null, 'CN');
        $provider = $this->extractDistinguishedNameValue($values['Issuer'] ?? null, 'O')
            ?? $this->extractDistinguishedNameValue($values['Issuer'] ?? null, 'CN');
        $issuer = $this->extractDistinguishedNameValue($values['Issuer'] ?? null, 'CN');

        if ($validFrom === null && $expiresAt === null && $subject === null && $issuer === null) {
            return null;
        }

        return [
            'valid_from' => $validFrom,
            'expires_at' => $expiresAt,
            'subject' => $subject,
            'provider' => $provider,
            'issuer' => $issuer,
        ];
    }

    private function parseCertificateDate(?string $value): mixed
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        try {
            return now()->parse($value);
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
