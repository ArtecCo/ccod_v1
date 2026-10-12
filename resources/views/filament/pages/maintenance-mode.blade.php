<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">Application maintenance</x-slot>
        <x-slot name="description">
            Control public access to CCOD while keeping localhost and the Developer panel available for testing.
        </x-slot>

        <div class="flex items-center justify-between gap-6 rounded-lg border border-gray-200 bg-white p-5 dark:border-white/10 dark:bg-white/5">
            <div class="min-w-0">
                <div class="flex items-center gap-3">
                    <span @class([
                        'inline-flex h-2.5 w-2.5 rounded-full',
                        'bg-success-500' => $this->enabled,
                        'bg-gray-400' => ! $this->enabled,
                    ])></span>
                    <span class="text-sm font-semibold text-gray-950 dark:text-white">
                        {{ $this->enabled ? 'Maintenance mode is ON' : 'Maintenance mode is OFF' }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ $this->enabled
                        ? 'Users accessing the public application are shown the maintenance page. Local development access remains available.'
                        : 'The public application is available normally.'
                    }}
                </p>
            </div>

            <div class="shrink-0">
                {{ $this->toggleMaintenanceAction }}
            </div>
        </div>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">Access during maintenance</x-slot>
        <div class="grid gap-3 sm:grid-cols-2">
            <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
                <div class="text-sm font-medium text-gray-950 dark:text-white">Localhost</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Remains accessible for development and testing.</div>
            </div>
            <div class="rounded-lg border border-gray-200 p-4 dark:border-white/10">
                <div class="text-sm font-medium text-gray-950 dark:text-white">Developer panel</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Remains accessible so developers can disable maintenance mode.</div>
            </div>
        </div>
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-panels::page>
