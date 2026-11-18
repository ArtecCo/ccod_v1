@php
    $detail = $detail ?? null;
    $properties = $detail?->raw_properties ?? [];
    $essentials = $detail?->essentials ?? ($properties['essentials'] ?? []);
    $context = $detail?->alert_context ?? ($properties['context'] ?? ($properties['alertContext'] ?? []));
    $targetIds = $essentials['alertTargetIDs'] ?? ($essentials['targetResource'] ?? null);
    $queryText = data_get($context, 'condition.allOf.0.searchQuery')
        ?? data_get($context, 'condition.allOf.0.query')
        ?? data_get($context, 'searchQuery')
        ?? data_get($context, 'query');
@endphp

<div class="space-y-5">
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach([
            'Severity' => $record->severity,
            'Fired time' => $record->fire_time?->format('d M Y, h:i A') ?? '—',
            'Status / condition' => $record->alert_condition ?? '—',
            'Alert state / response' => $record->user_response ?? '—',
            'Monitor service' => $record->monitor_service ?? '—',
            'Signal type' => $record->signal_type ?? '—',
            'Subscription' => $record->subscription?->display_name ?? $record->subscription_id,
            'Resource group' => $record->target_resource_group ?? '—',
            'Target resource type' => $record->target_resource_type ?? '—',
            'Affected resource' => $record->affected_resource ?? '—',
            'Suppression status' => $record->suppression_status ?? '—',
            'Last modified' => $record->last_modified_time?->format('d M Y, h:i A') ?? '—',
        ] as $label => $value)
            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</div>
                <div class="mt-1 break-words text-sm font-medium text-gray-900 dark:text-gray-100">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    @if(is_array($essentials) && count($essentials))
        <section class="space-y-2">
            <h3 class="text-sm font-semibold">Alert essentials</h3>
            <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-700">
                    @foreach($essentials as $key => $value)
                        <tr>
                            <th class="w-1/3 px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-300">{{ str($key)->headline() }}</th>
                            <td class="px-3 py-2 break-words">{{ is_array($value) ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : ($value ?? '—') }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </section>
    @endif

    @if($targetIds)
        <section class="space-y-2">
            <h3 class="text-sm font-semibold">Target resource IDs</h3>
            <pre class="max-h-48 overflow-auto rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs dark:border-gray-700 dark:bg-gray-900">{{ is_array($targetIds) ? json_encode($targetIds, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : $targetIds }}</pre>
        </section>
    @endif

    @if($queryText)
        <section class="space-y-2">
            <h3 class="text-sm font-semibold">Query</h3>
            <pre class="max-h-72 overflow-auto rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs dark:border-gray-700 dark:bg-gray-900">{{ $queryText }}</pre>
        </section>
    @endif

    @if(is_array($context) && count($context))
        <section class="space-y-2">
            <h3 class="text-sm font-semibold">Alert context</h3>
            <pre class="max-h-80 overflow-auto rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs dark:border-gray-700 dark:bg-gray-900">{{ json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        </section>
    @endif

    @if(is_array($properties) && count($properties))
        <details class="rounded-xl border border-gray-200 dark:border-gray-700">
            <summary class="cursor-pointer px-3 py-2 text-sm font-semibold">Raw Azure alert payload</summary>
            <pre class="max-h-96 overflow-auto border-t border-gray-200 p-3 text-xs dark:border-gray-700">{{ json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
        </details>
    @else
        <div class="rounded-lg border border-dashed border-gray-300 p-3 text-sm text-gray-500 dark:border-gray-700">
            Detailed Azure payload has not been collected yet. Run the alert synchronization to populate it.
        </div>
    @endif
</div>
