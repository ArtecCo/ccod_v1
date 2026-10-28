<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AzureSubscription;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AzureSubscriptionSyncService
{
    private const API_VERSION = '2022-12-01';
    private ?string $accessToken = null;

    public function sync(): array
    {
        $items = $this->discoverSubscriptions();
        $stats = ['discovered' => count($items), 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'failed' => 0];

        foreach ($items as $item) {
            try {
                $stats[$this->syncOne($item)]++;
            } catch (\Throwable $e) {
                $stats['failed']++;
                report($e);
            }
        }

        return $stats;
    }

    public function discoverSubscriptions(): array
    {
        $url = 'https://management.azure.com/subscriptions?api-version=' . self::API_VERSION;
        $items = [];

        while ($url !== null) {
            $response = $this->request()->get($url)->throw();
            $payload = $response->json();

            if (! is_array($payload) || ! is_array($payload['value'] ?? null)) {
                throw new RuntimeException('Invalid Azure subscription list response.');
            }

            foreach ($payload['value'] as $item) {
                if (is_array($item)) {
                    $items[] = $item;
                }
            }

            $next = $payload['nextLink'] ?? null;
            $url = is_string($next) && $next !== '' ? $next : null;
        }

        return $items;
    }

    private function syncOne(array $data): string
    {
        $id = $data['subscriptionId'] ?? null;
        if (! is_string($id) || $id === '') {
            throw new RuntimeException('Azure response did not contain subscriptionId.');
        }

        $attributes = [
            'display_name' => $this->stringOrNull($data['displayName'] ?? null),
            'state' => $this->stringOrNull($data['state'] ?? null),
            'tenant_id' => $this->stringOrNull($data['tenantId'] ?? null),
            'authorization_source' => $this->stringOrNull($data['authorizationSource'] ?? null),
            'last_synced' => now(),
        ];

        $record = AzureSubscription::query()->where('subscription_id', $id)->first();

        if ($record === null) {
            AzureSubscription::query()->create(['subscription_id' => $id, ...$attributes]);
            return 'created';
        }

        $changed = false;
        foreach (['display_name', 'state', 'tenant_id', 'authorization_source'] as $key) {
            if ($record->getAttribute($key) !== $attributes[$key]) {
                $changed = true;
                break;
            }
        }

        $record->forceFill($attributes)->saveQuietly();
        return $changed ? 'updated' : 'unchanged';
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    private function request(): PendingRequest
    {
        return Http::acceptJson()->withToken($this->token())->timeout((int) config('services.azure.timeout', 30));
    }

    private function token(): string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $tenant = config('services.azure.tenant_id');
        $client = config('services.azure.client_id');
        $secret = config('services.azure.client_secret');

        if (! $tenant || ! $client || ! $secret) {
            throw new RuntimeException('Azure service principal credentials are not configured.');
        }

        $response = Http::asForm()->acceptJson()->timeout((int) config('services.azure.timeout', 30))->post(
            "https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token",
            [
                'grant_type' => 'client_credentials',
                'client_id' => $client,
                'client_secret' => $secret,
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
