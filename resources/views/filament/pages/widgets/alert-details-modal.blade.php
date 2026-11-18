@php
    $detail = $detail ?? null;
    $properties = $detail?->raw_properties ?? [];
    $context = $detail?->alert_context ?? ($properties['context'] ?? ($properties['alertContext'] ?? []));
    $condition = data_get($context, 'condition.allOf.0', []);
    $evaluationStart = data_get($condition, 'windowStartTime') ?? data_get($condition, 'evaluationStartTime') ?? data_get($context, 'windowStartTime');
    $evaluationEnd = data_get($condition, 'windowEndTime') ?? data_get($condition, 'evaluationEndTime') ?? data_get($context, 'windowEndTime');
    $aggregation = data_get($condition, 'timeAggregation') ?? data_get($condition, 'aggregation');
    $rule = data_get($condition, 'metricName') ?? data_get($condition, 'alertRule') ?? data_get($context, 'alertRule');
    $actual = data_get($condition, 'metricValue') ?? data_get($condition, 'value') ?? data_get($condition, 'searchResult');
    $threshold = data_get($condition, 'threshold');
    $deviation = is_numeric($actual) && is_numeric($threshold) ? (float) $actual - (float) $threshold : null;
    $formatTime = fn ($value) => $value ? \Illuminate\Support\Carbon::parse($value)->format('d M Y, h:i A') : '—';
    $formatValue = fn ($value) => is_numeric($value) ? number_format((float) $value, 2) : (is_scalar($value) ? (string) $value : '—');
    $cards = [
        'Alert ID' => $record->azure_alert_id ?? '—',
        'Alert condition' => $record->alert_condition ?? '—',
        'User response' => $record->user_response ?? '—',
        'Fired time' => $formatTime($record->fire_time),
        'Evaluation start' => $formatTime($evaluationStart),
        'Evaluation end' => $formatTime($evaluationEnd),
        'Time aggregation' => $aggregation ?? '—',
        'Alert rule' => $rule ?? '—',
        'Value' => $formatValue($actual),
        'Threshold' => $formatValue($threshold),
        'Deviation' => $deviation !== null ? number_format($deviation, 2) : '—',
    ];
@endphp

<div class="space-y-5">
    <header class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Alert title</p>
                <h2 class="mt-1 break-words text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $record->name ?: 'Unnamed alert' }}</h2>
            </div>
            <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-semibold text-red-700 dark:bg-red-900/40 dark:text-red-300">{{ $record->severity ?? 'Severity unavailable' }}</span>
        </div>
    </header>

    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($cards as $label => $value)
            <div class="min-w-0 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-800">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
                <p class="mt-1 break-words text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $value }}</p>
            </div>
        @endforeach
    </section>

    <section class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
        <h3 class="mb-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Basic alert details</h3>
        <dl class="grid grid-cols-1 gap-x-5 gap-y-3 sm:grid-cols-2">
            @foreach([
                'Monitor service' => $record->monitor_service,
                'Signal type' => $record->signal_type,
                'Subscription' => $record->subscription?->display_name ?? $record->subscription_id,
                'Affected resource' => $record->affected_resource,
                'Resource group' => $record->target_resource_group,
                'Target resource type' => $record->target_resource_type,
                'Suppression status' => $record->suppression_status,
                'Last modified' => $formatTime($record->last_modified_time),
            ] as $label => $value)
                <div class="min-w-0">
                    <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                    <dd class="mt-1 break-words text-sm text-gray-900 dark:text-gray-100">{{ $value ?: '—' }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
</div>
