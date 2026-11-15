<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <meta name="color-scheme" content="light">
    <title>Synchronization report · {{ \App\Support\FilamentStyling::brandName() }}</title>
    @if ($favicon = \App\Support\FilamentStyling::favicon())
        <link rel="icon" href="{{ $favicon }}">
    @endif
    @php
        $settings = \App\Support\FilamentStyling::settings();
        $palette = \App\Support\FilamentStyling::colors()['primary'] ?? [];
        $accent = $palette[600] ?? $palette[500] ?? '#b7791f';
        if (is_array($accent)) {
            $accent = sprintf('rgb(%d %d %d)', $accent[0] ?? 183, $accent[1] ?? 121, $accent[2] ?? 31);
        }
        $brandName = \App\Support\FilamentStyling::brandName();
        $brandLogo = \App\Support\FilamentStyling::logoLight();
        $modules = $report['modules'] ?? [];
        $successful = (int) ($report['success_count'] ?? collect($modules)->where('status', 'SUCCESS')->count());
        $failed = (int) ($report['failure_count'] ?? (count($modules) - $successful));
        $overallSuccess = ($report['overall_status'] ?? 'FAILED') === 'SUCCESS';
        $webProjects = $report['web_url_projects'] ?? [];
    @endphp
    <style>
        :root { --accent: {{ $accent }}; --ink: #202733; --muted: #687386; --line: #e5e9ef; --surface: #ffffff; --canvas: #f5f7fa; --good: #18794e; --good-bg: #eaf6ef; --bad: #b42318; --bad-bg: #fff0ef; }
        * { box-sizing: border-box; }
        html { background: var(--canvas); }
        body { margin: 0; color: var(--ink); background: var(--canvas); font-family: Inter, "Segoe UI", Arial, sans-serif; font-size: 14px; line-height: 1.5; -webkit-font-smoothing: antialiased; }
        .page { width: min(1120px, calc(100% - 48px)); margin: 0 auto; padding: 34px 0 48px; }
        .masthead { display:flex; align-items:center; justify-content:space-between; gap:24px; padding:0 0 22px; border-bottom:1px solid var(--line); }
        .brand { display:flex; align-items:center; min-width:0; gap:14px; }
        .logo { display:block; max-width:170px; max-height:46px; width:auto; height:auto; object-fit:contain; }
        .brand-name { color:#303846; font-size:15px; font-weight:700; letter-spacing:-.02em; }
        .masthead-note { display:flex; flex-direction:column; align-items:flex-end; gap:1px; color:var(--ink); text-align:right; white-space:nowrap; } .masthead-title { font-size:12px; font-weight:700; line-height:1.35; } .masthead-subtitle { color:var(--muted); font-size:11px; font-weight:400; line-height:1.35; white-space:nowrap; }
        .heading { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; padding:30px 0 22px; }
        h1 { margin:0; font-size:27px; line-height:1.2; letter-spacing:-.045em; font-weight:700; }
        .subtitle { margin:7px 0 0; color:var(--muted); font-size:13px; }
        .status { display:inline-flex; align-items:center; gap:7px; border-radius:999px; padding:7px 12px; font-size:11px; font-weight:700; letter-spacing:.045em; white-space:nowrap; }
        .status:before { content:""; width:7px; height:7px; border-radius:50%; background:currentColor; }
        .status.success { color:var(--good); background:var(--good-bg); }
        .status.failed { color:var(--bad); background:var(--bad-bg); }
        .stats { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:14px; margin:0 0 18px; } .summary-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin:0 0 18px; } .summary-stats .stat { min-height:88px; } .summary-stats .stat-value { font-size:27px; } .run-meta { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; margin:0 0 24px; } .run-meta .stat { min-height:88px; } .run-meta .stat-value { font-size:18px; overflow-wrap:anywhere; } .details-toolbar { display:flex; align-items:center; justify-content:space-between; gap:16px; } .expand-all { border:0; padding:0; color:var(--muted); background:none; font:inherit; font-size:12px; text-decoration:underline; text-underline-offset:3px; cursor:pointer; }
        .stat, .panel { background:var(--surface); border:1px solid var(--line); border-radius:10px; }
        .stat { min-height:104px; padding:17px 19px; }
        .stat-label { color:var(--muted); font-size:12px; font-weight:500; }
        .stat-value { margin-top:8px; color:#202733; font-size:25px; line-height:1.1; font-weight:700; letter-spacing:-.04em; font-variant-numeric:tabular-nums; }
        .stat-value.accent { color:var(--accent); }
        .stat-value.good { color:var(--good); }
        .stat-value.bad { color:var(--bad); }
        .section { margin-top:25px; }
        .section-head { display:flex; align-items:baseline; justify-content:space-between; gap:16px; margin-bottom:11px; }
        h2 { margin:0; font-size:15px; font-weight:700; letter-spacing:-.015em; }
        .section-note { color:var(--muted); font-size:12px; }
        .panel { overflow:hidden; }
        .table-wrap { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; text-align:left; }
        thead { background:#f8f9fb; }
        th { padding:11px 15px; color:#687386; font-size:10px; font-weight:700; letter-spacing:.075em; text-transform:uppercase; white-space:nowrap; }
        td { padding:12px 15px; border-top:1px solid #edf0f4; color:#354052; font-size:12px; vertical-align:middle; }
        tbody tr:first-child td { border-top:0; }
        .num, .time { font-variant-numeric:tabular-nums; white-space:nowrap; }
        .module-name { color:#202733; font-weight:650; }
        .pill { display:inline-flex; align-items:center; border-radius:999px; padding:4px 8px; font-size:10px; line-height:1.2; font-weight:700; letter-spacing:.035em; }
        .pill.success { color:var(--good); background:var(--good-bg); }
        .pill.failed { color:var(--bad); background:var(--bad-bg); }
        .module-list { display:grid; gap:16px; }
        .module { overflow:hidden; cursor:pointer; border:1px solid #dce2eb; border-radius:12px; box-shadow:0 2px 7px rgba(25, 38, 58, .045); transition:border-color .15s ease, box-shadow .15s ease; }
        .module:hover { border-color:#c5cedb; box-shadow:0 4px 12px rgba(25, 38, 58, .075); }
        .module[open] { border-color:#cfd7e2; }
        .module summary { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:16px 19px; cursor:pointer; list-style:none; }
        .module summary::-webkit-details-marker { display:none; }
        .module summary:after { content:"＋"; color:#8993a2; font-size:15px; }
        .module[open] summary:after { content:"−"; }
        .module-title { display:flex; align-items:center; gap:12px; min-width:0; }
        .module-index { color:#929baa; font-size:11px; font-weight:650; font-variant-numeric:tabular-nums; }
        .module-script { margin-top:2px; color:var(--muted); font-size:11px; overflow-wrap:anywhere; }
        .module-content { border-top:1px solid var(--line); }
        .module-meta { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); background:#fafbfc; }
        .meta-item { padding:12px 15px; border-right:1px solid var(--line); min-width:0; }
        .meta-item:last-child { border-right:0; }
        .meta-label { display:block; color:var(--muted); font-size:10px; }
        .meta-value { display:block; margin-top:3px; color:#354052; font-size:11px; font-weight:600; overflow-wrap:anywhere; }
        .console { border-top:1px solid var(--line); }
        .console-label { padding:9px 15px; color:#566174; background:#fff; font-size:11px; font-weight:600; }
        .console pre { max-height:420px; margin:0; padding:14px 16px; overflow:auto; color:#e5eaf1; background:#202733; font:11px/1.6 Consolas, "Courier New", monospace; white-space:pre-wrap; overflow-wrap:anywhere; }
        .empty { padding:22px; color:var(--muted); text-align:center; font-size:12px; }
        .footer { display:flex; justify-content:space-between; gap:16px; margin-top:25px; padding-top:15px; border-top:1px solid var(--line); color:#687386; font-size:12px; font-weight:600; }
        .print-button { border:1px solid var(--line); border-radius:7px; padding:8px 12px; color:#354052; background:#fff; font-family:inherit; font-size:11px; font-weight:600; cursor:pointer; }
        .print-button:hover { border-color:var(--accent); color:var(--accent); }
        @media(max-width:760px) { .page{width:calc(100% - 28px);padding-top:20px}.stats{grid-template-columns:repeat(2,minmax(0,1fr))}.summary-stats{grid-template-columns:repeat(3,minmax(0,1fr));gap:8px}.summary-stats .stat{padding:12px 10px;min-height:76px}.summary-stats .stat-value{font-size:23px}.run-meta{grid-template-columns:repeat(2,minmax(0,1fr))}.heading{align-items:flex-start;flex-direction:column}h1{font-size:23px}.module-meta{grid-template-columns:repeat(2,minmax(0,1fr))}.meta-item:nth-child(2){border-right:0}.meta-item:nth-child(-n+2){border-bottom:1px solid var(--line)}.masthead-note{max-width:none} }
        @media(max-width:420px) { .brand-name{font-size:13px}.logo{max-width:125px}.stat{padding:14px}.stat-value{font-size:22px}.footer{flex-direction:column} }
        @media print {
            @page { size:A4 portrait; margin:12mm; }
            html,body{background:#fff!important;color:#111!important;font-size:10pt;-webkit-print-color-adjust:exact;print-color-adjust:exact}
            .page{width:100%;padding:0;margin:0}
            .masthead{padding-bottom:12px}.masthead-note{white-space:nowrap}.masthead-title{font-size:10px}.masthead-subtitle{font-size:9px;white-space:nowrap}.logo{max-height:34px}.heading{padding:18px 0 14px}h1{font-size:21pt}
            .stats{gap:8px;margin-bottom:16px}.summary-stats{gap:8px;margin-bottom:12px}.summary-stats .stat{min-height:0;padding:9px 10px}.summary-stats .stat-value{font-size:17pt}.run-meta{gap:8px;margin-bottom:16px}.run-meta .stat{min-height:0}.run-meta .stat-value{font-size:12pt}.stat{min-height:0;padding:11px 13px;break-inside:avoid}.stat-value{font-size:19pt}
            .section{margin-top:17px}.panel,.stat{box-shadow:none;border-color:#d9dee6}
            .table-wrap{overflow:visible}table{width:100%}thead{display:table-header-group}tr{break-inside:avoid}th,td{padding:7px 8px;font-size:8pt}
            .module-list{gap:12px}.module{break-inside:avoid}.module summary{padding:9px 11px}.module summary:after{display:none}
            details:not([open]) > :not(summary){display:block!important}.console pre{max-height:none;overflow:visible;white-space:pre-wrap}
            .print-button,.expand-all{display:none!important}.footer{margin-top:16px}
            a{color:inherit;text-decoration:none}
        }
    </style>
</head>
<body>
<main class="page">
    <header class="masthead">
        <div class="brand">
            @if ($brandLogo)
                <img class="logo" src="{{ $brandLogo }}" alt="{{ $brandName }}">
            @else
                <span class="brand-name">{{ $brandName }}</span>
            @endif
        </div>
        <div class="masthead-note"><span class="masthead-title">Consolidated Cloud Ops Dashboard</span><span class="masthead-subtitle">Automated synchronization report</span></div>
    </header>

    <section class="heading">
        <div>
            <h1>Azure Synchronization report</h1>
            <p class="subtitle">Consolidated execution across configured synchronization modules</p>
        </div>
        <span class="status {{ $overallSuccess ? 'success' : 'failed' }}">{{ $report['overall_status'] ?? 'UNKNOWN' }}</span>
    </section>

    <section class="run-meta" aria-label="Run information">
        <article class="stat"><div class="stat-label">Run ID</div><div class="stat-value accent">{{ $report['run_id'] ?? '—' }}</div></article>
        <article class="stat"><div class="stat-label">Start time</div><div class="stat-value">{{ $report['started_at'] ?? '—' }}</div></article>
        <article class="stat"><div class="stat-label">End time</div><div class="stat-value">{{ $report['completed_at'] ?? '—' }}</div></article>
        <article class="stat"><div class="stat-label">Total duration</div><div class="stat-value accent">{{ $report['duration'] ?? '—' }}</div></article>
    </section>

    <section class="section">
        <div class="section-head"><h2>Synchronization details</h2></div>
        <section class="summary-stats" aria-label="Module summary">
            <article class="stat"><div class="stat-label">Modules</div><div class="stat-value">{{ count($modules) }}</div></article>
            <article class="stat"><div class="stat-label">Successful</div><div class="stat-value good">{{ $successful }}</div></article>
            <article class="stat"><div class="stat-label">Failed</div><div class="stat-value bad">{{ $failed }}</div></article>
        </section>
        <div class="panel table-wrap">
            @if (count($modules))
                <table>
                    <thead><tr><th>#</th><th>Module</th><th>Status</th><th>Duration</th><th>Completed</th></tr></thead>
                    <tbody>
                    @foreach ($modules as $index => $module)
                        <tr>
                            <td class="num">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="module-name">{{ $module['name'] ?? '—' }}</td>
                            <td><span class="pill {{ ($module['status'] ?? '') === 'SUCCESS' ? 'success' : 'failed' }}">{{ $module['status'] ?? 'UNKNOWN' }}</span></td>
                            <td class="num">{{ $module['duration_formatted'] ?? '—' }}</td>
                            <td class="time">{{ $module['ended_at'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty">No module results are available for this run.</div>
            @endif
        </div>
    </section>

    @if (count($webProjects))
        <section class="section">
            <div class="section-head"><h2>Web URL coverage by project</h2><span class="section-note">{{ count($webProjects) }} projects</span></div>
            <div class="panel table-wrap">
                <table>
                    <thead><tr><th>Project</th><th>Links checked</th><th>Live</th><th>Down</th></tr></thead>
                    <tbody>
                    @foreach ($webProjects as $project)
                        <tr>
                            <td class="module-name">{{ $project['name'] ?? '—' }}</td>
                            <td class="num">{{ $project['checked'] ?? 0 }}</td>
                            <td class="num">{{ $project['live'] ?? 0 }}</td>
                            <td class="num">{{ $project['down'] ?? 0 }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    <section class="section">
        @if (count($modules))
            <div class="module-list">
                @foreach ($modules as $index => $module)
                    <details class="panel module" open>
                        <summary>
                            <div class="module-title">
                                <span class="module-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <div>
                                    <div class="module-name">{{ $module['name'] ?? 'Module' }}</div>
                                    <div class="module-script">{{ $module['script'] ?? '' }}</div>
                                </div>
                            </div>
                            <span class="pill {{ ($module['status'] ?? '') === 'SUCCESS' ? 'success' : 'failed' }}">{{ $module['status'] ?? 'UNKNOWN' }}</span>
                        </summary>
                        <div class="module-content">
                            <div class="module-meta">
                                <div class="meta-item"><span class="meta-label">Started</span><span class="meta-value">{{ $module['started_at'] ?? '—' }}</span></div>
                                <div class="meta-item"><span class="meta-label">Completed</span><span class="meta-value">{{ $module['ended_at'] ?? '—' }}</span></div>
                                <div class="meta-item"><span class="meta-label">Duration</span><span class="meta-value">{{ $module['duration_formatted'] ?? '—' }}</span></div>
                                <div class="meta-item"><span class="meta-label">Exit code</span><span class="meta-value">{{ $module['exit_code'] ?? '—' }}</span></div>
                            </div>
                            <div class="console">
                                <div class="console-label">Console output</div>
                                <pre>{{ $module['output'] ?? 'No console output was recorded.' }}</pre>
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
        @else
            <div class="panel empty">No module details are available.</div>
        @endif
    </section>

    <footer class="footer">
        <span>Generated {{ $report['generated_at'] ?? '—' }}</span>
        <span>Generated by CCOD Scheduler</span>
    </footer>
</main>

</body>
</html>
