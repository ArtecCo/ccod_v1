<div class="space-y-6">
    <x-filament::section>
        <x-slot name="heading">Azure estate dashboard</x-slot>
        <x-slot name="description">Monitor the subscriptions and resources available to your account.</x-slot>

        <div class="max-w-md">
            <label for="dashboard-scope-filter" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                Dashboard scope
            </label>
            <div class="mt-2">
                <x-filament::input.wrapper>
                    <x-filament::input.select id="dashboard-scope-filter" wire:model.live="filter">
                        <option value="all">All projects</option>
                        @if ($filterOptions['businessUnits']->isNotEmpty())
                            <optgroup label="Business units">
                                @foreach ($filterOptions['businessUnits'] as $unit)
                                    <option value="business_unit:{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                        @if ($filterOptions['projects']->isNotEmpty())
                            <optgroup label="Projects">
                                @foreach ($filterOptions['projects'] as $project)
                                    <option value="project:{{ $project->id }}">{{ $project->name }}{{ $project->businessUnit ? ' · '.$project->businessUnit->name : '' }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    </x-filament::input.select>
                </x-filament::input.wrapper>
            </div>
        </div>
    </x-filament::section>

    <div wire:loading.flex wire:target="filter" class="text-sm text-gray-500 dark:text-gray-400">
        Updating dashboard…
    </div>

    @if ($activeTab === 'overview')
        <livewire:filament.pages.widgets.consolidated-dashboard-stats :filter="$filter" active-tab="overview" :key="'dashboard-stats-overview-'.$filter" />

        <x-filament::section heading="Subscriptions in scope">
            @forelse ($subscriptions as $subscription)
                <div class="flex flex-col gap-2 border-t border-gray-200 py-3 first:border-t-0 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-950 dark:text-white">{{ $subscription->display_name }}</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ $subscription->projects->map(fn ($project) => ($project->businessUnit?->name ? $project->businessUnit->name.' / ' : '').$project->name)->implode(', ') }}
                        </div>
                    </div>
                    <x-filament::badge color="gray">{{ $subscription->health_status ?: 'Status unavailable' }}</x-filament::badge>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No subscriptions are available in this dashboard scope.</p>
            @endforelse
        </x-filament::section>
    @elseif ($activeTab === 'cost')
        <livewire:filament.pages.widgets.consolidated-dashboard-stats :filter="$filter" active-tab="cost" :key="'dashboard-stats-cost-'.$filter" />
        <x-filament::section heading="Subscription cost summary">
            @forelse ($costSubscriptions as $subscription)
                <div class="flex items-center justify-between gap-4 border-t border-gray-200 py-3 first:border-t-0 dark:border-white/10">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">{{ $subscription->display_name }}</span>
                    <x-filament::badge color="gray">EUR {{ number_format((float) ($subscription->mtd_spend_eur ?? 0), 2) }}</x-filament::badge>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No subscriptions in scope.</p>
            @endforelse
        </x-filament::section>
    @elseif ($activeTab === 'alerts')
        <livewire:filament.pages.widgets.consolidated-dashboard-stats :filter="$filter" active-tab="alerts" :key="'dashboard-stats-alerts-'.$filter" />
        <x-filament::section heading="Latest alerts">
            @forelse ($recentAlerts as $alert)
                <div class="flex flex-col gap-2 border-t border-gray-200 py-3 first:border-t-0 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-950 dark:text-white">{{ $alert->name ?: 'Azure alert' }}</div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $alert->subscription?->display_name }} · {{ $alert->affected_resource ?: 'Resource unavailable' }}</div>
                    </div>
                    <x-filament::badge color="warning">{{ $alert->severity }} · {{ $alert->fire_time?->format('d M Y, H:i') ?: 'Time unavailable' }}</x-filament::badge>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No alerts found in the selected scope.</p>
            @endforelse
        </x-filament::section>
    @elseif ($activeTab === 'security')
        <livewire:filament.pages.widgets.consolidated-dashboard-stats :filter="$filter" active-tab="security" :key="'dashboard-stats-security-'.$filter" />
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">Security assessment totals are calculated only from subscriptions in the selected dashboard scope.</p>
        </x-filament::section>
    @elseif ($activeTab === 'resources')
        <livewire:filament.pages.widgets.consolidated-dashboard-stats :filter="$filter" active-tab="resources" :key="'dashboard-stats-resources-'.$filter" />
        <x-filament::section heading="Resource types">
            @forelse ($resourcesByType as $resourceType)
                <div class="flex items-center justify-between gap-4 border-t border-gray-200 py-3 first:border-t-0 dark:border-white/10">
                    <span class="text-sm font-medium text-gray-950 dark:text-white">{{ $resourceType->resource_type ?: 'Unclassified' }}</span>
                    <x-filament::badge color="gray">{{ number_format((int) $resourceType->aggregate) }}</x-filament::badge>
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No resource data is available for this scope.</p>
            @endforelse
        </x-filament::section>
    @endif
</div>
