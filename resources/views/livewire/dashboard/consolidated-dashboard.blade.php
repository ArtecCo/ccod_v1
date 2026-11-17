<div class="space-y-5">
    <div class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-base font-semibold text-gray-950 dark:text-white">Azure estate dashboard</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">A consolidated view of the subscriptions available to your account.</p>
        </div>
        <div class="w-full sm:max-w-xs">
            <label for="dashboard-scope-filter" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-300">Dashboard scope</label>
            <select id="dashboard-scope-filter" wire:model.live="filter" class="w-full rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
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
            </select>
        </div>
    </div>

    <div class="flex flex-wrap gap-2 border-b border-gray-200 pb-2 dark:border-gray-700" role="tablist" aria-label="Dashboard sections">
        @foreach (['overview' => 'Overview', 'cost' => 'Cost', 'alerts' => 'Alerts', 'security' => 'Security', 'resources' => 'Resources'] as $key => $label)
            <button type="button" wire:click="setTab('{{ $key }}'" role="tab" aria-selected="{{ $activeTab === $key ? 'true' : 'false' }}"
                class="rounded-lg px-3 py-2 text-sm font-medium transition {{ $activeTab === $key ? 'bg-primary-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div wire:loading.flex wire:target="filter,setTab" class="items-center gap-2 text-sm text-gray-500">
        <x-filament::loading-indicator class="h-4 w-4" /> Updating dashboard…
    </div>

    @if ($activeTab === 'overview')
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([['Accessible subscriptions', $subscriptionCount, 'heroicon-o-cloud'], ['Projects in scope', $projectCount, 'heroicon-o-rectangle-stack'], ['Active alerts', $alertTotal, 'heroicon-o-bell-alert'], ['Resources', $resourceCount, 'heroicon-o-cube']] as [$label, $value, $icon])
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400"><span>{{ $label }}</span><x-filament::icon :icon="$icon" class="h-5 w-5" /></div>
                    <div class="mt-3 text-2xl font-semibold text-gray-950 dark:text-white">{{ number_format((int) $value) }}</div>
                </div>
            @endforeach
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <h3 class="font-semibold text-gray-950 dark:text-white">Accessible subscriptions</h3>
            @if ($subscriptions->isEmpty())
                <p class="mt-3 text-sm text-gray-500">No subscriptions are available in this dashboard scope.</p>
            @else
                <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach ($subscriptions as $subscription)
                        <div class="flex flex-col gap-1 py-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->display_name }}</div>
                                <div class="text-xs text-gray-500">{{ $subscription->projects->map(fn ($project) => ($project->businessUnit?->name ? $project->businessUnit->name.' / ' : '').$project->name)->implode(', ') }}</div>
                            </div>
                            <span class="text-xs text-gray-500">{{ $subscription->health_status ?: 'Status unavailable' }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @elseif ($activeTab === 'cost')
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($costTotals as $total)
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="text-sm text-gray-500 dark:text-gray-400">Month-to-date cost · {{ $total['currency'] }}</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ number_format($total['amount'], 2) }}</div>
                </div>
            @empty
                <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900">No cost data is available for the selected scope.</div>
            @endforelse
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <h3 class="font-semibold text-gray-950 dark:text-white">Subscription cost summary</h3>
            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($subscriptions as $subscription)
                    <div class="flex justify-between gap-4 py-3 text-sm">
                        <span class="text-gray-700 dark:text-gray-300">{{ $subscription->display_name }}</span>
                        <span class="font-medium text-gray-950 dark:text-white">EUR {{ number_format((float) ($subscription->mtd_spend_eur ?? 0), 2) }}</span>
                    </div>
                @empty
                    <p class="py-3 text-sm text-gray-500">No subscriptions in scope.</p>
                @endforelse
            </div>
        </div>
    @elseif ($activeTab === 'alerts')
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6">
            @foreach (['Sev0', 'Sev1', 'Sev2', 'Sev3', 'Sev4'] as $severity)
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $severity }}</div>
                    <div class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ number_format((int) ($alertCounts[$severity] ?? 0)) }}</div>
                </div>
            @endforeach
            <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total</div>
                <div class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ number_format($alertTotal) }}</div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <h3 class="font-semibold text-gray-950 dark:text-white">Latest alerts</h3>
            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($recentAlerts as $alert)
                    <div class="flex flex-col gap-1 py-3 sm:flex-row sm:items-center sm:justify-between">
                        <div><div class="text-sm font-medium text-gray-900 dark:text-white">{{ $alert->name ?: 'Azure alert' }}</div><div class="text-xs text-gray-500">{{ $alert->subscription?->display_name }} · {{ $alert->affected_resource ?: 'Resource unavailable' }}</div></div>
                        <div class="text-xs text-gray-500">{{ $alert->severity }} · {{ $alert->fire_time?->format('d M Y, H:i') ?: 'Time unavailable' }}</div>
                    </div>
                @empty
                    <p class="py-3 text-sm text-gray-500">No alerts found in the selected scope.</p>
                @endforelse
            </div>
        </div>
    @elseif ($activeTab === 'security')
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            @foreach ([['Assessments', (int) ($securityStats?->total ?? 0)], ['Unhealthy', (int) ($securityStats?->unhealthy ?? 0)], ['High severity', (int) ($securityStats?->high ?? 0)]] as [$label, $value])
                <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900"><div class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</div><div class="mt-2 text-2xl font-semibold text-gray-950 dark:text-white">{{ number_format($value) }}</div></div>
            @endforeach
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 text-sm text-gray-500 dark:border-gray-700 dark:bg-gray-900">Security assessment totals are calculated only from subscriptions in the selected dashboard scope.</div>
    @elseif ($activeTab === 'resources')
        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total discovered resources</div>
            <div class="mt-2 text-3xl font-semibold text-gray-950 dark:text-white">{{ number_format($resourceCount) }}</div>
            <h3 class="mt-5 font-semibold text-gray-950 dark:text-white">Resource types</h3>
            <div class="mt-3 divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($resourcesByType as $resourceType)
                    <div class="flex justify-between gap-4 py-3 text-sm"><span class="text-gray-700 dark:text-gray-300">{{ $resourceType->resource_type ?: 'Unclassified' }}</span><span class="font-medium text-gray-950 dark:text-white">{{ number_format((int) $resourceType->aggregate) }}</span></div>
                @empty
                    <p class="py-3 text-sm text-gray-500">No resource data is available for the selected scope.</p>
                @endforelse
            </div>
        </div>
    @endif
</div>
