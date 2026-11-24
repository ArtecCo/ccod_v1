<?php

namespace App\Services;

use App\Models\AzureBudget;
use App\Models\AzureSubscription;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class AzureBudgetSyncService
{
    private const API_VERSION = '2023-05-01';
    private ?string $accessToken = null;

    public function __construct(private readonly AzureCostManagementThrottle $throttle) {}

    public function sync(AzureSubscription $subscription): int
    {
        $url = sprintf(
            'https://management.azure.com/subscriptions/%s/providers/Microsoft.Consumption/budgets',
            $subscription->subscription_id,
        );

        $response = $this->throttle->get($this->request()->withQueryParameters(['api-version' => self::API_VERSION]), $url)->throw();
        $items = $response->json('value', []);

        if (! is_array($items)) {
            throw new RuntimeException('Invalid Azure budget response.');
        }

        $seen = [];

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $properties = is_array($item['properties'] ?? null) ? $item['properties'] : [];
            $name = is_string($item['name'] ?? null) ? $item['name'] : null;
            if ($name === null || $name === '') {
                continue;
            }

            $timePeriod = is_array($properties['timePeriod'] ?? null) ? $properties['timePeriod'] : [];
            $currentSpend = is_array($properties['currentSpend'] ?? null) ? $properties['currentSpend'] : [];
            $forecastSpend = is_array($properties['forecastSpend'] ?? null) ? $properties['forecastSpend'] : [];

            $attributes = [
                'amount' => is_numeric($properties['amount'] ?? null) ? (float) $properties['amount'] : 0,
                'currency' => $this->stringOrNull($currentSpend['unit'] ?? $properties['unit'] ?? null),
                'current_spend' => is_numeric($currentSpend['amount'] ?? null) ? (float) $currentSpend['amount'] : 0,
                'forecast_spend' => is_numeric($forecastSpend['amount'] ?? null) ? (float) $forecastSpend['amount'] : null,
                'time_grain' => $this->stringOrNull($properties['timeGrain'] ?? null),
                // Microsoft.Consumption uses startDate/endDate in BudgetTimePeriod.
                // Keep the fallback keys for compatibility with any older response shape.
                'start_date' => $this->dateOrNull($timePeriod['startDate'] ?? $timePeriod['start'] ?? null),
                'end_date' => $this->dateOrNull($timePeriod['endDate'] ?? $timePeriod['end'] ?? null),
                'fetched_at' => now(),
            ];

            $budget = AzureBudget::query()->firstOrNew([
                'subscription_id' => $subscription->subscription_id,
                'budget_name' => $name,
            ]);
            $previousContext = $budget->exists ? $this->thresholdContext($budget, $subscription) : null;

            $budget->fill($attributes);
            $budget->save();

            $currentContext = $this->thresholdContext($budget, $subscription);
            $rules = app(NotificationRuleService::class);

            if (
                $rules->configuredRuleMatches('azure.budget.threshold_reached', $currentContext)
                && ($previousContext === null || ! $rules->configuredRuleMatches('azure.budget.threshold_reached', $previousContext))
            ) {
                $rules->trigger('azure.budget.threshold_reached', $currentContext);
            }

            $seen[] = $name;
        }

        if ($seen !== []) {
            AzureBudget::query()
                ->where('subscription_id', $subscription->subscription_id)
                ->whereNotIn('budget_name', $seen)
                ->delete();
        }

        return count($seen);
    }

    private function thresholdContext(AzureBudget $budget, AzureSubscription $subscription): array
    {
        $amount = (float) $budget->amount;
        $spend = (float) $budget->current_spend;

        return [
            'budget_id' => (int) $budget->getKey(),
            'budget_name' => (string) $budget->budget_name,
            'subscription_id' => (string) $budget->subscription_id,
            'subscription_name' => (string) $subscription->display_name,
            'actual_cost' => $spend,
            'budget_amount' => $amount,
            'threshold_percent' => $amount > 0 ? round(($spend / $amount) * 100, 2) : 0,
            'currency' => (string) ($budget->currency ?? ''),
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    private function dateOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? substr($value, 0, 10) : null;
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
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
