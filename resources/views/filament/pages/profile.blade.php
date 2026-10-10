<x-filament-panels::page>
    @php($user = $this->getUser())

    <div class="space-y-6">
        <x-filament::section>
            <x-slot name="heading">Account</x-slot>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div><div class="text-sm text-gray-500">Name</div><div class="font-medium">{{ $user->name }}</div></div>
                <div><div class="text-sm text-gray-500">Email</div><div class="font-medium">{{ $user->email }}</div></div>
                <div><div class="text-sm text-gray-500">Date joined</div><div class="font-medium">{{ $user->created_at?->format('Y-m-d H:i:s') }}</div></div>
                <div><div class="text-sm text-gray-500">Base role</div><div class="font-medium">{{ $user->roleEnum()->label() }}</div></div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Teams and effective access</x-slot>

            @forelse ($this->getTeams() as $team)
                <div class="border-b border-gray-200 py-4 last:border-0 dark:border-white/10">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="font-semibold">{{ $team->name }}</div>
                            <div class="text-sm text-gray-500">Effective team role: {{ $this->effectiveRoleForTeam($team->id) ?? 'No access' }}</div>
                        </div>
                        <div class="text-sm text-gray-500">{{ $team->subscriptions->count() }} subscription(s)</div>
                    </div>

                    @if ($team->subscriptions->isNotEmpty())
                        <div class="mt-3 grid gap-2 md:grid-cols-2">
                            @foreach ($team->subscriptions as $subscription)
                                <div class="rounded-lg border border-gray-200 p-3 dark:border-white/10">
                                    <div class="font-medium">{{ $subscription->display_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $subscription->subscription_id }}</div>
                                    <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                        Effective role: {{ $this->effectiveRoleForSubscription($subscription->subscription_id) ?? 'No access' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500">You are not currently assigned to a team.</p>
            @endforelse
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Access exemptions and additional grants</x-slot>
            <x-slot name="description">Active direct or temporary grants are shown here. Your base role is not changed by an access grant.</x-slot>

            @forelse ($this->getGrants() as $grant)
                <div class="border-b border-gray-200 py-4 last:border-0 dark:border-white/10">
                    <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                        <div>
                            <div class="font-medium">{{ $grant->target_name }}</div>
                            <div class="text-sm text-gray-500">{{ $grant->target_type->label() }} · {{ \App\Enums\UserRole::tryFrom($grant->role)?->label() ?? $grant->role }}</div>
                        </div>
                        <div class="text-sm text-gray-500">
                            @if ($grant->expires_at)
                                Expires {{ $grant->expires_at->format('Y-m-d H:i:s') }}
                            @else
                                Permanent
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">No active access exemptions or additional grants.</p>
            @endforelse
        </x-filament::section>
    </div>
</x-filament-panels::page>
