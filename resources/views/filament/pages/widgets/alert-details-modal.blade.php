@php
    $properties = $detail?->raw_properties ?? [];
    $context = $properties['context'] ?? $properties['alertContext'] ?? [];
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
    $mainFields = [
        ['heroicon-o-identification','Alert ID',$record->azure_alert_id ?? '—'],
        ['heroicon-o-bolt','Alert condition',$record->alert_condition ?? '—'],
        ['heroicon-o-user','User response',$record->user_response ?? '—'],
        ['heroicon-o-clock','Fired time',$formatTime($record->fire_time)],
        ['heroicon-o-clock','Evaluation start',$formatTime($evaluationStart)],
        ['heroicon-o-clock','Evaluation end',$formatTime($evaluationEnd)],
        ['heroicon-o-chart-bar','Time aggregation',$aggregation ?? '—'],
        ['heroicon-o-adjustments-horizontal','Alert rule',$rule ?? '—'],
        ['heroicon-o-chart-line','Value',$formatValue($actual)],
        ['heroicon-o-scale','Threshold',$formatValue($threshold)],
        ['heroicon-o-arrows-up-down','Deviation',$deviation !== null ? number_format($deviation, 2) : '—'],
    ];
    $basicFields = [
        ['heroicon-o-cog-6-tooth','Monitor service',$record->monitor_service],
        ['heroicon-o-signal','Signal type',$record->signal_type],
        ['heroicon-o-cloud','Subscription',$record->subscription?->display_name ?? $record->subscription_id],
        ['heroicon-o-computer-desktop','Affected resource',$record->affected_resource],
        ['heroicon-o-folder','Resource group',$record->target_resource_group],
        ['heroicon-o-cube','Target resource type',$record->target_resource_type],
        ['heroicon-o-bell-slash','Suppression status',$record->suppression_status],
        ['heroicon-o-pencil-square','Last modified',$formatTime($record->last_modified_time)],
    ];
@endphp

<div class="space-y-6">
    <x-filament::section>
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="space-y-1">
                <x-filament::badge color="gray">Alert</x-filament::badge>
                <h2 class="text-xl font-semibold tracking-tight">{{ $record->name ?: 'Unnamed alert' }}</h2>
            </div>
            <x-filament::badge :color="match ($record->severity) { 'Sev0', 'Sev1' => 'danger', 'Sev2', 'Sev3' => 'warning', default => 'gray' }">{{ $record->severity ?? 'Severity unavailable' }}</x-filament::badge>
        </div>
    </x-filament::section>

    <x-filament::section heading="Alert evaluation" icon="heroicon-o-magnifying-glass">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($mainFields as [$icon,$label,$value])
                <div class="flex items-start gap-3">
                    <x-filament::icon :icon="$icon" class="mt-0.5 h-5 w-5 text-gray-500 dark:text-gray-400" />
                    <div class="min-w-0 space-y-1">
                        <div class="text-sm font-semibold">{{ $label }}</div>
                        <div class="text-sm break-words text-gray-700 dark:text-gray-300">{{ $value ?: '—' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    <x-filament::section heading="Basic alert details" icon="heroicon-o-information-circle">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($basicFields as [$icon,$label,$value])
                <div class="flex items-start gap-3">
                    <x-filament::icon :icon="$icon" class="mt-0.5 h-5 w-5 text-gray-500 dark:text-gray-400" />
                    <div class="min-w-0 space-y-1">
                        <div class="text-sm font-semibold">{{ $label }}</div>
                        <div class="text-sm break-words text-gray-700 dark:text-gray-300">{{ $value ?: '—' }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</div>
