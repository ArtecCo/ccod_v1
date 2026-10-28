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
    private const API_VERSION = '2025-03-01';
    private ?string $accessToken = null;

    public function sync(AzureSubscription $subscription): bool
    {
        $now = CarbonImmutable::now();
        $url = sprintf(
            'https://management.azure.com/subscriptions/%s/providers/Microsoft.CostManagement/forecast',
            $subscription->subscription_id,
        );

        $payload = [
            'type' => 'Usage',
            'dataset' => [
                'aggregation' => [
                    'totalCost' => [
                        'name' => 'Cost',
                        'function' => 'Sum',
                    ],
                ],
                'granularity' => 'Daily',
            ],
            'includeActualCost' => false,
            'includeFreshPartialCost' => false,
            'timePeriod' => [
                'from' => $now->startOfMonth()->toIso8601String(),
                'to' => $now->endOfMonth()->toIso8601String(),
            ],
            'timeframe' => 'Custom',
        ];

        $response = $this->request()->post($url, $payload);
        if ($response->status() === 204) {
            return false;
        }
        $response->throw();

        $properties = $response->json('properties', []);
        if (! is_array($properties)) {
            throw new RuntimeException('Invalid Azure forecast response properties.');
        }

        $columns = collect($properties['columns'] ?? [])
            ->map(fn (mixed $column): ?string => is_array($column) && is_string($column['name'] ?? null) ? $column['name'] : null)
            ->filter()
            ->values()
            ->all();

        $amount = 0.0;
        $hasAmount = false;
        $currency = null;

        foreach (($properties['rows'] ?? []) as $values) {
            if (! is_array($values)) {
                continue;
            }

            $row = [];
            foreach ($columns as $index => $column) {
                $row[$column] = $values[$index] ?? null;
            }

            $value = $row['PreTaxCost'] ?? $row['Cost'] ?? $row['CostUSD'] ?? $row['PreTaxCostUSD'] ?? null;
            if (is_numeric($value)) {
                $amount += (float) $value;
                $hasAmount = true;
            }

            $rowCurrency = $row['Currency'] ?? null;
            if (is_string($rowCurrency) && $rowCurrency !== '') {
                $currency ??= $rowCurrency;
            }
        }

        if (! $hasAmount) {
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
            ->withQueryParameters(['api-version' => self::API_VERSION])
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
