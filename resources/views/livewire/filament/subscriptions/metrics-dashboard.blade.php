<div>
    {{ $this->filtersForm }}

    <div class="mt-6" wire:poll.visible.30s="refreshCharts">
        {{ $this->graphsSchema }}
    </div>

    <x-filament-actions::modals />
</div>
