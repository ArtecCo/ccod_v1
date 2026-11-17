<div>
    <x-filament::section heading="Dashboard Filter" compact class="!shadow-none">
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
    <div wire:loading.flex wire:target="filter" class="text-sm text-gray-500 dark:text-gray-400">Updating dashboard…</div>
</div>