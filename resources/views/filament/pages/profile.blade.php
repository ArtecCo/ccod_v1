<x-filament-panels::page>
    @php($user = $this->getUser())

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-filament::section>
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-primary-50 text-primary-600 dark:bg-primary-500/10 dark:text-primary-400">
                        <x-filament::icon icon="heroicon-o-user" class="h-5 w-5" />
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Account</div>
                        <div class="text-lg font-semibold">{{ $user->name }}</div>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Base role</div>
                <div class="mt-1 text-lg font-semibold">{{ $user->roleEnum()->label() }}</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your base role is unchanged by access grants.</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Teams</div>
                <div class="mt-1 text-2xl font-semibold">{{ $this->getTeams()->count() }}</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Team memberships</div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-sm text-gray-500 dark:text-gray-400">Active grants</div>
                <div class="mt-1 text-2xl font-semibold">{{ $this->getGrants()->count() }}</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Additional access exemptions</div>
            </x-filament::section>
        </div>

        <x-filament::section>
            <x-slot name="heading">Personal information</x-slot>
            <div class="grid gap-5 md:grid-cols-3">
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Name</div>
                    <div class="mt-1 font-medium">{{ $user->name }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Email</div>
                    <div class="mt-1 font-medium">{{ $user->email }}</div>
                </div>
                <div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Date joined</div>
                    <div class="mt-1 font-medium">{{ $user->created_at?->format('Y-m-d H:i:s') }}</div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Teams and effective access</x-slot>
            <x-slot name="description">Your effective access combines your base role, team memberships and active access grants.</x-slot>

            <div class="space-y-4">
                @forelse ($this->getTeams() as $team)
                    <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-5 dark:border-white/10 dark:bg-white/[0.02]">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="text-base font-semibold">{{ $team->name }}</div>
                                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Effective team role: {{ $this->effectiveRoleForTeam($team->id) ?? 'No access' }}
                                </div>
                            </div>
                            <x-filament::badge color="gray">
                                {{ $team->subscriptions->count() }} {{ str('subscription')->plural($team->subscriptions->count()) }}
                            </x-filament::badge>
                        </div>

                        @if ($team->subscriptions->isNotEmpty())
                            <div class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                @foreach ($team->subscriptions as $subscription)
                                    <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-white/10 dark:bg-white/[0.03]">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="truncate font-medium">{{ $subscription->display_name }}</div>
                                                <div class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">{{ $subscription->subscription_id }}</div>
                                            </div>
                                            <x-filament::icon icon="heroicon-o-cloud" class="h-5 w-5 shrink-0 text-gray-400" />
                                        </div>
                                        <div class="mt-3 text-sm">
                                            <span class="text-gray-500 dark:text-gray-400">Effective role:</span>
                                            <span class="font-medium">{{ $this->effectiveRoleForSubscription($subscription->subscription_id) ?? 'No access' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">This team has no subscriptions assigned.</div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-white/10">
                        <x-filament::icon icon="heroicon-o-user-group" class="mx-auto h-8 w-8 text-gray-400" />
                        <div class="mt-3 font-medium">No team memberships</div>
                        <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">You are not currently assigned to a team.</div>
                    </div>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Access exemptions and additional grants</x-slot>
            <x-slot name="description">These grants are exceptions to your base role and may be permanent or time-bound.</x-slot>

            <div class="grid gap-3 md:grid-cols-2">
                @forelse ($this->getGrants() as $grant)
                    <div class="rounded-xl border border-gray-200 p-4 dark:border-white/10">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="font-semibold">{{ $grant->target_name }}</div>
                                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $grant->target_type->label() }} · {{ \App\Enums\UserRole::tryFrom($grant->role)?->label() ?? $grant->role }}
                                </div>
                            </div>
                            <x-filament::badge :color="$grant->expires_at ? 'warning' : 'success'">
                                {{ $grant->expires_at ? 'Time-bound' : 'Permanent' }}
                            </x-filament::badge>
                        </div>
                        <div class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                            @if ($grant->expires_at)
                                Expires {{ $grant->expires_at->format('Y-m-d H:i:s') }}
                            @else
                                No expiry
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="md:col-span-2 rounded-xl border border-dashed border-gray-300 p-8 text-center dark:border-white/10">
                        <x-filament::icon icon="heroicon-o-key" class="mx-auto h-8 w-8 text-gray-400" />
                        <div class="mt-3 font-medium">No active access exemptions</div>
                        <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Additional direct or temporary grants will appear here.</div>
                    </div>
                @endforelse
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
