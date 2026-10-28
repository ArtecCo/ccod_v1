<?php

namespace App\Services;

use App\Models\AzureCostForecast;
use App\Models\AzureSubscription;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AzureCostForecastSyncService
{
    private const API_VERSION = '2023-03-01';
    private ?string $accessToken = null;

    public function sync(AzureSubscription $subscription): bool
    {
        $url = sprintf(
            'https://management.azure.com/subscriptions/%s/providers/Microsoft.Consumption/forecasts',
            $subscription->subscription_id,
        );

        $response = $this->request()->get($url, [
            'api-version' => self::API_VERSION,
            'timeframe' => 'MonthToDate',
            'granularity' => 'Monthly',
        ])->throw();

        $rows = $response->json('value', []);
        if (! is_array($rows)) {
            throw new RuntimeException('Invalid Azure forecast response.');
        }

        $amount = null;
        $currency = null;

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $properties = is_array($row['properties'] ?? null) ? $row['properties'] : $row;
            $value = $properties['forecastCost'] ?? $properties['amount'] ?? $properties['forecast'] ?? null;
            $unit = $properties['unit'] ?? $properties['currency'] ?? null;

            if (is_numeric($value)) {
                $amount = (float) $value;
                $currency = is_string($unit) && $unit !== '' ? $unit : $currency;
            }
        }

        if ($amount === null) {
            return false;
        }

        AzureCostForecast::query()->updateOrCreate(
            ['subscription_id' => $subscription->subscription_id],
            [
                'forecast_amount' => $amount,
                'currency' => $currency ?: 'EUR',
                'fetched_at' => now(),
            ],
        );

        return true;
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()
            ->withToken($this->accessToken())
            ->timeout((int) config('services.azure.timeout', 30));
    }

    private function accessToken(): string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $tenantId = config('services.azure.tenant_id');
        $clientId = config('services.azure.client_id');
        $clientSecret = config('services.azure.client_secret');

        if (! $tenantId || ! $clientId || ! $clientSecret) {
            throw new RuntimeException('Azure service principal credentials are not configured.');
        }

        $response = Http::asForm()->acceptJson()->timeout((int) config('services.azure.timeout', 30))->post(
            sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/token', $tenantId),
            [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'https://management.azure.com/.default',
            ],
        )->throw();

        $token = $response->json('access_token');
        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Azure token response did not contain an access token.');
        }

        return $this->accessToken = $token;
    }
}
