<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AzureSubscription;
use App\Models\BillingResource;
use App\Models\SubscriptionMetricGraph;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use App\Services\AzureMetricsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;
use Throwable;

class SubscriptionMetricsController extends Controller
{
    public function resources(AzureSubscription $subscription, Request $request, AzureMetricsService $metrics): JsonResponse
    {
        $this->authorizeMetrics($subscription, 'resources.metrics.view');
        $resources = $metrics->eligibleResources($subscription);
        $eligible = [];

        foreach ($resources as $resource) {
            try {
                $definitions = $metrics->definitions($resource);
                if ($definitions !== []) {
                    $eligible[] = [
                        'resource_id' => $resource->resource_id,
                        'name' => $resource->name,
                        'resource_type' => $resource->resource_type,
                        'region' => $resource->region,
                        'sku' => $resource->sku,
                        'metrics' => $definitions,
                    ];
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return response()->json(['data' => $eligible]);
    }

    public function graphs(AzureSubscription $subscription): JsonResponse
    {
        $this->authorizeMetrics($subscription, 'resources.metrics.view');
        return response()->json(['data' => SubscriptionMetricGraph::query()
            ->where('subscription_id', $subscription->subscription_id)
            ->with('resource:resource_id,name,resource_type')
            ->orderBy('position')->orderBy('id')->get()]);
    }

    public function storeGraph(AzureSubscription $subscription, Request $request, AzureMetricsService $metrics): JsonResponse
    {
        $this->authorizeMetrics($subscription, 'resources.metrics.manage');
        $data = $request->validate([
            'resource_id' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:160'],
            'series' => ['required', 'array', 'min:1', 'max:20'],
            'series.*.metric' => ['required', 'string', 'max:200'],
            'series.*.aggregation' => ['required', 'string', 'max:30'],
        ]);

        $resource = BillingResource::query()->where('resource_id', $data['resource_id'])
            ->where('subscription_id', $subscription->subscription_id)->firstOrFail();

        foreach ($data['series'] as $item) {
            $definition = collect($metrics->definitions($resource))->firstWhere('name', $item['metric']);
            if (! $definition || ! in_array($item['aggregation'], $definition['aggregations'], true)) {
                throw ValidationException::withMessages(['series' => 'A selected metric or aggregation is not supported by this resource.']);
            }
        }

        $graph = SubscriptionMetricGraph::query()->create([
            'subscription_id' => $subscription->subscription_id,
            'resource_id' => $resource->resource_id,
            'title' => $data['title'],
            'series' => $data['series'],
            'position' => (int) SubscriptionMetricGraph::query()->where('subscription_id', $subscription->subscription_id)->max('position') + 1,
        ]);

        return response()->json(['data' => $graph->load(['resource:resource_id,name,resource_type'])], 201);
    }

    public function deleteGraph(AzureSubscription $subscription, SubscriptionMetricGraph $graph): JsonResponse
    {
        $this->authorizeMetrics($subscription, 'resources.metrics.manage');
        abort_unless($graph->subscription_id === $subscription->subscription_id, 404);
        $graph->delete();
        return response()->json(['success' => true]);
    }

    public function data(AzureSubscription $subscription, SubscriptionMetricGraph $graph, Request $request, AzureMetricsService $metrics): JsonResponse
    {
        $this->authorizeMetrics($subscription, 'resources.metrics.view');
        abort_unless($graph->subscription_id === $subscription->subscription_id, 404);
        $range = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
            'interval' => ['required', 'regex:/^(?:PT(?:1|5|15|30|60)M|PT1H)$/'],
        ]);
        abort_if(now()->parse($range['end'])->diffInDays(now()->parse($range['start'])) > 93, 422, 'Metric time range cannot exceed 93 days.');

        $resource = BillingResource::query()->where('resource_id', $graph->resource_id)
            ->where('subscription_id', $subscription->subscription_id)->firstOrFail();
        return response()->json(['data' => $metrics->query($resource, $graph->series, $range['start'], $range['end'], $range['interval'])]);
    }

    private function authorizeMetrics(AzureSubscription $subscription, string $permission): void
    {
        $user = auth()->guard('web')->user();
        $developer = auth()->guard('developers')->user();
        if ($developer?->is_active === true) return;
        abort_unless($user instanceof User && $user->is_active
            && app(AccessAuthorizationService::class)->hasPermission($user, $permission, $subscription), 403);
    }
}
