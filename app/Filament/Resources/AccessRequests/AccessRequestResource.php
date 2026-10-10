<?php

namespace App\Filament\Resources\AccessRequests;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;
use App\Filament\Resources\AccessRequests\Pages\ListAccessRequests;
use App\Filament\Resources\AccessRequests\Pages\ViewAccessRequest;
use App\Models\AccessRequest;
use App\Services\AccessAuthorizationService;
use Filament\Actions\ViewAction;
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

    public static function canViewAny(): bool
    {
        $user = auth()->guard('web')->user();

        return $user !== null && ($user->isGlobalOwner() || $user->roleEnum() === UserRole::RestrictedOwner);
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->guard('web')->user();

        if (! $user || ! static::canViewAny()) {
            return parent::getEloquentQuery()->whereKey(0);
        }

        if ($user->isGlobalOwner()) {
            return parent::getEloquentQuery()->with(['user', 'team', 'subscription']);
        }

        $subscriptionIds = $user->teams()
            ->with('subscriptions')
            ->get()
            ->flatMap(fn ($team) => $team->subscriptions->pluck('subscription_id'))
            ->unique()
            ->values();

        return parent::getEloquentQuery()
            ->with(['user', 'team', 'subscription'])
            ->where('status', AccessRequestStatus::Pending->value)
            ->where(function (Builder $query) use ($user, $subscriptionIds): void {
                $query->where(function (Builder $teamQuery) use ($user): void {
                    $teamQuery
                        ->where('target_type', AccessRequestTargetType::Team->value)
                        ->whereIn('team_id', $user->teams()->select('teams.id'));
                });

                if ($subscriptionIds->isNotEmpty()) {
                    $query->orWhere(function (Builder $subscriptionQuery) use ($subscriptionIds): void {
                        $subscriptionQuery
                            ->where('target_type', AccessRequestTargetType::Subscription->value)
                            ->whereIn('subscription_id', $subscriptionIds);
                    });
                }
            });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
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
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccessRequests::route('/'),
            'view' => ViewAccessRequest::route('/{record}'),
        ];
    }
}
