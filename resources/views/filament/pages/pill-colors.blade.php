<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Pill Colors</x-slot>
            <x-slot name="description">Set the color used by each registered pill throughout the application.</x-slot>

            <div class="overflow-x-auto">
                <table class="fi-ta-table w-full text-start">
                    <thead>
                        <tr>
                            <th class="px-4 py-3 text-start">Pill</th>
                            <th class="px-4 py-3 text-start">Color</th>
                            <th class="px-4 py-3 text-start">Preview</th>
                            <th class="px-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->pillColors() as $pill)
                            <tr class="border-t border-gray-200 dark:border-white/10">
                                <td class="px-4 py-3 font-medium">{{ $pill['label'] }}</td>
                                <td class="px-4 py-3">
                                    <select
                                        wire:model="selectedColors.{{ $pill['key'] }}"
                                        class="fi-select-input block w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm dark:border-white/10 dark:bg-white/5"
                                    >
                                        @foreach ($this->colorOptions() as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <x-filament::badge :color="$selectedColors[$pill['key']] ?? $pill['default']">
                                        {{ $pill['label'] }}
                                    </x-filament::badge>
                                </td>
                                <td class="px-4 py-3 text-end">
                                    <x-filament::button
                                        size="sm"
                                        wire:click="saveColor('{{ $pill['key'] }}')"
                                    >
                                        Save
                                    </x-filament::button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
