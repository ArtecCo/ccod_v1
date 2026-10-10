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
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->guard('web')->user();

        if (! $user) {
            return parent::getEloquentQuery()->whereKey(0);
        }

        return parent::getEloquentQuery()
            ->with(['user', 'team', 'subscription']);
    }

    public static function canViewAny(): bool
    {
        return auth()->guard('web')->user()?->isGlobal() === true;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            \Filament\Schemas\Components\Section::make('Access request')
                ->schema([
                    \Filament\Forms\Components\Select::make('target_type')
                        ->options([
                            AccessRequestTargetType::Team->value => 'Team',
                            AccessRequestTargetType::Subscription->value => 'Subscription',
                        ])
                        ->live()
                        ->required(),
                    \Filament\Forms\Components\Select::make('team_id')
                        ->label('Team')
                        ->options(fn () => Team::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->visible(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Team->value)
                        ->required(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Team->value),
                    \Filament\Forms\Components\Select::make('subscription_id')
                        ->label('Subscription')
                        ->options(fn () => \App\Models\AzureSubscription::query()->orderBy('display_name')->pluck('display_name', 'subscription_id'))
                        ->searchable()
                        ->visible(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Subscription->value)
                        ->required(fn ($get): bool => $get('target_type') === AccessRequestTargetType::Subscription->value),
                    \Filament\Forms\Components\Select::make('requested_role')
                        ->options(collect(UserRole::cases())->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])->all())
                        ->required(),
                    \Filament\Forms\Components\Textarea::make('reason')
                        ->required()
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
                TextColumn::make('requested_role')->label('Requested role')->formatStateUsing(fn (string $state): string => UserRole::tryFrom($state)?->label() ?? $state),
                TextColumn::make('duration')->label('Duration')->formatStateUsing(fn (string $state): string => AccessRequestDuration::tryFrom($state)?->label() ?? $state),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->label('Requested')->dateTime('Y-m-d H:i:s')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(collect(AccessRequestStatus::cases())->mapWithKeys(fn (AccessRequestStatus $status): array => [$status->value => $status->label()])->all()),
                SelectFilter::make('target_type')->options([
                    AccessRequestTargetType::Team->value => 'Team',
                    AccessRequestTargetType::Subscription->value => 'Subscription',
                ]),
            ])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),
            ]);
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
