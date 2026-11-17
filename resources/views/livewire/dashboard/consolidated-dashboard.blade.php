<div class="space-y-6">
    <x-filament::section heading="Dashboard Filter" compact>
        <div class="max-w-md">
            <label for="dashboard-scope-filter" class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 text-sm font-medium leading-6 text-gray-950 dark:text-white">
                Scope
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
        <livewire:filament.pages.widgets.consolidated-dashboard-table :filter="$filter" active-tab="overview" :key="'dashboard-table-overview-'.$filter" />
    @elseif ($activeTab === 'cost')
        <livewire:filament.pages.widgets.consolidated-dashboard-stats :filter="$filter" active-tab="cost" :key="'dashboard-stats-cost-'.$filter" />
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <livewire:filament.pages.widgets.consolidated-cost-history-chart :scope-filter="$filter" :key="'dashboard-cost-history-chart-'.$filter" />
            <livewire:filament.pages.widgets.consolidated-cost-history-table :scope-filter="$filter" :key="'dashboard-cost-history-table-'.$filter" />
        </div>
    @elseif ($activeTab === 'alerts')
        <livewire:filament.pages.widgets.consolidated-dashboard-stats :filter="$filter" active-tab="alerts" :key="'dashboard-stats-alerts-'.$filter" />
        <livewire:filament.pages.widgets.consolidated-severity-chart :filter="$filter" active-tab="alerts" :key="'dashboard-alert-chart-'.$filter" />
        <livewire:filament.pages.widgets.consolidated-dashboard-table :filter="$filter" active-tab="alerts" :key="'dashboard-table-alerts-'.$filter" />
    @elseif ($activeTab === 'security')
        <livewire:filament.pages.widgets.consolidated-dashboard-stats :filter="$filter" active-tab="security" :key="'dashboard-stats-security-'.$filter" />
        <livewire:filament.pages.widgets.consolidated-severity-chart :filter="$filter" active-tab="security" :key="'dashboard-security-chart-'.$filter" />
        <livewire:filament.pages.widgets.consolidated-dashboard-table :filter="$filter" active-tab="security_scores" :key="'dashboard-table-security-scores-'.$filter" />
        <livewire:filament.pages.widgets.consolidated-dashboard-table :filter="$filter" active-tab="security_high" :key="'dashboard-table-security-high-'.$filter" />
    @elseif ($activeTab === 'resources')
        <livewire:filament.pages.widgets.consolidated-dashboard-stats :filter="$filter" active-tab="resources" :key="'dashboard-stats-resources-'.$filter" />
        <livewire:filament.pages.widgets.consolidated-dashboard-table :filter="$filter" active-tab="resources" :key="'dashboard-table-resources-'.$filter" />
    @endif
</div>
