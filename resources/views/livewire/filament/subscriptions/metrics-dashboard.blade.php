<div>
    <x-filament::section>
        {{ $this->filtersForm }}
    </x-filament::section>

    <div class="mt-6">
        {{ $this->graphsSchema }}
    </div>

    <x-filament-actions::modals />
</div>
