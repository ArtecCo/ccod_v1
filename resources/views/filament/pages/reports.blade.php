<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                Generate selected reports
            </x-filament::button>
        </div>
    </form>

    @php
        $reportLabels = [
            'overview' => 'Scope overview',
            'costs' => 'Costs, budgets and forecasts',
            'security' => 'Security posture and recommendations',
            'alerts' => 'Azure alerts',
            'web_urls' => 'Web URLs and SSL',
            'resources' => 'Azure resources',
            'consolidated' => 'Consolidated PDF',
        ];
    @endphp

    <x-filament::section
        heading="Generated reports"
        description="Your recent report requests, their status, and available PDF downloads. Reports are retained for seven days."
        wire:poll.5s
    >
        @forelse ($reports as $report)
            <div class="flex flex-col gap-3 py-4 first:pt-0 last:pb-0">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <div class="font-medium text-gray-950 dark:text-white">
                            {{ $report->scope_name }}
                        </div>
                        <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ str($report->scope_type)->replace('_', ' ')->title() }}
                            <span aria-hidden="true">·</span>
                            {{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }}
                            <span aria-hidden="true">·</span>
                            Requested {{ $report->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center gap-2">
                        <x-filament::badge :color="match ($report->status) {
                            'completed' => 'success',
                            'failed' => 'danger',
                            'processing' => 'info',
                            'queued' => 'gray',
                            default => 'gray',
                        }">
                            {{ str($report->status)->headline() }}
                        </x-filament::badge>

                        @foreach (($report->report_types ?? []) as $type)
                            <x-filament::badge color="gray">
                                {{ $reportLabels[$type] ?? str($type)->headline() }}
                            </x-filament::badge>
                        @endforeach
                    </div>
                </div>

                @if ($report->failure_reason)
                    <div class="text-sm text-danger-600 dark:text-danger-400">
                        <span class="font-medium">Generation failed:</span>
                        {{ $report->failure_reason }}
                    </div>
                @endif

                @if (! empty($report->files))
                    <div class="flex flex-wrap gap-2">
                        @foreach ($report->files as $file)
                            <x-filament::button
                                size="sm"
                                color="gray"
                                outlined
                                tag="a"
                                icon="heroicon-o-arrow-down-tray"
                                :href="route('developer.reports.download', ['report' => $report, 'file' => $file['key']])"
                            >
                                Download {{ $file['label'] }}
                            </x-filament::button>
                        @endforeach
                    </div>
                @elseif (in_array($report->status, ['queued', 'processing'], true))
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <x-filament::loading-indicator />
                        Generating selected report(s)…
                    </div>
                @endif
            </div>
            @unless ($loop->last)
                <hr class="border-gray-200 dark:border-gray-700">
            @endunless
        @empty
            <div class="py-4 text-sm text-gray-500 dark:text-gray-400">
                No reports have been requested yet. Select a scope, reporting period and report type above, then generate your PDF.
            </div>
        @endforelse
    </x-filament::section>
</x-filament-panels::page>
