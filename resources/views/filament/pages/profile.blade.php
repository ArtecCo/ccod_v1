<x-filament-panels::page>
    @php
        $user = $this->getUser();
        $teams = $this->getTeams();
        $grants = $this->getGrants();
    @endphp

    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-filament::section compact>
                <x-slot name="heading">Account</x-slot>
                <x-slot name="description">{{ $user->email }}</x-slot>
                <div class="text-base font-semibold">{{ $user->name }}</div>
            </x-filament::section>

            <x-filament::section compact>
                <x-slot name="heading">Base role</x-slot>
                <x-slot name="description">Your base role is not changed by grants.</x-slot>
                <x-filament::badge color="primary">{{ $user->roleEnum()->label() }}</x-filament::badge>
            </x-filament::section>

            <x-filament::section compact>
                <x-slot name="heading">Teams</x-slot>
                <x-slot name="description">Current team memberships</x-slot>
                <div class="text-2xl font-semibold">{{ $teams->count() }}</div>
            </x-filament::section>

            <x-filament::section compact>
                <x-slot name="heading">Active grants</x-slot>
                <x-slot name="description">Additional access exemptions</x-slot>
                <div class="text-2xl font-semibold">{{ $grants->count() }}</div>
            </x-filament::section>
        </div>

        <x-filament::section>
            <x-slot name="heading">Personal information</x-slot>
            <x-slot name="description">Account details associated with your CCOD identity.</x-slot>

            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</div>
                    <div class="mt-1 text-sm font-medium">{{ $user->name }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</div>
                    <div class="mt-1 text-sm font-medium">{{ $user->email }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Date joined</div>
                    <div class="mt-1 text-sm font-medium">{{ $user->created_at?->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Teams and effective access</x-slot>
            <x-slot name="description">Your effective access combines your base role, team membership and active grants.</x-slot>

            <div class="space-y-4">
                @forelse ($teams as $team)
                    <x-filament::section compact>
                        <x-slot name="heading">{{ $team->name }}</x-slot>
                        <x-slot name="description">
                            Effective team role: {{ $this->effectiveRoleForTeam($team->id) ?? 'No access' }}
                        </x-slot>

                        <x-slot name="aside">
                            <x-filament::badge color="gray">
                                {{ $team->subscriptions->count() }} {{ str('subscription')->plural($team->subscriptions->count()) }}
                            </x-filament::badge>
                        </x-slot>

                        @if ($team->subscriptions->isNotEmpty())
                            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                                @foreach ($team->subscriptions as $subscription)
                                    <x-filament::section compact>
                                        <x-slot name="heading">{{ $subscription->display_name }}</x-slot>
                                        <x-slot name="description">{{ $subscription->subscription_id }}</x-slot>
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Effective role</span>
                                            <x-filament::badge color="gray">
                                                {{ $this->effectiveRoleForSubscription($subscription->subscription_id) ?? 'No access' }}
                                            </x-filament::badge>
                                        </div>
                                    </x-filament::section>
                                @endforeach
                            </div>
                        @else
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                No subscriptions are assigned to this team.
                            </div>
                        @endif
                    </x-filament::section>
                @empty
                    <x-filament::section compact>
                        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                            You are not currently assigned to a team.
                        </div>
                    </x-filament::section>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Access exemptions and additional grants</x-slot>
            <x-slot name="description">Active grants are exceptions to your base role and can be permanent or time-bound.</x-slot>

            @if ($grants->isNotEmpty())
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($grants as $grant)
                        <x-filament::section compact>
                            <x-slot name="heading">{{ $grant->target_name }}</x-slot>
                            <x-slot name="description">
                                {{ $grant->target_type->label() }} · {{ \App\Enums\UserRole::tryFrom($grant->role)?->label() ?? $grant->role }}
                            </x-slot>

                            <div class="flex items-center justify-between gap-4">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    @if ($grant->expires_at)
                                        Expires {{ $grant->expires_at->format('d M Y, H:i') }}
                                    @else
                                        No expiry
                                    @endif
                                </span>
                                <x-filament::badge :color="$grant->expires_at ? 'warning' : 'success'">
                                    {{ $grant->expires_at ? 'Time-bound' : 'Permanent' }}
                                </x-filament::badge>
                            </div>
                        </x-filament::section>
                    @endforeach
                </div>
            @else
                <div class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                    No active access exemptions or additional grants.
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
