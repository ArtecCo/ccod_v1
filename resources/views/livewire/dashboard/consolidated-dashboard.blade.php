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

    <x-filament::tabs label="Dashboard sections">
        @foreach (['overview' => 'Overview', 'cost' => 'Cost', 'alerts' => 'Alerts', 'security' => 'Security', 'resources' => 'Resources'] as $key => $label)
            @if ($visibleTabs->contains($key))
                <x-filament::tabs.item
                    :active="$activeTab === $key"
                    wire:click="setTab('{{ $key }}')"
                >
                    {{ $label }}
                </x-filament::tabs.item>
            @endif
        @endforeach
    </x-filament::tabs>

    <div wire:loading.flex wire:target="filter,setTab" class="text-sm text-gray-500 dark:text-gray-400">
        Updating dashboard…
    </div>

    @if ($activeTab === 'overview')
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Accessible subscriptions</div>
                <div class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ number_format($subscriptionCount) }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Within your access scope</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Projects in scope</div>
                <div class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ number_format($projectCount) }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Matching selected filter</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Active alerts</div>
                <div class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ number_format($alertTotal) }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Across permitted subscriptions</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Resources</div>
                <div class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ number_format($resourceCount) }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Discovered resources</div>
            </x-filament::section>
        </div>

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
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($costTotals as $total)
                <x-filament::section>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Month-to-date cost · {{ $total['currency'] }}</div>
                    <div class="mt-2 text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">{{ number_format($total['amount'], 2) }}</div>
                </x-filament::section>
            @empty
                <x-filament::section>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No cost data is available for this scope.</p>
                </x-filament::section>
            @endforelse
        </div>
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
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-6">
            @foreach (['Sev0', 'Sev1', 'Sev2', 'Sev3', 'Sev4'] as $severity)
                <x-filament::section>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $severity }}</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ number_format((int) ($alertCounts[$severity] ?? 0)) }}</div>
                </x-filament::section>
            @endforeach
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total</div>
                <div class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ number_format($alertTotal) }}</div>
            </x-filament::section>
        </div>
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
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Assessments</div>
                <div class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ number_format((int) ($securityStats?->total ?? 0)) }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Unhealthy</div>
                <div class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ number_format((int) ($securityStats?->unhealthy ?? 0)) }}</div>
            </x-filament::section>
            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">High severity</div>
                <div class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ number_format((int) ($securityStats?->high ?? 0)) }}</div>
            </x-filament::section>
        </div>
        <x-filament::section>
            <p class="text-sm text-gray-500 dark:text-gray-400">Security assessment totals are calculated only from subscriptions in the selected dashboard scope.</p>
        </x-filament::section>
    @elseif ($activeTab === 'resources')
        <x-filament::section>
            <x-slot name="heading">Total discovered resources</x-slot>
            <div class="text-3xl font-semibold text-gray-950 dark:text-white">{{ number_format($resourceCount) }}</div>
        </x-filament::section>
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
