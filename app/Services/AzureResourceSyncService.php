<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AzureSubscription;
use App\Models\BillingResource;
use Illuminate\Http\Client\Response;
use RuntimeException;

class AzureResourceSyncService
{
    private const API_VERSION = '2024-04-01';
    private const PAGE_SIZE = 1000;

    public function __construct(
        private readonly AzureSubscriptionSyncService $azure,
    ) {
    }

    public function sync(): array
    {
        $subscriptions = AzureSubscription::query()
            ->whereNotNull('subscription_id')
            ->pluck('subscription_id')
            ->filter(fn (mixed $id): bool => is_string($id) && $id !== '')
            ->values()
            ->all();

        if ($subscriptions === []) {
            return [
                'subscriptions' => 0,
                'discovered' => 0,
                'created' => 0,
                'updated' => 0,
                'unchanged' => 0,
            ];
        }

        $stats = [
            'subscriptions' => count($subscriptions),
            'discovered' => 0,
            'created' => 0,
            'updated' => 0,
            'unchanged' => 0,
        ];

        foreach (array_chunk($subscriptions, 100) as $subscriptionBatch) {
            $skipToken = null;

            do {
                $payload = [
                    'subscriptions' => $subscriptionBatch,
                    'query' => <<<'KQL'
Resources
| project id, name, type, location, subscriptionId, sku
| order by id asc
KQL,
                    'options' => [
                        '$top' => self::PAGE_SIZE,
                        'resultFormat' => 'objectArray',
                    ],
                ];

                if ($skipToken !== null) {
                    $payload['options']['$skipToken'] = $skipToken;
                }

                $response = $this->azure->post(
                    'https://management.azure.com/providers/Microsoft.ResourceGraph/resources?api-version=' . self::API_VERSION,
                    $payload,
                );

                $response->throw();
                $page = $response->json();

                if (! is_array($page) || ! is_array($page['data'] ?? null)) {
                    throw new RuntimeException('Invalid Azure Resource Graph response.');
                }

                foreach ($page['data'] as $resource) {
                    if (! is_array($resource)) {
                        continue;
                    }

                    $stats['discovered']++;
                    $result = $this->syncOne($resource, $subscriptionBatch);
                    $stats[$result]++;
                }

                $next = $page['$skipToken'] ?? null;
                $skipToken = is_string($next) && $next !== '' ? $next : null;
            } while ($skipToken !== null);
        }

        return $stats;
    }

    private function syncOne(array $resource, array $subscriptionBatch): string
    {
        $resourceId = $resource['id'] ?? null;
        $subscriptionId = $resource['subscriptionId'] ?? $this->subscriptionIdFromResourceId($resourceId);

        if (! is_string($resourceId) || $resourceId === '') {
            throw new RuntimeException('Azure Resource Graph returned a resource without an id.');
        }

        if (! is_string($subscriptionId) || $subscriptionId === '' || ! in_array($subscriptionId, $subscriptionBatch, true)) {
            throw new RuntimeException("Azure resource {$resourceId} does not map to a synchronized subscription.");
        }

        $attributes = [
            'resource_id' => $resourceId,
            'subscription_id' => $subscriptionId,
            'name' => $this->stringOrNull($resource['name'] ?? null),
            'resource_type' => $this->stringOrNull($resource['type'] ?? null),
            'region' => $this->stringOrNull($resource['location'] ?? null),
            'sku' => $this->skuString($resource['sku'] ?? null),
        ];

        $existing = BillingResource::query()->find($resourceId);

        if ($existing === null) {
            BillingResource::query()->create($attributes);
            return 'created';
        }

        $changed = false;
        foreach (['subscription_id', 'name', 'resource_type', 'region', 'sku'] as $key) {
            if ($existing->getAttribute($key) !== $attributes[$key]) {
                $changed = true;
                break;
            }
        }

        if ($changed) {
            $existing->forceFill($attributes)->saveQuietly();
            return 'updated';
        }

        return 'unchanged';
    }

    private function subscriptionIdFromResourceId(mixed $resourceId): ?string
    {
        if (! is_string($resourceId)) {
            return null;
        }

        if (preg_match('#/subscriptions/([^/]+)#i', $resourceId, $matches) !== 1) {
            return null;
        }

        return $matches[1] ?? null;
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    private function skuString(mixed $value): ?string
    {
        if (is_string($value)) {
            return $value !== '' ? $value : null;
        }

        if (is_array($value)) {
            $encoded = json_encode($value, JSON_UNESCAPED_SLASHES);
            return is_string($encoded) ? $encoded : null;
        }

        return null;
    }
}
