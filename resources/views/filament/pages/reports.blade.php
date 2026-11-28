<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <div>
            <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                Generate PDF
            </x-filament::button>
        </div>
    </form>

    <x-filament::section
        heading="Your generated reports"
        description="Processing status and completed downloads appear here. Reports remain available for seven days."
        wire:poll.5s
    >
        @if ($reports->isEmpty())
            <x-filament::empty-state
                icon="heroicon-o-document-chart-bar"
                heading="No reports generated yet"
                description="Your generated PDF reports will appear here."
            />
        @else
            <div>
                @foreach ($reports as $report)
                    <x-filament::section
                        :heading="$report->scope_name"
                        :description="ucfirst(str_replace('_', ' ', $report->scope_type)) . ' · ' . $report->period_start->format('d M Y') . ' – ' . $report->period_end->format('d M Y') . ' · Requested ' . $report->created_at->format('d M Y, H:i')"
                        compact
                    >
                        <x-filament::badge :color="match ($report->status) {
                            'completed' => 'success',
                            'failed' => 'danger',
                            'processing' => 'info',
                            default => 'gray',
                        }">
                            {{ ucfirst($report->status) }}
                        </x-filament::badge>

                        @if ($report->failure_reason)
                            <x-filament::section>
                                {{ $report->failure_reason }}
                            </x-filament::section>
                        @endif

                        @if (! empty($report->files))
                            <div>
                                @foreach ($report->files as $file)
                                    <x-filament::button
                                        size="sm"
                                        color="gray"
                                        outlined
                                        tag="a"
                                        :href="route('developer.reports.download', ['report' => $report, 'file' => $file['key']])"
                                    >
                                        {{ $file['label'] }}
                                    </x-filament::button>
                                @endforeach
                            </div>
                        @elseif (in_array($report->status, ['queued', 'processing'], true))
                            <x-filament::loading-indicator />
                            Preparing PDF files…
                        @endif
                    </x-filament::section>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
