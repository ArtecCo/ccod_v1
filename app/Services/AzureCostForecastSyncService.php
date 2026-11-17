<?php

namespace App\Services;

use App\Models\AzureCostForecast;
use App\Models\AzureCostForecastSnapshot;
use App\Models\AzureSubscription;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AzureCostForecastSyncService
{
    private const API_VERSION = '2025-03-01';
    private const RETENTION_MONTHS = 12;

    private ?string $accessToken = null;

    public function __construct(private readonly AzureCostManagementThrottle $throttle) {}

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

        $response = $this->throttle->post($this->request(), $url, $payload);
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

        $dailyForecasts = [];
        $currency = null;

        foreach (($properties['rows'] ?? []) as $values) {
            if (! is_array($values)) {
                continue;
            }

            $row = [];
            foreach ($columns as $index => $column) {
                $row[$column] = $values[$index] ?? null;
            }

            // Keep forecast predictions only; never archive actual-cost rows as forecasts.
            if (($row['CostStatus'] ?? null) !== 'Forecast') {
                continue;
            }

            $value = $row['PreTaxCost'] ?? $row['Cost'] ?? $row['CostUSD'] ?? $row['PreTaxCostUSD'] ?? null;
            $usageDate = $row['UsageDate'] ?? null;

            if (! is_numeric($value) || ! is_numeric($usageDate)) {
                continue;
            }

            $date = CarbonImmutable::createFromFormat('!Ymd', (string) (int) $usageDate);
            if ($date === false) {
                continue;
            }

            $period = $date->startOfMonth()->toDateString();
            $dailyForecasts[$period] = ($dailyForecasts[$period] ?? 0.0) + (float) $value;

            $rowCurrency = $row['Currency'] ?? null;
            if (is_string($rowCurrency) && $rowCurrency !== '') {
                if ($currency !== null && $currency !== $rowCurrency) {
                    throw new RuntimeException('Azure returned multiple currencies for one subscription forecast.');
                }
                $currency = $rowCurrency;
            }
        }

        if ($dailyForecasts === []) {
            return false;
        }

        $capturedOn = $now->toDateString();
        $totalForecast = array_sum($dailyForecasts);

        foreach ($dailyForecasts as $period => $amount) {
            AzureCostForecastSnapshot::query()->updateOrCreate(
                [
                    'subscription_id' => $subscription->subscription_id,
                    'forecast_period' => $period,
                    'captured_on' => $capturedOn,
                ],
                [
                    'forecast_amount' => $amount,
                    'currency' => $currency,
                    'captured_at' => $now,
                    'synced_at' => $now,
                ],
            );
        }

        // Retain a rolling 12 months of daily forecast snapshots.
        AzureCostForecastSnapshot::query()
            ->where('subscription_id', $subscription->subscription_id)
            ->where('captured_on', '<', $now->subMonths(self::RETENTION_MONTHS)->toDateString())
            ->delete();

        // Preserve the existing latest-forecast record for current dashboard consumers.
        AzureCostForecast::query()->updateOrCreate(
            ['subscription_id' => $subscription->subscription_id],
            [
                'forecast_amount' => $totalForecast,
                'currency' => $currency,
                'fetched_at' => $now,
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
