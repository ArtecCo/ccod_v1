<?php

namespace App\Filament\Resources\AccessRequests;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;
use App\Filament\Resources\AccessRequests\Pages\CreateAccessRequest;
use App\Filament\Resources\AccessRequests\Pages\ListAccessRequests;
use App\Filament\Resources\AccessRequests\Pages\ViewAccessRequest;
use App\Models\AccessRequest;
use App\Models\Team;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccessRequestResource extends Resource
{
    protected static ?string $model = AccessRequest::class;
    protected static ?string $navigationLabel = 'Access Requests';
    protected static ?string $modelLabel = 'Access Request';
    protected static ?string $pluralModelLabel = 'Access Requests';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 40;
    protected static ?string $slug = 'access-requests';

    public static function canViewAny(): bool
    {
        return (auth()->guard('developers')->check() && auth()->guard('developers')->user()?->is_active === true)
            || (auth()->guard('web')->check() && auth()->guard('web')->user()?->is_active === true);
    }

    public static function canCreate(): bool
    {
        $user = auth()->guard('web')->user();

        return $user instanceof User && $user->is_active && ! $user->isGlobal();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->guard('developers')->check()) {
            return $query;
        }

        $user = auth()->guard('web')->user();

        if (! $user) {
            return $query->whereKey(-1);
        }

        if ($user->isGlobalOwner()) {
            return $query;
        }

        if ($user->roleEnum() === UserRole::RestrictedOwner) {
            $teamIds = $user->teams()->pluck('teams.id');

            return $query->where(function (Builder $builder) use ($user, $teamIds): void {
                $builder->where('user_id', $user->getKey())
                    ->orWhere(function (Builder $approvals) use ($teamIds): void {
                        $approvals->where('target_type', AccessRequestTargetType::Team->value)
                            ->whereIn('team_id', $teamIds)
                            ->orWhere(function (Builder $target) use ($teamIds): void {
                                $target->where('target_type', AccessRequestTargetType::Subscription->value)
                                    ->whereIn('subscription_id', function ($subscriptionQuery) use ($teamIds): void {
                                        $subscriptionQuery->select('team_subscription.subscription_id')
                                            ->from('team_subscription')
                                            ->whereIn('team_subscription.team_id', $teamIds);
                                    });
                            });
                    });
            });
        }

        return $query->where('user_id', $user->getKey());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Access request')
                ->schema([
                    \Filament\Forms\Components\Select::make('target_type')
                        ->label('Request access to')
                        ->options([
                            AccessRequestTargetType::Team->value => 'Team',
                            AccessRequestTargetType::Subscription->value => 'Subscription',
                        ])
                        ->default(AccessRequestTargetType::Subscription->value)
                        ->live()
                        ->required(),
                    \Filament\Forms\Components\Select::make('team_id')
                        ->label('Team')
                        ->options(fn (): array => Team::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->visible(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Team->value)
                        ->required(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Team->value),
                    \Filament\Forms\Components\Select::make('subscription_id')
                        ->label('Subscription')
                        ->options(fn (): array => \App\Models\AzureSubscription::query()->orderBy('display_name')->pluck('display_name', 'subscription_id')->all())
                        ->searchable()
                        ->visible(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Subscription->value)
                        ->required(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Subscription->value),
                    \Filament\Forms\Components\Select::make('requested_role')
                        ->label('Requested role')
                        ->options([
                            UserRole::RestrictedReader->value => UserRole::RestrictedReader->label(),
                            UserRole::RestrictedContributor->value => UserRole::RestrictedContributor->label(),
                            UserRole::RestrictedOwner->value => UserRole::RestrictedOwner->label(),
                        ])
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->required()
                        ->minLength(10)
                        ->maxLength(2000)
                        ->rows(4)
                        ->columnSpanFull(),
                    \Filament\Forms\Components\Select::make('duration')
                        ->options([
                            AccessRequestDuration::Permanent->value => 'Permanent',
                            AccessRequestDuration::TimeBound->value => 'Time-bound',
                        ])
                        ->default(AccessRequestDuration::Permanent->value)
                        ->live()
                        ->required(),
                    \Filament\Forms\Components\DateTimePicker::make('requested_until')
                        ->label('Requested expiry')
                        ->minDate(now())
                        ->visible(fn ($get): bool => $get('duration') === AccessRequestDuration::TimeBound->value)
                        ->required(fn ($get): bool => $get('duration') === AccessRequestDuration::TimeBound->value),
                ])->columns(2),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request')
                ->schema([
                    TextEntry::make('user.name')->label('Requester'),
                    TextEntry::make('user.email')->label('Email'),
                    TextEntry::make('target_name')->label('Target'),
                    TextEntry::make('target_type')->label('Target type')->badge(),
                    TextEntry::make('requested_role')->label('Requested role')->formatStateUsing(fn (string $state): string => UserRole::tryFrom($state)?->label() ?? $state),
                    TextEntry::make('duration')->label('Duration')->formatStateUsing(fn (string $state): string => AccessRequestDuration::tryFrom($state)?->label() ?? $state),
                    TextEntry::make('requested_until')->label('Requested expiry')->dateTime('Y-m-d H:i:s')->placeholder('No expiry'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('reason')->columnSpanFull(),
                ])->columns(2),
            Section::make('Decision')
                ->schema([
                    TextEntry::make('decided_at')->label('Decided at')->dateTime('Y-m-d H:i:s')->placeholder('Pending'),
                    TextEntry::make('decision_reason')->label('Decision reason')->placeholder('No reason provided')->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('user.name')->label('Requester')->searchable()->sortable(),
                TextColumn::make('target_name')->label('Target')->searchable()->sortable(),
                TextColumn::make('target_type')->label('Type')->badge(),
                TextColumn::make('requested_role')->label('Requested role')
                    ->formatStateUsing(fn (string $state): string => UserRole::tryFrom($state)?->label() ?? $state),
                TextColumn::make('duration')->label('Duration')
                    ->formatStateUsing(fn (string $state): string => AccessRequestDuration::tryFrom($state)?->label() ?? $state),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->label('Submitted')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    AccessRequestStatus::Pending->value => 'Pending',
                    AccessRequestStatus::Approved->value => 'Approved',
                    AccessRequestStatus::Rejected->value => 'Rejected',
                ]),
                SelectFilter::make('target_type')->options([
                    AccessRequestTargetType::Team->value => 'Team',
                    AccessRequestTargetType::Subscription->value => 'Subscription',
                ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('approve')
                    ->label('Grant access')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (AccessRequest $record): bool => static::canDecide($record))
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\Textarea::make('decision_reason')->label('Approval note')->maxLength(2000),
                    ])
                    ->action(function (AccessRequest $record, array $data): void {
                        app(\App\Services\AccessRequestService::class)->approve($record, static::currentActor(), $data['decision_reason'] ?? null);
                    }),
                \Filament\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn (AccessRequest $record): bool => static::canDecide($record))
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\Textarea::make('decision_reason')->label('Reason')->required()->maxLength(2000),
                    ])
                    ->action(function (AccessRequest $record, array $data): void {
                        app(\App\Services\AccessRequestService::class)->reject($record, static::currentActor(), $data['decision_reason'] ?? null);
                    }),
            ]);
    }

    public static function canDecide(AccessRequest $record): bool
    {
        if ($record->status !== AccessRequestStatus::Pending) {
            return false;
        }

        if (auth()->guard('developers')->check()) {
            return auth()->guard('developers')->user()?->is_active === true;
        }

        $user = auth()->guard('web')->user();

        return $user instanceof User && app(AccessAuthorizationService::class)->canApprove($user, $record);
    }

    public static function currentActor(): User|\App\Models\Developer
    {
        if (auth()->guard('developers')->check()) {
            return auth()->guard('developers')->user();
        }

        return auth()->guard('web')->user();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccessRequests::route('/'),
            'create' => CreateAccessRequest::route('/create'),
            'view' => ViewAccessRequest::route('/{record}'),
        ];
    }
}
