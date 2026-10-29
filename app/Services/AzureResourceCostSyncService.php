<?php

namespace App\Services;

use App\Models\AzureSubscription;
use App\Models\BillingResource;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AzureResourceCostSyncService
{
    private const API_VERSION = '2025-03-01';
    private ?string $accessToken = null;

    public function __construct(private readonly AzureCostManagementThrottle $throttle) {}

    public function sync(AzureSubscription $subscription): int
    {
        $url = sprintf('https://management.azure.com/subscriptions/%s/providers/Microsoft.CostManagement/query', $subscription->subscription_id);
        $payload = [
            'type' => 'Usage',
            'timeframe' => 'MonthToDate',
            'dataset' => [
                'granularity' => 'Monthly',
                'aggregation' => ['totalCost' => ['name' => 'PreTaxCost', 'function' => 'Sum']],
                'grouping' => [['type' => 'Dimension', 'name' => 'ResourceId']],
            ],
        ];

        $response = $this->throttle->post($this->request(), $url, $payload);
        if ($response->status() === 204) {
            BillingResource::query()->where('subscription_id', $subscription->subscription_id)->update(['cost_eur' => 0]);
            return 0;
        }
        $response->throw();

        $properties = $response->json('properties', []);
        $columns = collect($properties['columns'] ?? [])->map(fn (mixed $column): ?string => is_array($column) && is_string($column['name'] ?? null) ? $column['name'] : null)->filter()->values()->all();
        $costs = [];
        foreach (($properties['rows'] ?? []) as $values) {
            if (! is_array($values)) continue;
            $row = [];
            foreach ($columns as $index => $column) $row[$column] = $values[$index] ?? null;
            $resourceId = $row['ResourceId'] ?? $row['resourceId'] ?? $row['ResourceID'] ?? null;
            $amount = $row['PreTaxCost'] ?? $row['preTaxCost'] ?? $row['totalCost'] ?? null;
            if (is_string($resourceId) && $resourceId !== '' && is_numeric($amount)) $costs[strtolower($resourceId)] = ($costs[strtolower($resourceId)] ?? 0) + (float) $amount;
        }

        $resources = BillingResource::query()->where('subscription_id', $subscription->subscription_id)->get(['resource_id']);
        $updated = 0;
        foreach ($resources as $resource) {
            $amount = $costs[strtolower((string) $resource->resource_id)] ?? 0;
            $resource->forceFill(['cost_eur' => $amount])->saveQuietly();
            $updated++;
        }
        return $updated;
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()->withToken($this->accessToken())->withQueryParameters(['api-version' => self::API_VERSION])->timeout((int) config('services.azure.timeout', 30));
    }

    private function accessToken(): string
    {
        if ($this->accessToken !== null) return $this->accessToken;
        $tenantId = config('services.azure.tenant_id');
        $clientId = config('services.azure.client_id');
        $clientSecret = config('services.azure.client_secret');
        if (! $tenantId || ! $clientId || ! $clientSecret) throw new RuntimeException('Azure service principal credentials are not configured.');
        $response = Http::asForm()->acceptJson()->timeout((int) config('services.azure.timeout', 30))->post(sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/token', $tenantId), ['grant_type' => 'client_credentials', 'client_id' => $clientId, 'client_secret' => $clientSecret, 'scope' => 'https://management.azure.com/.default'])->throw();
        $token = $response->json('access_token');
        if (! is_string($token) || $token === '') throw new RuntimeException('Azure token response did not contain an access token.');
        return $this->accessToken = $token;
    }
}
