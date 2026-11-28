<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
@page { margin: 25mm 18mm 22mm 18mm; }
body { font-family: DejaVu Sans, sans-serif; color:#20252b; font-size:9pt; line-height:1.55; }
.cover { height:235mm; text-align:center; }
.cover-inner { padding-top:75mm; }
.logo { max-width:150px; max-height:48px; margin-bottom:24px; }
.brand { font-size:10pt; font-weight:bold; letter-spacing:2px; color:#526171; text-transform:uppercase; }
h1 { font-size:25pt; font-weight:normal; margin:14px 0 10px; color:#17212b; }
.cover-meta { color:#687582; font-size:10pt; line-height:2; }
.cover-rule { width:70px; border-top:2px solid {{ $primaryColor }}; margin:24px auto; }
.page-break { page-break-after:always; }
.header { position:fixed; top:-17mm; left:0; right:0; height:10mm; border-bottom:1px solid #d8dde2; font-size:8pt; color:#66727e; }
.header-logo { max-width:90px; max-height:22px; vertical-align:middle; }
.page-number:before { content: counter(page) " / " counter(pages); }
.footer { position:fixed; bottom:-14mm; left:0; right:0; border-top:1px solid #d8dde2; padding-top:5px; color:#77818a; font-size:7pt; }
.footer .right { float:right; }
h2 { font-size:15pt; font-weight:normal; margin:0 0 10px; color:#1e2933; border-left:3px solid {{ $primaryColor }}; padding-left:9px; }
h3 { font-size:10pt; margin:17px 0 7px; color:#394653; border-bottom:1px solid #e3e7ea; padding-bottom:5px; }
p { margin:4px 0 9px; }
table { width:100%; border-collapse:collapse; margin:8px 0 15px; }
thead { display:table-header-group; }
tr { page-break-inside:avoid; }
th { text-align:left; background:{{ $primaryColor }}; color:#ffffff; font-size:7.5pt; font-weight:bold; padding:7px 6px; border-bottom:1px solid {{ $primaryColor }}; }
td { padding:6px; border-bottom:1px solid #e3e7ea; vertical-align:top; font-size:8pt; }
.muted { color:#687582; }
.empty { padding:14px; border:1px solid #e0e4e8; color:#687582; }
.small { font-size:7.5pt; }
.summary-grid { width:100%; display:table; table-layout:fixed; border-spacing:8px 0; margin:10px -8px 18px; }
.summary-card { display:table-cell; width:33.33%; padding:12px; border:1px solid #e0e4e8; border-top:3px solid {{ $primaryColor }}; background:#f8fafc; vertical-align:top; }
.summary-label { color:#64748b; font-size:7.5pt; margin-bottom:7px; }
.summary-value { color:#17212b; font-size:14pt; font-weight:bold; }
.chart-panel { border:1px solid #e2e8f0; padding:10px 12px; margin:8px 0 12px; }
.distribution { display:table; width:100%; table-layout:fixed; }
.pie-chart { display:table-cell; width:34%; vertical-align:middle; text-align:center; }
.distribution-table { display:table-cell; width:66%; vertical-align:middle; }
.distribution-table table { margin-top:0; }
</style>
</head>
<body>
<div class="header">@if($logoPath)<img class="header-logo" src="{{ $logoPath }}" alt="{{ $brandName }}">@endif <span style="float:right">{{ $brandName }}</span></div>
<div class="footer">System generated report <span class="right">Page <span class="page-number"></span></span></div>
<div class="cover">
    <div class="cover-inner">
        @if($logoPath)<img class="logo" src="{{ $logoPath }}" alt="{{ $brandName }}">@endif
        <div class="brand">{{ $brandName }}</div>
        <div class="cover-rule"></div>
        <h1>{{ $label }}</h1>
        <div class="cover-meta">
            {{ $report->scope_name }}<br>
            {{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }}<br>
            Generated {{ $generatedAt->format('d M Y H:i') }}
        </div>
    </div>
</div>
<div class="page-break"></div>
<h2>{{ $label }}</h2>
<p class="muted">{{ $report->scope_name }} · {{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }}</p>

@if($type === 'overview')
    <h3>Scope summary</h3>
    <table><tbody>
        @foreach($payload['summary'] as $key => $value)
            <tr><th>{{ ucfirst($key) }}</th><td>{{ number_format($value) }}</td></tr>
        @endforeach
    </tbody></table>
    <h3>Subscriptions in scope</h3>
    @if($payload['subscriptions']->isEmpty())<div class="empty">No subscription records are available for this scope.</div>
    @else
    <table><thead><tr><th>Subscription</th><th>Subscription ID</th><th>State</th><th>Health</th><th>Security score</th></tr></thead><tbody>
    @foreach($payload['subscriptions'] as $row)
        <tr><td>{{ $row->display_name }}</td><td class="small">{{ $row->subscription_id }}</td><td>{{ $row->state ?: '—' }}</td><td>{{ $row->health_status ?: '—' }}</td><td>{{ $row->security_score ?? '—' }}</td></tr>
    @endforeach
    </tbody></table>
    @endif
@elseif($type === 'costs')
    <h3>Recorded costs</h3>
    @if($payload['costs']->isEmpty())<div class="empty">No cost records are available for the selected period.</div>
    @else
    <table><thead><tr><th>Subscription</th><th>Period</th><th>Amount</th><th>Currency</th><th>Granularity</th></tr></thead><tbody>
    @foreach($payload['costs'] as $row)
        <tr><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ $row->period_start?->format('d M Y') }} – {{ $row->period_end?->format('d M Y') }}</td><td>{{ number_format((float)$row->amount, 2) }}</td><td>{{ $row->currency ?: '—' }}</td><td>{{ $row->granularity }}</td></tr>
    @endforeach
    </tbody></table>
    @endif
    <h3>Budgets</h3>
    @if($payload['budgets']->isEmpty())<div class="empty">No budget records are available.</div>
    @else
    <table><thead><tr><th>Subscription</th><th>Budget</th><th>Amount</th><th>Current spend</th><th>Forecast spend</th><th>Currency</th></tr></thead><tbody>
    @foreach($payload['budgets'] as $row)
        <tr><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ $row->budget_name }}</td><td>{{ $row->amount }}</td><td>{{ $row->current_spend }}</td><td>{{ $row->forecast_spend }}</td><td>{{ $row->currency ?: '—' }}</td></tr>
    @endforeach
    </tbody></table>
    @endif
    <h3>Forecast snapshots</h3>
    @if($payload['forecasts']->isEmpty() && $payload['current_forecasts']->isEmpty())<div class="empty">No forecast records are available.</div>
    @else
    <table><thead><tr><th>Subscription</th><th>Forecast period</th><th>Forecast amount</th><th>Currency</th><th>Captured</th></tr></thead><tbody>
    @foreach($payload['forecasts'] as $row)
        <tr><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ $row->forecast_period?->format('M Y') }}</td><td>{{ $row->forecast_amount }}</td><td>{{ $row->currency ?: '—' }}</td><td>{{ $row->captured_at?->format('d M Y H:i') }}</td></tr>
    @endforeach
    @foreach($payload['current_forecasts'] as $row)
        <tr><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>Current</td><td>{{ $row->forecast_amount }}</td><td>{{ $row->currency ?: '—' }}</td><td>{{ $row->fetched_at?->format('d M Y H:i') }}</td></tr>
    @endforeach
    </tbody></table>
    @endif
@elseif($type === 'security')
    <h3>Security score snapshots</h3>
    @if($payload['scores']->isEmpty())<div class="empty">No security score snapshots are available for the selected period.</div>
    @else
    <table><thead><tr><th>Subscription</th><th>Initiative</th><th>Score</th><th>Maximum</th><th>Percentage</th><th>Captured</th></tr></thead><tbody>
    @foreach($payload['scores'] as $row)
        <tr><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ $row->initiative_name ?: '—' }}</td><td>{{ $row->current_score }}</td><td>{{ $row->max_score }}</td><td>{{ number_format((float)$row->percentage_score, 2) }}%</td><td>{{ $row->captured_at?->format('d M Y H:i') }}</td></tr>
    @endforeach
    </tbody></table>
    @endif
    <h3>Security recommendations</h3>
    @if($payload['assessments']->isEmpty())<div class="empty">No security assessment records are available.</div>
    @else
    <table><thead><tr><th>Subscription</th><th>Recommendation</th><th>Severity</th><th>State</th><th>Resource</th></tr></thead><tbody>
    @foreach($payload['assessments'] as $row)
        <tr><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ $row->recommendation_name ?: $row->recommendation_id ?: '—' }}<div class="muted small">{{ $row->description }}</div></td><td>{{ $row->severity ?: '—' }}</td><td>{{ $row->state ?: '—' }}</td><td class="small">{{ $row->resource_id ?: '—' }}</td></tr>
    @endforeach
    </tbody></table>
    @endif
@elseif($type === 'alerts')
    @if($payload['rows']->isEmpty())<div class="empty">No Azure alerts were recorded during the selected period.</div>
    @else
    <table><thead><tr><th>Alert</th><th>Severity</th><th>Subscription</th><th>Resource</th><th>Fired</th><th>Status</th></tr></thead><tbody>
    @foreach($payload['rows'] as $row)
        <tr><td>{{ $row->name }}</td><td>{{ $row->severity }}</td><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ $row->affected_resource ?: '—' }}</td><td>{{ $row->fire_time?->format('d M Y H:i') }}</td><td>{{ $row->user_response ?: $row->suppression_status ?: '—' }}</td></tr>
    @endforeach
    </tbody></table>
    @endif
@elseif($type === 'web_urls')
    @if($payload['rows']->isEmpty())<div class="empty">No active website URL records are available for this scope.</div>
    @else
    <table><thead><tr><th>URL</th><th>Subscription</th><th>HTTP</th><th>Live</th><th>SSL</th><th>SSL expiry</th><th>Days left</th><th>Last checked</th></tr></thead><tbody>
    @foreach($payload['rows'] as $row)
        <tr><td>{{ $row->url }}</td><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ $row->http_status ?? '—' }}</td><td>{{ is_null($row->is_live) ? 'Unknown' : ($row->is_live ? 'Yes' : 'No') }}</td><td>{{ is_null($row->ssl_status) ? 'Unknown' : ($row->ssl_status ? 'Valid' : 'Invalid') }}</td><td>{{ $row->ssl_expires_at?->format('d M Y') ?? '—' }}</td><td>{{ $row->ssl_days_remaining ?? '—' }}</td><td>{{ $row->last_checked_at?->format('d M Y H:i') ?? '—' }}</td></tr>
    @endforeach
    </tbody></table>
    @endif
@elseif($type === 'resources')
    @if($payload['rows']->isEmpty())<div class="empty">No billing resource records are available for this scope.</div>
    @else
    <table><thead><tr><th>Resource</th><th>Type</th><th>Subscription</th><th>Region</th><th>SKU</th><th>Cost (EUR)</th></tr></thead><tbody>
    @foreach($payload['rows'] as $row)
        <tr><td>{{ $row->name ?: $row->resource_id }}</td><td>{{ $row->resource_type ?: '—' }}</td><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ $row->region ?: '—' }}</td><td>{{ $row->sku ?: '—' }}</td><td>{{ $row->cost_eur ?? '—' }}</td></tr>
    @endforeach
    </tbody></table>
    @endif
@endif
</body>
</html>
