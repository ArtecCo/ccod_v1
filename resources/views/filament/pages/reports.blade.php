<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="generate" class="space-y-4">
            {{ $this->form }}
            <div class="flex justify-end">
                <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                    Generate PDFs
                </x-filament::button>
            </div>
        </form>

        <div wire:poll.5s>
        <x-filament::section heading="Your generated reports" description="PDFs remain available for seven days. Only you can access your generated files.">
            @if ($reports->isEmpty())
                <p class="text-sm text-gray-500">No reports have been generated yet.</p>
            @else
                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach ($reports as $report)
                        <div class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="min-w-0">
                                <div class="font-medium">{{ $report->scope_name }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ ucfirst(str_replace('_', ' ', $report->scope_type)) }}
                                    · {{ $report->period_start->format('d M Y') }} – {{ $report->period_end->format('d M Y') }}
                                    · {{ $report->created_at->format('d M Y, H:i') }}
                                </div>
                                @if ($report->failure_reason)
                                    <p class="mt-1 text-sm text-danger-600">{{ $report->failure_reason }}</p>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <x-filament::badge :color="match($report->status) { 'completed' => 'success', 'failed' => 'danger', 'processing' => 'info', default => 'gray' }">
                                    {{ ucfirst($report->status) }}
                                </x-filament::badge>
                                @foreach (($report->files ?? []) as $file)
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
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
