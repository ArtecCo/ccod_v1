<x-filament::section wire:poll.visible.30s="refreshCharts">
    {{ $this->filtersForm }}

    {{ $this->graphsSchema }}
</x-filament::section>

<x-filament-actions::modals />
