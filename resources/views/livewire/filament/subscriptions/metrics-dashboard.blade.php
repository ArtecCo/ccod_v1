<x-filament::section>
    <x-slot name="heading">Resource Metrics</x-slot>
    <x-slot name="description">
        Azure Monitor metrics for resources in this subscription. All timestamps are displayed in India Standard Time.
    </x-slot>

    {{ $this->filtersForm }}

    <div class="flex flex-wrap items-center gap-2">
        <x-filament::button
            color="gray"
            icon="heroicon-o-arrow-path"
            wire:click="refreshCharts"
            wire:loading.attr="disabled"
        >
            Refresh
        </x-filament::button>

        {{ $this->addGraphAction }}
    </div>
</x-filament::section>

@if ($graphs->isEmpty())
    <x-filament::section>
        <x-slot name="heading">No metric graphs configured</x-slot>
        <x-slot name="description">
            @if ($this->addGraphAction->isVisible())
                Add a graph to monitor one or more metrics from an Azure resource.
            @else
                No metric graphs have been configured for this subscription.
            @endif
        </x-slot>
    </x-filament::section>
@else
    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        @foreach ($graphs as $graph)
            <x-filament::section :key="'metric-graph-'.$graph->id">
                <x-slot name="heading">{{ $graph->title }}</x-slot>
                <x-slot name="description">
                    {{ $graph->resource?->name }} · {{ $graph->resource?->resource_type }}
                </x-slot>

                @livewire(
                    \App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionMetricGraphChart::class,
                    [
                        'record' => $record,
                        'graphId' => $graph->id,
                        'start' => $range['start'],
                        'end' => $range['end'],
                        'interval' => $range['interval'],
                    ],
                    key('metric-chart-'.$graph->id.'-'.sha1(json_encode($range)).'-'.$refreshVersion)
                )

                @if ($this->addGraphAction->isVisible())
                    <div class="mt-3 flex justify-end">
                        <x-filament::button
                            color="danger"
                            size="sm"
                            icon="heroicon-o-trash"
                            wire:click="deleteGraph({{ $graph->id }})"
                            wire:confirm="Remove this graph for everyone who can view this subscription?"
                        >
                            Remove graph
                        </x-filament::button>
                    </div>
                @endif
            </x-filament::section>
        @endforeach
    </div>
@endif

<x-filament-actions::modals />
