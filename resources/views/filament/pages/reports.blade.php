<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
            Generate selected reports
        </x-filament::button>
    </form>

    {{ $this->table }}
</x-filament-panels::page>
