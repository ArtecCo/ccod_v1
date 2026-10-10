<?php

namespace App\Filament\Pages;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;
use App\Models\AzureSubscription;
use App\Models\Team;
use App\Models\User;
use App\Models\UserAccessGrant;
use App\Services\AccessAuthorizationService;
use App\Services\AccessRequestService;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Carbon;

class Profile extends Page
{
    protected static ?string $navigationLabel = 'Account';
    protected static ?string $title = 'Account';
    protected static ?string $slug = 'profile';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';

    protected string $view = 'filament.pages.profile';

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
        return AzureSubscription::query()->orderBy('display_name')->get();
    }

    public function getGrants(): Collection
    {
        return UserAccessGrant::query()
            ->where('user_id', $this->getUser()->getKey())
            ->where('starts_at', '<=', now())
            ->where(function ($query): void {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
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

    public function getHeaderActions(): array
    {
        return [
            Action::make('requestAccess')
                ->label('Request access')
                ->icon('heroicon-o-key')
                ->color('primary')
                ->modalHeading('Request access')
                ->modalDescription('Request a higher level of access to a team or Azure subscription. Your request will be routed to the appropriate approvers.')
                ->form([
                    Select::make('target_type')
                        ->label('Access to')
                        ->options([
                            AccessRequestTargetType::Team->value => 'Team',
                            AccessRequestTargetType::Subscription->value => 'Azure subscription',
                        ])
                        ->default(AccessRequestTargetType::Subscription->value)
                        ->live()
                        ->required(),
                    Select::make('team_id')
                        ->label('Team')
                        ->options(fn (): array => $this->getTeams()->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Team->value)
                        ->required(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Team->value),
                    Select::make('subscription_id')
                        ->label('Azure subscription')
                        ->options(fn (): array => $this->getSubscriptions()->pluck('display_name', 'subscription_id')->all())
                        ->searchable()
                        ->preload()
                        ->visible(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Subscription->value)
                        ->required(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Subscription->value),
                    Select::make('requested_role')
                        ->label('Requested role')
                        ->options(collect(UserRole::cases())
                            ->filter(fn (UserRole $role): bool => $role->isRestricted())
                            ->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])
                            ->all())
                        ->required(),
                    Textarea::make('reason')
                        ->label('Reason')
                        ->placeholder('Explain why this access is required.')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                    Select::make('duration')
                        ->label('Access duration')
                        ->options([
                            AccessRequestDuration::Permanent->value => 'Permanent',
                            AccessRequestDuration::TimeBound->value => 'Time-bound',
                        ])
                        ->default(AccessRequestDuration::Permanent->value)
                        ->live()
                        ->required(),
                    DateTimePicker::make('requested_until')
                        ->label('Requested expiry')
                        ->minDate(now())
                        ->visible(fn ($get): bool => $get('duration') === AccessRequestDuration::TimeBound->value)
                        ->required(fn ($get): bool => $get('duration') === AccessRequestDuration::TimeBound->value),
                ])
                ->action(function (array $data): void {
                    $targetType = AccessRequestTargetType::from($data['target_type']);
                    $target = $targetType === AccessRequestTargetType::Team
                        ? Team::query()->findOrFail($data['team_id'])
                        : AzureSubscription::query()->whereKey($data['subscription_id'])->firstOrFail();

                    $requestedUntil = isset($data['requested_until']) && $data['requested_until'] !== ''
                        ? Carbon::parse($data['requested_until'])
                        : null;

                    app(AccessRequestService::class)->create(
                        user: $this->getUser(),
                        targetType: $targetType,
                        target: $target,
                        requestedRole: UserRole::from($data['requested_role']),
                        reason: $data['reason'],
                        duration: AccessRequestDuration::from($data['duration']),
                        requestedUntil: $requestedUntil,
                    );

                    Notification::make()
                        ->title('Access request submitted')
                        ->body('Your request has been sent to the appropriate approvers.')
                        ->success()
                        ->send();
                })
                ->modalSubmitActionLabel('Submit request')
                ->modalCancelActionLabel('Cancel'),
        ];
    }
}
