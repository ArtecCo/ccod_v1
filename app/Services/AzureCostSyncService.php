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

    public function syncSubscription(AzureSubscription $subscription, bool $forceHistorical = false): array
    {
        $hasHistoricalData = AzureSubscriptionCost::query()->where('subscription_id', $subscription->subscription_id)->where('granularity', self::GRANULARITY)->exists();
        return $forceHistorical || ! $hasHistoricalData ? $this->syncHistorical($subscription) : $this->syncMonthToDate($subscription);
    }

    public function syncHistorical(AzureSubscription $subscription): array
    {
        $now = CarbonImmutable::now();
        $from = $now->subMonths(11)->startOfMonth();
        $rows = $this->queryCost($subscription->subscription_id, 'Custom', $from, $now->endOfMonth());
        $indexed = [];
        $unmappedRows = 0;

        foreach ($rows as $row) {
            $periodStart = $this->periodStartFromRow($row);
            $amount = $this->numericCostFromRow($row);
            if ($periodStart === null || $amount === null) {
                $unmappedRows++;
                continue;
            }

            $key = $periodStart->toDateString();
            $currency = $this->stringOrNull($row['Currency'] ?? null);
            $indexed[$key] ??= ['amount' => 0.0, 'currency' => $currency];
            $indexed[$key]['amount'] += $amount;
            $indexed[$key]['currency'] ??= $currency;
        }

        if ($rows !== [] && $indexed === []) {
            throw new RuntimeException('Azure returned cost rows, but no usable billing date and PreTaxCost values could be parsed.');
        }

        $records = [];
        $cursor = $from;
        while ($cursor->lte($now->startOfMonth())) {
            $key = $cursor->toDateString();
            $value = $indexed[$key] ?? ['amount' => 0.0, 'currency' => null];
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

        AzureSubscriptionCost::upsert($records, ['subscription_id', 'period_start', 'granularity'], ['period_end', 'amount', 'currency', 'source', 'synced_at', 'updated_at']);
        $current = collect($records)->last();
        $subscription->forceFill([
            'mtd_spend_eur' => $current !== null && strtoupper((string) ($current['currency'] ?? '')) === 'EUR' ? $current['amount'] : null,
            'last_synced' => now(),
        ])->saveQuietly();

        return ['mode' => 'historical', 'months' => count($records), 'current_mtd' => $current['amount'] ?? null, 'currency' => $current['currency'] ?? null, 'source_rows' => count($rows), 'unmapped_rows' => $unmappedRows];
    }

    public function syncMonthToDate(AzureSubscription $subscription): array
    {
        $now = CarbonImmutable::now();
        $rows = $this->queryCost($subscription->subscription_id, 'MonthToDate');
        $amount = 0.0;
        $currency = null;
        foreach ($rows as $row) {
            if (($value = $this->numericCostFromRow($row)) !== null) {
                $amount += $value;
            }
            $currency ??= $this->stringOrNull($row['Currency'] ?? null);
        }

        AzureSubscriptionCost::query()->updateOrCreate(
            ['subscription_id' => $subscription->subscription_id, 'period_start' => $now->startOfMonth()->toDateString(), 'granularity' => self::GRANULARITY],
            ['period_end' => $now->endOfMonth()->toDateString(), 'amount' => $amount, 'currency' => $currency, 'source' => self::SOURCE, 'synced_at' => now()],
        );

        $subscription->forceFill(['mtd_spend_eur' => strtoupper((string) $currency) === 'EUR' ? $amount : null, 'last_synced' => now()])->saveQuietly();
        return ['mode' => 'mtd', 'months' => 1, 'current_mtd' => $amount, 'currency' => $currency, 'source_rows' => count($rows)];
    }

    /** @return array<int, array<string, mixed>> */
    public function queryCost(string $subscriptionId, string $timeframe, ?CarbonImmutable $from = null, ?CarbonImmutable $to = null): array
    {
        $payload = [
            'type' => 'Usage',
            'timeframe' => $timeframe,
            'dataset' => [
                'aggregation' => ['totalCost' => ['name' => 'PreTaxCost', 'function' => 'Sum']],
                'granularity' => 'Monthly',
            ],
        ];

        if ($timeframe === 'Custom') {
            if ($from === null || $to === null) {
                throw new RuntimeException('Custom Azure cost queries require a start and end date.');
            }
            $payload['timePeriod'] = ['from' => $from->startOfDay()->toIso8601String(), 'to' => $to->endOfDay()->toIso8601String()];
        }

        $url = sprintf('https://management.azure.com/subscriptions/%s/providers/Microsoft.CostManagement/query', $subscriptionId);
        $response = $this->managementRequest()->post($url, $payload);
        if ($response->status() === 204) {
            return [];
        }
        $response->throw();

        $properties = $response->json('properties', []);
        $columns = collect($properties['columns'] ?? [])->map(fn (mixed $column): ?string => is_array($column) && is_string($column['name'] ?? null) ? $column['name'] : null)->filter()->values()->all();
        return collect($properties['rows'] ?? [])->filter('is_array')->map(function (array $values) use ($columns): array {
            return collect($columns)->mapWithKeys(fn (string $column, int $index): array => [$column => $values[$index] ?? null])->all();
        })->all();
    }

    private function numericCostFromRow(array $row): ?float
    {
        foreach (['PreTaxCost', 'preTaxCost', 'totalCost'] as $key) {
            if (isset($row[$key]) && is_numeric($row[$key])) {
                return (float) $row[$key];
            }
        }
        return null;
    }

    private function periodStartFromRow(array $row): ?CarbonImmutable
    {
        $value = $row['BillingMonth'] ?? $row['billingMonth'] ?? $row['UsageDate'] ?? $row['usageDate'] ?? $row['UsageMonth'] ?? $row['usageMonth'] ?? null;
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        foreach ([['Ym', '/^\d{6}$/'], ['Ymd', '/^\d{8}$/']] as [$format, $pattern]) {
            if (preg_match($pattern, $value) === 1) {
                try {
                    return CarbonImmutable::createFromFormat($format, $value)->startOfMonth();
                } catch (\Throwable) {
                    return null;
                }
            }
        }

        try {
            return CarbonImmutable::parse($value)->startOfMonth();
        } catch (\Throwable) {
            return null;
        }
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    private function managementRequest(): PendingRequest
    {
        return Http::acceptJson()->withToken($this->accessToken())->withQueryParameters(['api-version' => self::API_VERSION])->timeout((int) config('services.azure.timeout', 30));
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
            throw new RuntimeException('Azure service principal credentials are not configured. Set AZURE_TENANT_ID, AZURE_CLIENT_ID and AZURE_CLIENT_SECRET.');
        }
        $response = Http::asForm()->acceptJson()->timeout((int) config('services.azure.timeout', 30))->post(sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/token', $tenantId), ['grant_type' => 'client_credentials', 'client_id' => $clientId, 'client_secret' => $clientSecret, 'scope' => 'https://management.azure.com/.default']);
        $response->throw();
        $token = $response->json('access_token');
        if (! is_string($token) || $token === '') {
            throw new RuntimeException('Azure token response did not contain an access token.');
        }
        return $this->accessToken = $token;
    }
}
