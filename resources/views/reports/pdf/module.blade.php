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
.cover-rule { width:70px; border-top:2px solid #b18a43; margin:24px auto; }
.page-break { page-break-after:always; }
.header { position:fixed; top:-17mm; left:0; right:0; height:10mm; border-bottom:1px solid #d8dde2; font-size:8pt; color:#66727e; }
.header-logo { max-width:90px; max-height:22px; vertical-align:middle; }
.page-number:before { content: counter(page) " / " counter(pages); }
.footer { position:fixed; bottom:-14mm; left:0; right:0; border-top:1px solid #d8dde2; padding-top:5px; color:#77818a; font-size:7pt; }
.footer .right { float:right; }
h2 { font-size:15pt; font-weight:normal; margin:0 0 10px; color:#1e2933; }
h3 { font-size:10pt; margin:17px 0 7px; color:#394653; }
p { margin:4px 0 9px; }
table { width:100%; border-collapse:collapse; margin:8px 0 15px; }
thead { display:table-header-group; }
tr { page-break-inside:avoid; }
th { text-align:left; background:#f1f3f5; color:#52606d; font-size:7.5pt; font-weight:bold; padding:7px 6px; border-bottom:1px solid #d7dde2; }
td { padding:6px; border-bottom:1px solid #e3e7ea; vertical-align:top; font-size:8pt; }
.muted { color:#687582; }
.empty { padding:14px; border:1px solid #e0e4e8; color:#687582; }
.small { font-size:7.5pt; }
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
    @php
        $history = $payload['history'];
        $historyValues = $history->pluck('actual_cost')->map(fn ($value) => $value === null ? null : (float) $value)->all();
        $validHistory = array_values(array_filter($historyValues, fn ($value) => $value !== null));
        $maxHistory = count($validHistory) ? max($validHistory) : 0;
        $historyPoints = [];
        $historyCount = count($historyValues);
        foreach ($historyValues as $index => $value) {
            if ($value === null || $maxHistory <= 0) { $historyPoints[] = null; continue; }
            $x = $historyCount > 1 ? 40 + ($index * 620 / ($historyCount - 1)) : 350;
            $y = 150 - (($value / $maxHistory) * 115);
            $historyPoints[] = number_format($x, 2, '.', '').','.number_format($y, 2, '.', '');
        }
        $currencyValues = $history->pluck('currency')->filter()->unique();
        $currency = $currencyValues->count() === 1 ? $currencyValues->first() : null;
        $currentHistory = $history->first(fn ($row) => $row->period_start->format('Y-m') === now()->format('Y-m'));
        $actualMtd = $currentHistory?->actual_cost;
        $budgetTotal = $payload['budgets']->sum(fn ($row) => (float) $row->amount);
        $budgetCurrencies = $payload['budgets']->pluck('currency')->filter()->unique();
        $budgetCurrency = $budgetCurrencies->count() === 1 ? $budgetCurrencies->first() : null;
        $forecastRows = $payload['current_forecasts'];
        $forecastCurrencies = $forecastRows->pluck('currency')->filter()->unique();
        $forecastCurrency = $forecastCurrencies->count() === 1 ? $forecastCurrencies->first() : null;
        $forecastTotal = $forecastCurrency && $currency === $forecastCurrency && $actualMtd !== null
            ? (float) $actualMtd + $forecastRows->sum(fn ($row) => (float) $row->forecast_amount)
            : null;
        $distributionTotal = $payload['resource_type_breakdown']->sum(fn ($row) => (float) $row->total_cost);
        $pieColors = [$primaryColor, '#64748b', '#0f766e', '#a16207', '#7c3aed', '#94a3b8'];
        $pieSegments = [];
        $pieAngle = -90;
        foreach ($payload['resource_type_breakdown']->take(6) as $index => $row) {
            $value = (float) $row->total_cost;
            if ($distributionTotal <= 0 || $value <= 0) { continue; }
            $angle = ($value / $distributionTotal) * 360;
            $startAngle = deg2rad($pieAngle);
            $endAngle = deg2rad($pieAngle + $angle);
            $x1 = 100 + 82 * cos($startAngle); $y1 = 100 + 82 * sin($startAngle);
            $x2 = 100 + 82 * cos($endAngle); $y2 = 100 + 82 * sin($endAngle);
            $largeArc = $angle > 180 ? 1 : 0;
            $pieSegments[] = ['path' => "M 100 100 L {$x1} {$y1} A 82 82 0 {$largeArc} 1 {$x2} {$y2} Z", 'color' => $pieColors[$index % count($pieColors)]];
            $pieAngle += $angle;
        }
    @endphp

    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-label">Accumulated cost · selected period</div>
            <div class="summary-value">{{ $currency ? $currency.' '.number_format((float) $payload['costs']->sum(fn ($row) => (float) $row->amount), 2) : 'Unavailable' }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Current month budget</div>
            <div class="summary-value">{{ $budgetCurrency ? $budgetCurrency.' '.number_format($budgetTotal, 2) : 'Unavailable' }}</div>
        </div>
        <div class="summary-card">
            <div class="summary-label">Current month forecast</div>
            <div class="summary-value">{{ $forecastTotal !== null ? $forecastCurrency.' '.number_format($forecastTotal, 2) : 'Unavailable' }}</div>
        </div>
    </div>

    <h3>12-month accumulated cost history</h3>
    @if(count($validHistory) > 0)
        <div class="chart-panel">
            <svg width="100%" height="190" viewBox="0 0 700 190" xmlns="http://www.w3.org/2000/svg">
                <line x1="40" y1="150" x2="660" y2="150" stroke="#cbd5e1" stroke-width="1"/>
                <line x1="40" y1="35" x2="40" y2="150" stroke="#cbd5e1" stroke-width="1"/>
                @foreach($historyPoints as $point)
                    @if($point !== null)
                        @php [$px,$py] = explode(',', $point); @endphp
                        <circle cx="{{ $px }}" cy="{{ $py }}" r="3.5" fill="{{ $primaryColor }}"/>
                    @endif
                @endforeach
                @php $segments = []; $previousPoint = null; foreach($historyPoints as $point) { if($point === null) { $previousPoint = null; continue; } if($previousPoint !== null) $segments[] = $previousPoint.' '.$point; $previousPoint = $point; } @endphp
                @foreach($segments as $segment)
                    <polyline points="{{ $segment }}" fill="none" stroke="{{ $primaryColor }}" stroke-width="2.5" stroke-linecap="round"/>
                @endforeach
                @foreach($history as $index => $row)
                    @php $x = $historyCount > 1 ? 40 + ($index * 620 / ($historyCount - 1)) : 350; @endphp
                    <text x="{{ $x }}" y="174" text-anchor="middle" font-size="9" fill="#64748b">{{ $row->period_start->format('M y') }}</text>
                @endforeach
            </svg>
            <div class="small muted">Actual cost by recorded month. Gaps represent months without recorded values.</div>
        </div>
        <table><thead><tr><th>Month</th><th>Actual cost</th><th>Budget</th><th>Forecast</th></tr></thead><tbody>
        @foreach($history as $row)
            <tr><td>{{ $row->period_start->format('M Y') }}</td><td>{{ $row->actual_cost !== null && $row->currency ? $row->currency.' '.number_format((float)$row->actual_cost, 2) : '—' }}</td><td>{{ $row->budget_total !== null ? number_format((float)$row->budget_total, 2) : '—' }}</td><td>{{ $row->forecast_total !== null ? number_format((float)$row->forecast_total, 2) : '—' }}</td></tr>
        @endforeach
        </tbody></table>
    @else
        <div class="empty">No accumulated cost history is recorded for this scope.</div>
    @endif

    <h3>Current month budget and forecast</h3>
    <table><thead><tr><th>Subscription</th><th>Budget</th><th>Actual spend</th><th>Forecast</th><th>Currency</th></tr></thead><tbody>
    @forelse($payload['budgets'] as $row)
        <tr><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ number_format((float)$row->amount, 2) }}</td><td>{{ number_format((float)$row->current_spend, 2) }}</td><td>{{ number_format((float)$row->forecast_spend, 2) }}</td><td>{{ $row->currency ?: '—' }}</td></tr>
    @empty
        <tr><td colspan="5" class="muted">No active budget records are available.</td></tr>
    @endforelse
    </tbody></table>

    <h3>Cost distribution by resource type</h3>
    @if($payload['resource_type_breakdown']->isNotEmpty() && $distributionTotal > 0)
        <div class="distribution">
            <div class="pie-chart">
                <svg width="190" height="190" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    @foreach($pieSegments as $segment)
                        <path d="{{ $segment['path'] }}" fill="{{ $segment['color'] }}"/>
                    @endforeach
                    <circle cx="100" cy="100" r="48" fill="#ffffff"/>
                    <text x="100" y="96" text-anchor="middle" font-size="10" fill="#64748b">Total</text>
                    <text x="100" y="112" text-anchor="middle" font-size="10" font-weight="bold" fill="#1e293b">{{ number_format($distributionTotal, 0) }}</text>
                </svg>
            </div>
            <div class="distribution-table">
                <table><thead><tr><th>Resource type</th><th>Cost (EUR)</th><th>Share</th></tr></thead><tbody>
                @foreach($payload['resource_type_breakdown'] as $row)
                    <tr><td>{{ $row->resource_type }}</td><td>{{ number_format((float)$row->total_cost, 2) }}</td><td>{{ number_format(((float)$row->total_cost / $distributionTotal) * 100, 1) }}%</td></tr>
                @endforeach
                </tbody></table>
            </div>
        </div>
        <p class="small muted">Resource-type totals use the same BillingResource cost_eur aggregation as the consolidated dashboard.</p>
    @else
        <div class="empty">No resource-level cost breakdown is available.</div>
    @endif

    <h3>Top 10 cost-consuming resources · Azure month-to-date snapshot</h3>
    @if($payload['top_resources']->isNotEmpty())
        <table><thead><tr><th>#</th><th>Resource</th><th>Type</th><th>Subscription</th><th>Cost (EUR)</th></tr></thead><tbody>
        @foreach($payload['top_resources'] as $index => $row)
            <tr><td>{{ $index + 1 }}</td><td>{{ $row->name ?: $row->resource_id }}</td><td>{{ $row->resource_type ?: 'Unknown' }}</td><td>{{ $row->subscription?->display_name ?? $row->subscription_id }}</td><td>{{ number_format((float)$row->cost_eur, 2) }}</td></tr>
        @endforeach
        </tbody></table>
    @else
        <div class="empty">No resource-level month-to-date costs are available.</div>
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
