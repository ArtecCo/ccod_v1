<div class="flex flex-col gap-6">
    {{ $this->filtersForm }}

    <div wire:poll.visible.30s="refreshCharts">
        {{ $this->graphsSchema }}
    </div>

    <x-filament-actions::modals />
</div>
