<x-filament::section wire:poll.visible.30s="refreshCharts">
    {{ $this->addGraphAction }}

    {{ $this->filtersForm }}

    {{ $this->graphsSchema }}
</x-filament::section>

<x-filament-actions::modals />
