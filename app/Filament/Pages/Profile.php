<?php

namespace App\Filament\Pages;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;
use App\Models\AzureSubscription;
use App\Models\Team;
use App\Models\User;
use App\Models\UserAccessGrant;
use App\Models\UserSubscriptionAccessOverride;
use App\Services\AccessAuthorizationService;
use App\Services\AccessRequestService;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class Profile extends Page
{
    protected static ?string $navigationLabel = 'Account';
    protected static ?string $title = 'Account';
    protected static ?string $slug = 'profile';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function getUser(): User
    {
        return auth()->guard('web')->user();
    }

    public function getTeams(): Collection
    {
        return $this->getUser()->teams()->with('subscriptions')->orderBy('name')->get();
    }

    public function getSubscriptions(): Collection
    {
        return app(AccessAuthorizationService::class)->accessibleSubscriptions($this->getUser());
    }

    public function getGrants(): Collection
    {
        $user = $this->getUser();

        return UserAccessGrant::query()
            ->where('user_id', $user->getKey())
            ->where('starts_at', '<=', now())
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->where(function ($query) use ($user): void {
                $query->where(function ($subscriptionGrantQuery): void {
                    $subscriptionGrantQuery
                        ->where('target_type', '<>', AccessRequestTargetType::Subscription->value)
                        ->orWhereNull('subscription_id');
                })->orWhere(function ($subscriptionGrantQuery) use ($user): void {
                    $subscriptionGrantQuery
                        ->where('target_type', AccessRequestTargetType::Subscription->value)
                        ->whereNotExists(function ($overrideQuery) use ($user): void {
                            $overrideQuery->selectRaw('1')
                                ->from('user_subscription_access_overrides')
                                ->whereColumn(
                                    'user_subscription_access_overrides.subscription_id',
                                    'user_access_grants.subscription_id',
                                )
                                ->where('user_subscription_access_overrides.user_id', $user->getKey())
                                ->where('user_subscription_access_overrides.override', 'revoked');
                        });
                });
            })
            ->orderByDesc('starts_at')
            ->get();
    }

    public function effectiveRoleForTeam(int $teamId): ?string
    {
        return app(AccessAuthorizationService::class)
            ->effectiveRole($this->getUser(), AccessRequestTargetType::Team, $teamId)
            ?->label();
    }

    public function effectiveRoleForSubscription(string $subscriptionId): ?string
    {
        return app(AccessAuthorizationService::class)
            ->effectiveRole($this->getUser(), AccessRequestTargetType::Subscription, $subscriptionId)
            ?->label();
    }

    public function content(Schema $schema): Schema
    {
        $user = $this->getUser();
        $teams = $this->getTeams();
        $grants = $this->getGrants();
        $accessibleSubscriptions = $this->getSubscriptions();
        $accessibleSubscriptionIds = $accessibleSubscriptions->pluck('subscription_id')->all();

        return $schema->components([
            Section::make('Account overview')
                ->description('Your identity, base role and current access summary.')
                ->schema([
                    Grid::make(4)->schema([
                        TextEntry::make('account_name')->label('Account')->state($user->name),
                        TextEntry::make('base_role')->label('Base role')->state($user->roleEnum()->label())->badge()->color('primary'),
                        TextEntry::make('team_count')->label('Teams')->state((string) $teams->count()),
                        TextEntry::make('grant_count')->label('Active grants')->state((string) $grants->count()),
                    ]),
                ])
                ->columnSpanFull(),

            Section::make('Personal information')
                ->description('Account details associated with your CCOD identity.')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('name')->label('Name')->state($user->name),
                        TextEntry::make('email')->label('Email')->state($user->email),
                        TextEntry::make('joined')->label('Date joined')->state($user->created_at?->format('d M Y, H:i')),
                    ]),
                ])
                ->columnSpanFull(),

            Section::make('Teams and effective access')
                ->description('Effective access combines your base role, team membership and active grants.')
                ->schema($teams->isEmpty()
                    ? [TextEntry::make('no_teams')->hiddenLabel()->state('You are not currently assigned to a team.')]
                    : $teams->flatMap(function (Team $team) use ($accessibleSubscriptionIds): array {
                        $teamRows = [
                            Grid::make(2)->schema([
                                TextEntry::make("team_{$team->id}_name")
                                    ->label('Team')
                                    ->state($team->name),
                                TextEntry::make("team_{$team->id}_role")
                                    ->label('Effective team role')
                                    ->state($this->effectiveRoleForTeam($team->id) ?? 'No access')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                        ];

                        foreach ($team->subscriptions->whereIn('subscription_id', $accessibleSubscriptionIds) as $subscription) {
                            $teamRows[] = Grid::make(2)->schema([
                                TextEntry::make("team_{$team->id}_subscription_{$subscription->subscription_id}")
                                    ->label('Subscription')
                                    ->state($subscription->display_name),
                                TextEntry::make("team_{$team->id}_subscription_role_{$subscription->subscription_id}")
                                    ->label('Effective role')
                                    ->state($this->effectiveRoleForSubscription($subscription->subscription_id) ?? 'No access')
                                    ->badge()
                                    ->color('gray'),
                            ]);
                        }

                        return [
                            Section::make($team->name)
                                ->schema($teamRows)
                                ->collapsible()
                                ->collapsed(),
                        ];
                    })->all())
                ->columnSpanFull(),

            Section::make('Accessible subscriptions')
                ->description('Subscriptions you can currently access. Revoked subscription access is removed from this list immediately.')
                ->schema($accessibleSubscriptions->isEmpty()
                    ? [TextEntry::make('no_subscriptions')->hiddenLabel()->state('You do not currently have access to any Azure subscriptions.')]
                    : $accessibleSubscriptions->map(fn (AzureSubscription $subscription): Grid => Grid::make(3)->schema([
                        TextEntry::make("accessible_subscription_{$subscription->subscription_id}_name")
                            ->label('Subscription')
                            ->state($subscription->display_name),
                        TextEntry::make("accessible_subscription_{$subscription->subscription_id}_id")
                            ->label('Subscription ID')
                            ->state($subscription->subscription_id),
                        TextEntry::make("accessible_subscription_{$subscription->subscription_id}_role")
                            ->label('Effective role')
                            ->state($subscription->access_role?->label() ?? 'No access')
                            ->badge()
                            ->color('gray'),
                    ]))->all())
                ->collapsible()
                ->collapsed()
                ->columnSpanFull(),

            Section::make('Access exemptions and additional grants')
                ->description('Active grants are exceptions to your base role and can be permanent or time-bound.')
                ->schema($grants->isEmpty()
                    ? [TextEntry::make('no_grants')->hiddenLabel()->state('No active access exemptions or additional grants.')]
                    : $grants->flatMap(function (UserAccessGrant $grant): array {
                        return [
                            Grid::make(3)->schema([
                                TextEntry::make("grant_{$grant->id}_target")->label('Target')->state($grant->target_name),
                                TextEntry::make("grant_{$grant->id}_role")->label('Role')->state(UserRole::tryFrom($grant->role)?->label() ?? $grant->role)->badge()->color('gray'),
                                TextEntry::make("grant_{$grant->id}_duration")->label('Duration')->state($grant->expires_at ? 'Time-bound' : 'Permanent')->badge()->color($grant->expires_at ? 'warning' : 'success'),
                            ]),
                        ];
                    })->all())
                ->columnSpanFull(),
        ]);
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('viewRequests')
                ->label('My access requests')
                ->icon('heroicon-o-clipboard-document-list')
                ->url(fn (): string => \App\Filament\Resources\AccessRequests\AccessRequestResource::getUrl('index')),
            Action::make('requestAccess')
                ->label('Request access')
                ->icon('heroicon-o-key')
                ->color('primary')
                ->modalHeading('Request access')
                ->modalDescription('Request a higher level of access to a team or Azure subscription. Your request will be routed to the appropriate approvers.')
                ->form([
                    Select::make('target_type')->label('Access to')->options([
                        AccessRequestTargetType::Team->value => 'Team',
                        AccessRequestTargetType::Subscription->value => 'Azure subscription',
                    ])->default(AccessRequestTargetType::Subscription->value)->live()->required(),
                    Select::make('team_id')->label('Team')->options(fn (): array => $this->getTeams()->pluck('name', 'id')->all())->searchable()->preload()->visible(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Team->value)->required(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Team->value),
                    Select::make('subscription_id')->label('Azure subscription')->options(fn (): array => AzureSubscription::query()->orderBy('display_name')->pluck('display_name', 'subscription_id')->all())->searchable()->preload()->visible(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Subscription->value)->required(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Subscription->value),
                    Select::make('requested_role')->label('Requested role')->options(collect(UserRole::cases())->filter(fn (UserRole $role): bool => $role->isRestricted())->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])->all())->required(),
                    Textarea::make('reason')->label('Reason')->placeholder('Explain why this access is required.')->required()->rows(4)->columnSpanFull(),
                    Select::make('duration')->label('Access duration')->options([
                        AccessRequestDuration::Permanent->value => 'Permanent',
                        AccessRequestDuration::TimeBound->value => 'Time-bound',
                    ])->default(AccessRequestDuration::Permanent->value)->live()->required(),
                    DateTimePicker::make('requested_until')->label('Requested expiry')->minDate(now())->visible(fn ($get): bool => $get('duration') === AccessRequestDuration::TimeBound->value)->required(fn ($get): bool => $get('duration') === AccessRequestDuration::TimeBound->value),
                ])
                ->action(function (array $data): void {
                    $targetType = AccessRequestTargetType::from($data['target_type']);
                    $target = $targetType === AccessRequestTargetType::Team
                        ? Team::query()->findOrFail($data['team_id'])
                        : AzureSubscription::query()->whereKey($data['subscription_id'])->firstOrFail();

                    $requestedUntil = isset($data['requested_until']) && $data['requested_until'] !== '' ? Carbon::parse($data['requested_until']) : null;

                    app(AccessRequestService::class)->create(
                        user: $this->getUser(),
                        targetType: $targetType,
                        target: $target,
                        requestedRole: UserRole::from($data['requested_role']),
                        reason: $data['reason'],
                        duration: AccessRequestDuration::from($data['duration']),
                        requestedUntil: $requestedUntil,
                    );

                    Notification::make()->title('Access request submitted')->body('Your request has been sent to the appropriate approvers.')->success()->send();
                })
                ->modalSubmitActionLabel('Submit request')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
