<x-filament-panels::page>
    @php
        $user = $this->getUser();
        $teams = $this->getTeams();
        $grants = $this->getGrants();
    @endphp

    <div class="ccod-account-page">
        <style>
            .ccod-account-page {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .ccod-account-overview {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 0.75rem;
            }

            .ccod-account-stat {
                min-width: 0;
                padding: 1rem;
                border: 1px solid rgb(229 231 235);
                border-radius: 0.75rem;
                background: rgb(255 255 255);
            }

            .ccod-account-stat-label {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                color: rgb(107 114 128);
                font-size: 0.8125rem;
                line-height: 1.25rem;
                font-weight: 500;
            }

            .ccod-account-stat-value {
                margin-top: 0.35rem;
                color: rgb(17 24 39);
                font-size: 1.125rem;
                line-height: 1.5rem;
                font-weight: 600;
                overflow-wrap: anywhere;
            }

            .ccod-account-stat-meta {
                margin-top: 0.2rem;
                color: rgb(107 114 128);
                font-size: 0.8125rem;
                line-height: 1.25rem;
            }

            .ccod-account-info-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 1rem;
            }

            .ccod-account-field {
                min-width: 0;
            }

            .ccod-account-field-label {
                color: rgb(107 114 128);
                font-size: 0.8125rem;
                line-height: 1.25rem;
                font-weight: 500;
            }

            .ccod-account-field-value {
                margin-top: 0.2rem;
                color: rgb(17 24 39);
                font-size: 0.9375rem;
                line-height: 1.4rem;
                font-weight: 500;
                overflow-wrap: anywhere;
            }

            .ccod-account-list {
                display: flex;
                flex-direction: column;
                gap: 0.75rem;
            }

            .ccod-account-team {
                padding: 1rem;
                border: 1px solid rgb(229 231 235);
                border-radius: 0.75rem;
                background: rgb(249 250 251);
            }

            .ccod-account-team-header,
            .ccod-account-card-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 1rem;
            }

            .ccod-account-team-name,
            .ccod-account-card-title {
                color: rgb(17 24 39);
                font-size: 0.9375rem;
                line-height: 1.4rem;
                font-weight: 600;
            }

            .ccod-account-muted {
                margin-top: 0.2rem;
                color: rgb(107 114 128);
                font-size: 0.8125rem;
                line-height: 1.25rem;
            }

            .ccod-account-subscriptions {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 0.65rem;
                margin-top: 0.75rem;
            }

            .ccod-account-subscription,
            .ccod-account-grant {
                min-width: 0;
                padding: 0.85rem;
                border: 1px solid rgb(229 231 235);
                border-radius: 0.65rem;
                background: rgb(255 255 255);
            }

            .ccod-account-subscription-name {
                color: rgb(17 24 39);
                font-size: 0.875rem;
                line-height: 1.25rem;
                font-weight: 500;
                overflow-wrap: anywhere;
            }

            .ccod-account-subscription-id {
                margin-top: 0.15rem;
                color: rgb(107 114 128);
                font-size: 0.7rem;
                line-height: 1rem;
                overflow-wrap: anywhere;
            }

            .ccod-account-role {
                margin-top: 0.55rem;
                color: rgb(75 85 99);
                font-size: 0.75rem;
                line-height: 1.1rem;
            }

            .ccod-account-empty {
                padding: 1.5rem;
                border: 1px dashed rgb(209 213 219);
                border-radius: 0.75rem;
                text-align: center;
            }

            .ccod-account-empty-icon {
                width: 1.75rem;
                height: 1.75rem;
                margin: 0 auto;
                color: rgb(156 163 175);
            }

            .ccod-account-empty-title {
                margin-top: 0.5rem;
                color: rgb(17 24 39);
                font-size: 0.875rem;
                font-weight: 600;
            }

            .ccod-account-empty-text {
                margin-top: 0.2rem;
                color: rgb(107 114 128);
                font-size: 0.8125rem;
            }

            .dark .ccod-account-stat,
            .dark .ccod-account-subscription,
            .dark .ccod-account-grant {
                border-color: rgb(255 255 255 / 0.1);
                background: rgb(255 255 255 / 0.025);
            }

            .dark .ccod-account-team {
                border-color: rgb(255 255 255 / 0.1);
                background: rgb(255 255 255 / 0.025);
            }

            .dark .ccod-account-stat-value,
            .dark .ccod-account-field-value,
            .dark .ccod-account-team-name,
            .dark .ccod-account-card-title,
            .dark .ccod-account-subscription-name,
            .dark .ccod-account-empty-title {
                color: rgb(243 244 246);
            }

            .dark .ccod-account-stat-label,
            .dark .ccod-account-stat-meta,
            .dark .ccod-account-field-label,
            .dark .ccod-account-muted,
            .dark .ccod-account-subscription-id,
            .dark .ccod-account-role,
            .dark .ccod-account-empty-text {
                color: rgb(156 163 175);
            }

            .dark .ccod-account-empty {
                border-color: rgb(255 255 255 / 0.12);
            }

            @media (max-width: 1100px) {
                .ccod-account-overview,
                .ccod-account-subscriptions {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media (max-width: 700px) {
                .ccod-account-overview,
                .ccod-account-info-grid,
                .ccod-account-subscriptions {
                    grid-template-columns: minmax(0, 1fr);
                }
            }
        </style>

        <x-filament::section>
            <x-slot name="heading">Account overview</x-slot>
            <x-slot name="description">Your identity, base role and current access summary.</x-slot>

            <div class="ccod-account-overview">
                <div class="ccod-account-stat">
                    <div class="ccod-account-stat-label">
                        <x-filament::icon icon="heroicon-o-user" style="width: 1rem; height: 1rem;" />
                        Account
                    </div>
                    <div class="ccod-account-stat-value">{{ $user->name }}</div>
                    <div class="ccod-account-stat-meta">{{ $user->email }}</div>
                </div>

                <div class="ccod-account-stat">
                    <div class="ccod-account-stat-label">
                        <x-filament::icon icon="heroicon-o-shield-check" style="width: 1rem; height: 1rem;" />
                        Base role
                    </div>
                    <div class="ccod-account-stat-value">{{ $user->roleEnum()->label() }}</div>
                    <div class="ccod-account-stat-meta">Base role is unchanged by grants.</div>
                </div>

                <div class="ccod-account-stat">
                    <div class="ccod-account-stat-label">
                        <x-filament::icon icon="heroicon-o-user-group" style="width: 1rem; height: 1rem;" />
                        Teams
                    </div>
                    <div class="ccod-account-stat-value">{{ $teams->count() }}</div>
                    <div class="ccod-account-stat-meta">Current team memberships</div>
                </div>

                <div class="ccod-account-stat">
                    <div class="ccod-account-stat-label">
                        <x-filament::icon icon="heroicon-o-key" style="width: 1rem; height: 1rem;" />
                        Active grants
                    </div>
                    <div class="ccod-account-stat-value">{{ $grants->count() }}</div>
                    <div class="ccod-account-stat-meta">Additional access exemptions</div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Personal information</x-slot>
            <x-slot name="description">Account details associated with your CCOD identity.</x-slot>

            <div class="ccod-account-info-grid">
                <div class="ccod-account-field">
                    <div class="ccod-account-field-label">Name</div>
                    <div class="ccod-account-field-value">{{ $user->name }}</div>
                </div>
                <div class="ccod-account-field">
                    <div class="ccod-account-field-label">Email</div>
                    <div class="ccod-account-field-value">{{ $user->email }}</div>
                </div>
                <div class="ccod-account-field">
                    <div class="ccod-account-field-label">Date joined</div>
                    <div class="ccod-account-field-value">{{ $user->created_at?->format('d M Y, H:i') }}</div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Teams and effective access</x-slot>
            <x-slot name="description">Effective access combines your base role, team membership and active grants.</x-slot>

            <div class="ccod-account-list">
                @forelse ($teams as $team)
                    <div class="ccod-account-team">
                        <div class="ccod-account-team-header">
                            <div>
                                <div class="ccod-account-team-name">{{ $team->name }}</div>
                                <div class="ccod-account-muted">Effective team role: {{ $this->effectiveRoleForTeam($team->id) ?? 'No access' }}</div>
                            </div>
                            <x-filament::badge color="gray">
                                {{ $team->subscriptions->count() }} {{ str('subscription')->plural($team->subscriptions->count()) }}
                            </x-filament::badge>
                        </div>

                        @if ($team->subscriptions->isNotEmpty())
                            <div class="ccod-account-subscriptions">
                                @foreach ($team->subscriptions as $subscription)
                                    <div class="ccod-account-subscription">
                                        <div class="ccod-account-subscription-name">{{ $subscription->display_name }}</div>
                                        <div class="ccod-account-subscription-id">{{ $subscription->subscription_id }}</div>
                                        <div class="ccod-account-role">
                                            Effective role:
                                            <strong>{{ $this->effectiveRoleForSubscription($subscription->subscription_id) ?? 'No access' }}</strong>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="ccod-account-muted" style="margin-top: 0.75rem;">No subscriptions are assigned to this team.</div>
                        @endif
                    </div>
                @empty
                    <div class="ccod-account-empty">
                        <x-filament::icon icon="heroicon-o-user-group" class="ccod-account-empty-icon" />
                        <div class="ccod-account-empty-title">No team memberships</div>
                        <div class="ccod-account-empty-text">You are not currently assigned to a team.</div>
                    </div>
                @endforelse
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">Access exemptions and additional grants</x-slot>
            <x-slot name="description">Active grants are exceptions to your base role and can be permanent or time-bound.</x-slot>

            @if ($grants->isNotEmpty())
                <div class="ccod-account-subscriptions">
                    @foreach ($grants as $grant)
                        <div class="ccod-account-grant">
                            <div class="ccod-account-card-header">
                                <div>
                                    <div class="ccod-account-card-title">{{ $grant->target_name }}</div>
                                    <div class="ccod-account-muted">
                                        {{ $grant->target_type->label() }} · {{ \App\Enums\UserRole::tryFrom($grant->role)?->label() ?? $grant->role }}
                                    </div>
                                </div>
                                <x-filament::badge :color="$grant->expires_at ? 'warning' : 'success'">
                                    {{ $grant->expires_at ? 'Time-bound' : 'Permanent' }}
                                </x-filament::badge>
                            </div>
                            <div class="ccod-account-muted" style="margin-top: 0.65rem;">
                                @if ($grant->expires_at)
                                    Expires {{ $grant->expires_at->format('d M Y, H:i') }}
                                @else
                                    No expiry
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="ccod-account-empty">
                    <x-filament::icon icon="heroicon-o-key" class="ccod-account-empty-icon" />
                    <div class="ccod-account-empty-title">No active access exemptions</div>
                    <div class="ccod-account-empty-text">Additional direct or temporary grants will appear here.</div>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
