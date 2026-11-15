<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive">
    <title>Synchronization report · {{ \App\Support\FilamentStyling::brandName() }}</title>
    @if ($favicon = \App\Support\FilamentStyling::favicon())
        <link rel="icon" href="{{ $favicon }}">
    @endif
    @vite(['resources/css/app.css', 'resources/css/filament/developer/theme.css'])
    @php
        $palettes = \App\Support\FilamentStyling::colors();
        $themeVariables = [];
        foreach (['primary', 'success', 'warning', 'danger', 'info', 'gray'] as $palette) {
            foreach (($palettes[$palette] ?? []) as $shade => $value) {
                if (is_string($value) && (str_starts_with($value, '#') || preg_match('/^(oklch|rgb|hsl|color)\(/i', $value))) {
                    $themeVariables["--{$palette}-{$shade}"] = $value;
                }
            }
        }
        $themeVariables['--ccod-corner-radius'] = \App\Support\FilamentStyling::cornerRadius();
        $brandName = \App\Support\FilamentStyling::brandName();
        $brandLogo = \App\Support\FilamentStyling::logoLight();
    @endphp
    <style>
        :root { {!! collect($themeVariables)->map(fn ($value, $key) => $key . ':' . $value)->implode(';') !!}; }
        html { font-size: 13px; }
        body { min-height: 100vh; background: var(--gray-50, #f9fafb); }
        .public-report-shell { max-width: 1440px; margin-inline: auto; padding: 1.5rem; }
        .public-report-brand { display:flex; align-items:center; gap:.85rem; min-width:0; }
        .public-report-logo { max-width: 160px; max-height: 42px; object-fit:contain; }
        .public-report-brand-name { font-size:1rem; font-weight:650; letter-spacing:-.02em; color:var(--gray-950,#111827); }
        .public-report-header { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem 1.25rem; margin-bottom:1.5rem; background:white; border:1px solid var(--gray-200,#e5e7eb); border-radius:var(--ccod-corner-radius,.375rem); }
        .public-report-header-meta { color:var(--gray-500,#6b7280); font-size:.75rem; }
        @media(max-width:640px) { .public-report-shell { padding: .75rem; } .public-report-header { align-items:flex-start; flex-direction:column; } }
        @media print { body { background:white !important; } .public-report-shell { max-width:none; padding:0; } }
    </style>
</head>
<body>
    <main class="public-report-shell">
        <header class="public-report-header">
            <div class="public-report-brand">
                @if ($brandLogo)
                    <img class="public-report-logo" src="{{ $brandLogo }}" alt="{{ $brandName }}">
                @else
                    <span class="public-report-brand-name">{{ $brandName }}</span>
                @endif
            </div>
            <div class="public-report-header-meta">Azure Operations · Synchronization report</div>
        </header>
        <div class="mb-5">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">Azure synchronization report</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest unified scheduler execution</p>
        </div>
            @php
        $modules = $report['modules'] ?? [];
        $successful = (int) ($report['success_count'] ?? 0);
        $failed = (int) ($report['failure_count'] ?? 0);
        $overallSuccess = ($report['overall_status'] ?? 'FAILED') === 'SUCCESS';
    @endphp

    <div class="space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5">
                    <x-filament::icon icon="heroicon-o-clock" class="h-4 w-4" />
                    {{ $report['started_at'] ?? '—' }}
                </span>
                <span class="text-gray-300 dark:text-gray-600">→</span>
                <span>{{ $report['completed_at'] ?? '—' }}</span>
                <span class="text-gray-300 dark:text-gray-600">·</span>
                <span class="font-mono text-xs">{{ $report['run_id'] ?? '—' }}</span>
            </div>
            <x-filament::badge :color="$overallSuccess ? 'success' : 'danger'" size="lg">
                {{ $report['overall_status'] ?? 'UNKNOWN' }}
            </x-filament::badge>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-filament::section compact>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-clock" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                    <div class="min-w-0">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Total elapsed</div>
                        <div class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ $report['duration'] ?? '—' }}</div>
                    </div>
                </div>
            </x-filament::section>
            <x-filament::section compact>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-queue-list" class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                    <div class="min-w-0">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Modules executed</div>
                        <div class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ count($modules) }}</div>
                    </div>
                </div>
            </x-filament::section>
            <x-filament::section compact>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-check-circle" class="h-5 w-5 text-success-600 dark:text-success-400" />
                    <div class="min-w-0">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Successful</div>
                        <div class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ $successful }}</div>
                    </div>
                </div>
            </x-filament::section>
            <x-filament::section compact>
                <div class="flex items-center gap-3">
                    <x-filament::icon icon="heroicon-o-x-circle" class="h-5 w-5 text-danger-600 dark:text-danger-400" />
                    <div class="min-w-0">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Failed</div>
                        <div class="text-2xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ $failed }}</div>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <x-filament::section heading="Execution timeline" icon="heroicon-o-list-bullet">
            <div class="-mx-4 overflow-x-auto sm:-mx-6">
                <table class="w-full divide-y divide-gray-200 text-start dark:divide-white/10">
                    <thead class="bg-gray-50 dark:bg-white/5">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">#</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Module</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Status</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Elapsed</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Completed</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                        @foreach ($modules as $index => $module)
                            <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
                                <td class="whitespace-nowrap px-4 py-3 text-sm tabular-nums text-gray-500 sm:px-6">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-950 dark:text-white">{{ $module['name'] ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-6">
                                    <x-filament::badge :color="($module['status'] ?? '') === 'SUCCESS' ? 'success' : 'danger'">{{ $module['status'] ?? 'UNKNOWN' }}</x-filament::badge>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm tabular-nums text-gray-700 dark:text-gray-300">{{ $module['duration_formatted'] ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ $module['ended_at'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        @if (! empty($report['web_url_projects']))
            <x-filament::section heading="Web URL coverage by project" icon="heroicon-o-globe-alt">
                <div class="-mx-4 overflow-x-auto sm:-mx-6">
                    <table class="w-full divide-y divide-gray-200 text-start dark:divide-white/10">
                        <thead class="bg-gray-50 dark:bg-white/5">
                            <tr>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Project</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Links checked</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Live</th>
                                <th class="px-4 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500 sm:px-6">Down</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-white/10">
                            @foreach ($report['web_url_projects'] as $project)
                                <tr class="hover:bg-gray-50 dark:hover:bg-white/5">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-950 dark:text-white sm:px-6">{{ $project['name'] }}</td>
                                    <td class="px-4 py-3 text-sm tabular-nums text-gray-700 dark:text-gray-300 sm:px-6">{{ $project['checked'] }}</td>
                                    <td class="px-4 py-3 text-sm tabular-nums text-success-600 dark:text-success-400 sm:px-6">{{ $project['live'] }}</td>
                                    <td class="px-4 py-3 text-sm tabular-nums text-danger-600 dark:text-danger-400 sm:px-6">{{ $project['down'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        @endif

        <x-filament::section heading="Module details" icon="heroicon-o-rectangle-stack">
            <div class="space-y-3">
                @foreach ($modules as $index => $module)
                    <x-filament::section collapsible :collapsed="($module['status'] ?? '') === 'SUCCESS'" compact>
                        <x-slot name="heading">
                            <span class="mr-2 text-gray-400">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                            {{ $module['name'] ?? 'Module' }}
                        </x-slot>
                        <x-slot name="description">{{ $module['script'] ?? '' }}</x-slot>

                        <div class="mb-3">
                            <x-filament::badge :color="($module['status'] ?? '') === 'SUCCESS' ? 'success' : 'danger'">{{ $module['status'] ?? 'UNKNOWN' }}</x-filament::badge>
                        </div>

                        <div class="grid grid-cols-1 gap-4 border-t border-gray-200 pt-4 dark:border-white/10 sm:grid-cols-2 xl:grid-cols-4">
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Started</div>
                                <div class="mt-1 text-sm text-gray-950 dark:text-white">{{ $module['started_at'] ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Ended</div>
                                <div class="mt-1 text-sm text-gray-950 dark:text-white">{{ $module['ended_at'] ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Elapsed</div>
                                <div class="mt-1 text-sm tabular-nums text-gray-950 dark:text-white">{{ $module['duration_formatted'] ?? '—' }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-medium text-gray-500 dark:text-gray-400">Exit code</div>
                                <div class="mt-1 text-sm tabular-nums text-gray-950 dark:text-white">{{ $module['exit_code'] ?? '—' }}</div>
                            </div>
                        </div>

                        <details class="mt-4 overflow-hidden rounded-lg border border-gray-200 dark:border-white/10">
                            <summary class="cursor-pointer bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:bg-white/5 dark:text-gray-300">Console output</summary>
                            <pre class="max-h-[32rem] overflow-auto whitespace-pre-wrap break-words bg-gray-950 p-4 text-xs leading-relaxed text-gray-100">{{ $module['output'] ?? 'No console output was recorded.' }}</pre>
                        </details>
                    </x-filament::section>
                @endforeach
            </div>
        </x-filament::section>

        <div class="text-xs text-gray-400 dark:text-gray-500">
            Generated {{ $report['generated_at'] ?? '—' }} · Run ID {{ $report['run_id'] ?? '—' }}
        </div>
    </div>
    </main>
</body>
</html>
