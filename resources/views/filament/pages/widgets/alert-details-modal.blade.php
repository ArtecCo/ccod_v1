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
        ['🆔','Alert ID',$record->azure_alert_id ?? '—'],
        ['⚡','Alert condition',$record->alert_condition ?? '—'],
        ['👤','User response',$record->user_response ?? '—'],
        ['🕒','Fired time',$formatTime($record->fire_time)],
        ['⏱️','Evaluation start',$formatTime($evaluationStart)],
        ['⏱️','Evaluation end',$formatTime($evaluationEnd)],
        ['📊','Time aggregation',$aggregation ?? '—'],
        ['📏','Alert rule',$rule ?? '—'],
        ['📈','Value',$formatValue($actual)],
        ['🎯','Threshold',$formatValue($threshold)],
        ['↕️','Deviation',$deviation !== null ? number_format($deviation, 2) : '—'],
    ];
    $basicFields = [
        ['🛠️','Monitor service',$record->monitor_service],
        ['📡','Signal type',$record->signal_type],
        ['☁️','Subscription',$record->subscription?->display_name ?? $record->subscription_id],
        ['💻','Affected resource',$record->affected_resource],
        ['📁','Resource group',$record->target_resource_group],
        ['🧩','Target resource type',$record->target_resource_type],
        ['🔕','Suppression status',$record->suppression_status],
        ['📝','Last modified',$formatTime($record->last_modified_time)],
    ];
@endphp

<div style="font-family:inherit; color:#111827; display:flex; flex-direction:column; gap:18px;">
    <div style="border:1px solid #d1d5db; border-radius:12px; padding:16px; background:#f8fafc;">
        <div style="font-size:12px; font-weight:700; letter-spacing:.08em; color:#64748b; text-transform:uppercase; margin-bottom:6px;">Alert</div>
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap;">
            <div style="font-size:19px; line-height:1.4; font-weight:700; overflow-wrap:anywhere;">{{ $record->name ?: 'Unnamed alert' }}</div>
            <span style="background:#fee2e2; color:#991b1b; border-radius:999px; padding:5px 11px; font-size:12px; font-weight:700; white-space:nowrap;">{{ $record->severity ?? 'Severity unavailable' }}</span>
        </div>
    </div>

    <section>
        <h3 style="font-size:15px; font-weight:700; margin:0 0 10px;">🔎 Alert evaluation</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:10px;">
            @foreach($mainFields as [$icon,$label,$value])
                <div style="border:1px solid #e5e7eb; border-radius:9px; padding:12px; background:#ffffff; min-width:0;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600; margin-bottom:6px;">{{ $icon }} {{ $label }}</div>
                    <div style="font-size:14px; color:#111827; font-weight:650; overflow-wrap:anywhere;">{{ $value ?: '—' }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section>
        <h3 style="font-size:15px; font-weight:700; margin:0 0 10px;">📋 Basic alert details</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:10px;">
            @foreach($basicFields as [$icon,$label,$value])
                <div style="border:1px solid #e5e7eb; border-radius:9px; padding:12px; background:#f9fafb; min-width:0;">
                    <div style="font-size:12px; color:#6b7280; font-weight:600; margin-bottom:6px;">{{ $icon }} {{ $label }}</div>
                    <div style="font-size:14px; color:#111827; font-weight:600; overflow-wrap:anywhere;">{{ $value ?: '—' }}</div>
                </div>
            @endforeach
        </div>
    </section>
</div>
