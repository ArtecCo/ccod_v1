<div class="ccod-dashboard">
    <style>
        .ccod-dashboard{--ccod-border:#e5e7eb;--ccod-muted:#64748b;--ccod-ink:#111827;--ccod-surface:#fff;--ccod-soft:#f8fafc;color:var(--ccod-ink);font-family:inherit}
        .dark .ccod-dashboard{--ccod-border:#334155;--ccod-muted:#94a3b8;--ccod-ink:#f1f5f9;--ccod-surface:#111827;--ccod-soft:#1e293b}
        .ccod-dashboard *{box-sizing:border-box}
        .ccod-dashboard .ccod-top{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:22px 24px;background:var(--ccod-surface);border:1px solid var(--ccod-border);border-radius:14px;margin-bottom:18px}
        .ccod-dashboard .ccod-kicker{font-size:11px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#64748b;margin-bottom:6px}
        .ccod-dashboard .ccod-heading{font-size:21px;line-height:1.3;font-weight:700;color:var(--ccod-ink);margin:0}
        .ccod-dashboard .ccod-subtitle{font-size:13px;color:var(--ccod-muted);margin:5px 0 0}
        .ccod-dashboard .ccod-filter{width:260px;max-width:100%;font:inherit;font-size:13px;color:var(--ccod-ink);background:var(--ccod-surface);border:1px solid var(--ccod-border);border-radius:8px;padding:10px 34px 10px 12px}
        .ccod-dashboard .ccod-filter-label{display:block;font-size:11px;font-weight:650;color:var(--ccod-muted);margin:0 0 6px}
        .ccod-dashboard .ccod-tabs{display:flex;gap:5px;align-items:center;overflow-x:auto;border-bottom:1px solid var(--ccod-border);margin-bottom:20px;padding:0 0 10px}
        .ccod-dashboard .ccod-tab{border:1px solid transparent;border-radius:8px;padding:9px 15px;background:transparent;color:var(--ccod-muted);font:inherit;font-size:13px;font-weight:600;white-space:nowrap;cursor:pointer}
        .ccod-dashboard .ccod-tab:hover{background:var(--ccod-soft);color:var(--ccod-ink)}
        .ccod-dashboard .ccod-tab.is-active{background:#fff7ed;border-color:#fed7aa;color:#c2410c}
        .ccod-dashboard .ccod-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin-bottom:18px}
        .ccod-dashboard .ccod-card{background:var(--ccod-surface);border:1px solid var(--ccod-border);border-radius:12px;padding:18px;min-width:0}
        .ccod-dashboard .ccod-card-label{font-size:12px;font-weight:550;color:var(--ccod-muted)}
        .ccod-dashboard .ccod-card-value{font-size:28px;line-height:1.2;font-weight:700;color:var(--ccod-ink);margin-top:11px}
        .ccod-dashboard .ccod-card-note{font-size:11px;color:var(--ccod-muted);margin-top:5px}
        .ccod-dashboard .ccod-panel{background:var(--ccod-surface);border:1px solid var(--ccod-border);border-radius:12px;padding:20px;margin-bottom:16px}
        .ccod-dashboard .ccod-panel-title{font-size:14px;font-weight:700;color:var(--ccod-ink);margin:0 0 14px}
        .ccod-dashboard .ccod-row{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:13px 0;border-top:1px solid var(--ccod-border)}
        .ccod-dashboard .ccod-row:first-of-type{border-top:0}
        .ccod-dashboard .ccod-main{font-size:13px;font-weight:600;color:var(--ccod-ink)}
        .ccod-dashboard .ccod-meta{font-size:11px;color:var(--ccod-muted);margin-top:4px}
        .ccod-dashboard .ccod-pill{display:inline-block;padding:4px 8px;border-radius:999px;background:var(--ccod-soft);font-size:11px;color:var(--ccod-muted);white-space:nowrap}
        .ccod-dashboard .ccod-empty{font-size:13px;color:var(--ccod-muted);padding:10px 0}
        .ccod-dashboard .ccod-loading{font-size:12px;color:var(--ccod-muted);margin:0 0 12px}
        @media(max-width:900px){.ccod-dashboard .ccod-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media(max-width:640px){.ccod-dashboard .ccod-top{align-items:stretch;flex-direction:column;padding:18px}.ccod-dashboard .ccod-filter{width:100%}.ccod-dashboard .ccod-grid{grid-template-columns:repeat(2,minmax(0,1fr));gap:9px}.ccod-dashboard .ccod-card{padding:14px}.ccod-dashboard .ccod-card-value{font-size:23px}.ccod-dashboard .ccod-panel{padding:15px}.ccod-dashboard .ccod-row{align-items:flex-start;flex-direction:column;gap:7px}}
    </style>

    <section class="ccod-top">
        <div>
            <div class="ccod-kicker">Cloud operations · Overview</div>
            <h2 class="ccod-heading">Azure estate dashboard</h2>
            <p class="ccod-subtitle">Monitor the subscriptions and resources available to your account.</p>
        </div>
        <div>
            <label class="ccod-filter-label" for="dashboard-scope-filter">Dashboard scope</label>
            <select id="dashboard-scope-filter" wire:model.live="filter" class="ccod-filter">
                <option value="all">All projects</option>
                @if ($filterOptions['businessUnits']->isNotEmpty())
                    <optgroup label="Business units">
                        @foreach ($filterOptions['businessUnits'] as $unit)
                            <option value="business_unit:{{ $unit->id }}">{{ $unit->name }}</option>
                        @endforeach
                    </optgroup>
                @endif
                @if ($filterOptions['projects']->isNotEmpty())
                    <optgroup label="Projects">
                        @foreach ($filterOptions['projects'] as $project)
                            <option value="project:{{ $project->id }}">{{ $project->name }}{{ $project->businessUnit ? ' · '.$project->businessUnit->name : '' }}</option>
                        @endforeach
                    </optgroup>
                @endif
            </select>
        </div>
    </section>

    <nav class="ccod-tabs" role="tablist" aria-label="Dashboard sections">
        @foreach (['overview' => 'Overview', 'cost' => 'Cost', 'alerts' => 'Alerts', 'security' => 'Security', 'resources' => 'Resources'] as $key => $label)
            @if ($visibleTabs->contains($key))
                <button type="button" wire:click="setTab('{{ $key }}')" role="tab" aria-selected="{{ $activeTab === $key ? 'true' : 'false' }}" class="ccod-tab {{ $activeTab === $key ? 'is-active' : '' }}">{{ $label }}</button>
            @endif
        @endforeach
    </nav>

    <div wire:loading.flex wire:target="filter,setTab" class="ccod-loading">Updating dashboard…</div>

    @if ($activeTab === 'overview')
        <div class="ccod-grid">
            <article class="ccod-card"><div class="ccod-card-label">Accessible subscriptions</div><div class="ccod-card-value">{{ number_format($subscriptionCount) }}</div><div class="ccod-card-note">Within your access scope</div></article>
            <article class="ccod-card"><div class="ccod-card-label">Projects in scope</div><div class="ccod-card-value">{{ number_format($projectCount) }}</div><div class="ccod-card-note">Matching selected filter</div></article>
            <article class="ccod-card"><div class="ccod-card-label">Active alerts</div><div class="ccod-card-value">{{ number_format($alertTotal) }}</div><div class="ccod-card-note">Across permitted subscriptions</div></article>
            <article class="ccod-card"><div class="ccod-card-label">Resources</div><div class="ccod-card-value">{{ number_format($resourceCount) }}</div><div class="ccod-card-note">Discovered resources</div></article>
        </div>
        <section class="ccod-panel">
            <h3 class="ccod-panel-title">Subscriptions in scope</h3>
            @forelse ($subscriptions as $subscription)
                <div class="ccod-row">
                    <div><div class="ccod-main">{{ $subscription->display_name }}</div><div class="ccod-meta">{{ $subscription->projects->map(fn ($project) => ($project->businessUnit?->name ? $project->businessUnit->name.' / ' : '').$project->name)->implode(', ') }}</div></div>
                    <span class="ccod-pill">{{ $subscription->health_status ?: 'Status unavailable' }}</span>
                </div>
            @empty
                <p class="ccod-empty">No subscriptions are available in this dashboard scope.</p>
            @endforelse
        </section>
    @elseif ($activeTab === 'cost')
        <div class="ccod-grid">
            @forelse ($costTotals as $total)
                <article class="ccod-card"><div class="ccod-card-label">Month-to-date cost · {{ $total['currency'] }}</div><div class="ccod-card-value">{{ number_format($total['amount'], 2) }}</div></article>
            @empty
                <article class="ccod-card"><div class="ccod-empty">No cost data is available for this scope.</div></article>
            @endforelse
        </div>
        <section class="ccod-panel"><h3 class="ccod-panel-title">Subscription cost summary</h3>
            @forelse ($costSubscriptions as $subscription)
                <div class="ccod-row"><span class="ccod-main">{{ $subscription->display_name }}</span><span class="ccod-pill">EUR {{ number_format((float) ($subscription->mtd_spend_eur ?? 0), 2) }}</span></div>
            @empty
                <p class="ccod-empty">No subscriptions in scope.</p>
            @endforelse
        </section>
    @elseif ($activeTab === 'alerts')
        <div class="ccod-grid" style="grid-template-columns:repeat(6,minmax(0,1fr))">
            @foreach (['Sev0', 'Sev1', 'Sev2', 'Sev3', 'Sev4'] as $severity)
                <article class="ccod-card"><div class="ccod-card-label">{{ $severity }}</div><div class="ccod-card-value">{{ number_format((int) ($alertCounts[$severity] ?? 0)) }}</div></article>
            @endforeach
            <article class="ccod-card"><div class="ccod-card-label">Total</div><div class="ccod-card-value">{{ number_format($alertTotal) }}</div></article>
        </div>
        <section class="ccod-panel"><h3 class="ccod-panel-title">Latest alerts</h3>
            @forelse ($recentAlerts as $alert)
                <div class="ccod-row"><div><div class="ccod-main">{{ $alert->name ?: 'Azure alert' }}</div><div class="ccod-meta">{{ $alert->subscription?->display_name }} · {{ $alert->affected_resource ?: 'Resource unavailable' }}</div></div><span class="ccod-pill">{{ $alert->severity }} · {{ $alert->fire_time?->format('d M Y, H:i') ?: 'Time unavailable' }}</span></div>
            @empty
                <p class="ccod-empty">No alerts found in the selected scope.</p>
            @endforelse
        </section>
    @elseif ($activeTab === 'security')
        <div class="ccod-grid" style="grid-template-columns:repeat(3,minmax(0,1fr))">
            <article class="ccod-card"><div class="ccod-card-label">Assessments</div><div class="ccod-card-value">{{ number_format((int) ($securityStats?->total ?? 0)) }}</div></article>
            <article class="ccod-card"><div class="ccod-card-label">Unhealthy</div><div class="ccod-card-value">{{ number_format((int) ($securityStats?->unhealthy ?? 0)) }}</div></article>
            <article class="ccod-card"><div class="ccod-card-label">High severity</div><div class="ccod-card-value">{{ number_format((int) ($securityStats?->high ?? 0)) }}</div></article>
        </div>
        <section class="ccod-panel"><p class="ccod-empty">Security assessment totals are calculated only from subscriptions in the selected dashboard scope.</p></section>
    @elseif ($activeTab === 'resources')
        <div class="ccod-grid"><article class="ccod-card"><div class="ccod-card-label">Total discovered resources</div><div class="ccod-card-value">{{ number_format($resourceCount) }}</div></article></div>
        <section class="ccod-panel"><h3 class="ccod-panel-title">Resource types</h3>
            @forelse ($resourcesByType as $resourceType)
                <div class="ccod-row"><span class="ccod-main">{{ $resourceType->resource_type ?: 'Unclassified' }}</span><span class="ccod-pill">{{ number_format((int) $resourceType->aggregate) }}</span></div>
            @empty
                <p class="ccod-empty">No resource data is available for this scope.</p>
            @endforelse
        </section>
    @endif
</div>
