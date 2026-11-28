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
        description="Each request is listed with the report types selected, its processing status, and the available PDF downloads. Files are retained for seven days."
        wire:poll.5s
    >
        @forelse ($reports as $report)
            <x-filament::section
                :heading="$report->scope_name"
                :description="ucfirst(str_replace('_', ' ', $report->scope_type)) . ' · ' . $report->period_start->format('d M Y') . ' – ' . $report->period_end->format('d M Y') . ' · Requested ' . $report->created_at->format('d M Y, H:i')"
                compact
            >
                <div class="flex flex-wrap items-center gap-2">
                    <x-filament::badge :color="match ($report->status) {
                        'completed' => 'success',
                        'failed' => 'danger',
                        'processing' => 'info',
                        default => 'gray',
                    }">
                        {{ ucfirst($report->status) }}
                    </x-filament::badge>
                    <span class="text-sm text-gray-500">Requested report type(s):</span>
                    @foreach (($report->report_types ?? []) as $type)
                        <x-filament::badge color="gray">
                            {{ $reportLabels[$type] ?? str($type)->headline() }}
                        </x-filament::badge>
                    @endforeach
                </div>

                @if ($report->failure_reason)
                    <div class="mt-3">
                        <x-filament::section
                            heading="Generation error"
                            icon="heroicon-o-exclamation-triangle"
                            icon-color="danger"
                            compact
                        >
                            <p class="text-sm">{{ $report->failure_reason }}</p>
                        </x-filament::section>
                    </div>
                @endif

                @if (! empty($report->files))
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($report->files as $file)
                            <x-filament::button
                                size="sm"
                                color="gray"
                                outlined
                                tag="a"
                                icon="heroicon-o-arrow-down-tray"
                                :href="route('developer.reports.download', ['report' => $report, 'file' => $file['key']])"
                            >
                                Download {{ $file['label'] }} PDF
                            </x-filament::button>
                        @endforeach
                    </div>
                @elseif (in_array($report->status, ['queued', 'processing'], true))
                    <div class="mt-3 flex items-center gap-2 text-sm text-gray-500">
                        <x-filament::loading-indicator />
                        Generating the selected PDF report(s)…
                    </div>
                @endif
            </x-filament::section>
        @empty
            <div class="py-6 text-sm text-gray-500">
                No reports have been requested yet. Select a scope, period, and report type above, then choose Generate selected reports.
            </div>
        @endforelse
    </x-filament::section>
</x-filament-panels::page>
