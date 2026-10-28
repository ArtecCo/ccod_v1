<?php

namespace App\Services;

use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AzureCostSyncService
{
    private const SOURCE = 'azure_cost_management';
    private const GRANULARITY = 'monthly';
    private const API_VERSION = '2025-03-01';

    private ?string $accessToken = null;

    /**
     * Synchronize cost data for one subscription.
     *
     * The first sync (or an explicitly forced historical sync) fetches the
     * rolling 12-month window. Normal syncs only refresh the current MTD
     * month, leaving completed months in the database untouched.
     */
    public function syncSubscription(
        AzureSubscription $subscription,
        bool $forceHistorical = false,
    ): array {
        $hasHistoricalData = AzureSubscriptionCost::query()
            ->where('subscription_id', $subscription->subscription_id)
            ->where('granularity', self::GRANULARITY)
            ->exists();

        if ($forceHistorical || ! $hasHistoricalData) {
            return $this->syncHistorical($subscription);
        }

        return $this->syncMonthToDate($subscription);
    }

    /**
     * Refresh the rolling 12-month monthly cost history.
     */
    public function syncHistorical(AzureSubscription $subscription): array
    {
        $now = CarbonImmutable::now();
        $from = $now->subMonths(11)->startOfMonth();
        $to = $now->endOfMonth();

        $rows = $this->queryCost(
            $subscription->subscription_id,
            'Custom',
            $from,
            $to,
        );

        $indexed = [];
        foreach ($rows as $row) {
            $periodStart = $this->periodStartFromRow($row);

            if ($periodStart === null) {
                continue;
            }

            $indexed[$periodStart->toDateString()] = [
                'amount' => (float) ($row['PreTaxCost'] ?? 0),
                'currency' => $row['Currency'] ?? null,
            ];
        }

        $records = [];
        $cursor = $from;

        while ($cursor->lte($now->startOfMonth())) {
            $key = $cursor->toDateString();
            $value = $indexed[$key] ?? ['amount' => 0, 'currency' => null];

            $records[] = [
                'subscription_id' => $subscription->subscription_id,
                'period_start' => $key,
                'period_end' => $cursor->endOfMonth()->toDateString(),
                'amount' => $value['amount'],
                'currency' => $value['currency'],
                'granularity' => self::GRANULARITY,
                'source' => self::SOURCE,
                'synced_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $cursor = $cursor->addMonth();
        }

        AzureSubscriptionCost::upsert(
            $records,
            ['subscription_id', 'period_start', 'granularity'],
            ['period_end', 'amount', 'currency', 'source', 'synced_at', 'updated_at'],
        );

        $current = collect($records)->last();

        if ($current !== null && strtoupper((string) ($current['currency'] ?? '')) === 'EUR') {
            $subscription->forceFill([
                'mtd_spend_eur' => $current['amount'],
                'last_synced' => now(),
            ])->saveQuietly();
        }

        return [
            'mode' => 'historical',
            'months' => count($records),
            'current_mtd' => $current['amount'] ?? null,
            'currency' => $current['currency'] ?? null,
        ];
    }

    /**
     * Refresh only the current month's MTD value.
     */
    public function syncMonthToDate(AzureSubscription $subscription): array
    {
        $now = CarbonImmutable::now();

        $rows = $this->queryCost(
            $subscription->subscription_id,
            'MonthToDate',
        );

        $row = $rows[0] ?? null;
        $amount = (float) ($row['PreTaxCost'] ?? 0);
        $currency = $row['Currency'] ?? null;

        AzureSubscriptionCost::query()->updateOrCreate(
            [
                'subscription_id' => $subscription->subscription_id,
                'period_start' => $now->startOfMonth()->toDateString(),
                'granularity' => self::GRANULARITY,
            ],
            [
                'period_end' => $now->endOfMonth()->toDateString(),
                'amount' => $amount,
                'currency' => $currency,
                'source' => self::SOURCE,
                'synced_at' => now(),
            ],
        );

        if (strtoupper((string) $currency) === 'EUR') {
            $subscription->forceFill([
                'mtd_spend_eur' => $amount,
                'last_synced' => now(),
            ])->saveQuietly();
        }

        return [
            'mode' => 'mtd',
            'months' => 1,
            'current_mtd' => $amount,
            'currency' => $currency,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function queryCost(
        string $subscriptionId,
        string $timeframe,
        ?CarbonImmutable $from = null,
        ?CarbonImmutable $to = null,
    ): array {
        $dataset = [
            'aggregation' => [
                'totalCost' => [
                    'name' => 'PreTaxCost',
                    'function' => 'Sum',
                ],
            ],
            'granularity' => 'Monthly',
        ];

        $payload = [
            'type' => 'Usage',
            'timeframe' => $timeframe,
            'dataset' => $dataset,
        ];

        if ($timeframe === 'Custom') {
            if ($from === null || $to === null) {
                throw new RuntimeException('Custom Azure cost queries require a start and end date.');
            }

            $payload['timePeriod'] = [
                'from' => $from->startOfDay()->toIso8601String(),
                'to' => $to->endOfDay()->toIso8601String(),
            ];
        }

        $url = sprintf(
            'https://management.azure.com/subscriptions/%s/providers/Microsoft.CostManagement/query',
            $subscriptionId,
        );

        $response = $this->managementRequest()->post($url, $payload);

        if ($response->status() === 204) {
            return [];
        }

        $response->throw();

        $properties = $response->json('properties', []);
        $columns = collect($properties['columns'] ?? [])
            ->pluck('name')
            ->values()
            ->all();

        return collect($properties['rows'] ?? [])
            ->map(function (array $values) use ($columns): array {
                return collect($columns)
                    ->mapWithKeys(fn (string $column, int $index): array => [
                        $column => $values[$index] ?? null,
                    ])
                    ->all();
            })
            ->all();
    }

    private function periodStartFromRow(array $row): ?CarbonImmutable
    {
        $value = $row['UsageDate'] ?? $row['usageDate'] ?? null;

        if ($value === null) {
            return null;
        }

        $value = (string) $value;

        if (preg_match('/^\d{8}$/', $value) === 1) {
            return CarbonImmutable::createFromFormat('Ymd', $value)->startOfMonth();
        }

        try {
            return CarbonImmutable::parse($value)->startOfMonth();
        } catch (\Throwable) {
            return null;
        }
    }

    private function managementRequest(): PendingRequest
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
            throw new RuntimeException(
                'Azure service principal credentials are not configured. Set AZURE_TENANT_ID, AZURE_CLIENT_ID and AZURE_CLIENT_SECRET.'
            );
        }

        $response = Http::asForm()
            ->acceptJson()
            ->timeout((int) config('services.azure.timeout', 30))
            ->post(
                sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/token', $tenantId),
                [
                    'grant_type' => 'client_credentials',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'scope' => 'https://management.azure.com/.default',
                ],
            );

        $response->throw();

        $token = $response->json('access_token');

        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Azure token response did not contain an access token.');
        }

        return $this->accessToken = $token;
    }
}
