<?php

namespace App\Filament\Resources\AccessRequests;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Enums\AccessRequestTargetType;
use App\Enums\UserRole;
use App\Filament\Resources\AccessRequests\Pages\ListAccessRequests;
use App\Filament\Resources\AccessRequests\Pages\ViewAccessRequest;
use App\Models\AccessRequest;
use App\Models\User;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
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
        return auth()->guard('web')->user() !== null || (auth()->guard('developers')->user()?->is_active === true);
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->guard('web')->user();
        $developer = auth()->guard('developers')->user();

        return $developer?->is_active === true
            || ($user !== null && ($user->isGlobalOwner() || $user->roleEnum() === UserRole::RestrictedOwner));
    }

    public static function getEloquentQuery(): Builder
    {
        $developer = auth()->guard('developers')->user();
        $user = auth()->guard('web')->user();

        if ($developer?->is_active === true) {
            return parent::getEloquentQuery()->with(['user', 'team', 'subscription']);
        }

        if (! $user) {
            return parent::getEloquentQuery()->whereKey(0);
        }

        if ($user->isGlobalOwner()) {
            return parent::getEloquentQuery()->with(['user', 'team', 'subscription']);
        }

        $query = parent::getEloquentQuery()->with(['user', 'team', 'subscription']);

        if ($user->roleEnum() !== UserRole::RestrictedOwner) {
            return $query->where('user_id', $user->getKey());
        }

        $subscriptionIds = $user->teams()
            ->with('subscriptions')
            ->get()
            ->flatMap(fn ($team) => $team->subscriptions->pluck('subscription_id'))
            ->unique()
            ->values();

        return $query->where(function (Builder $requestQuery) use ($user, $subscriptionIds): void {
            $requestQuery->where('user_id', $user->getKey());
            $requestQuery->orWhere(function (Builder $approvalQuery) use ($user, $subscriptionIds): void {
                $approvalQuery->where(function (Builder $teamQuery) use ($user): void {
                    $teamQuery
                        ->where('target_type', AccessRequestTargetType::Team->value)
                        ->whereIn('team_id', $user->teams()->select('teams.id'));
                });

                if ($subscriptionIds->isNotEmpty()) {
                    $approvalQuery->orWhere(function (Builder $subscriptionQuery) use ($subscriptionIds): void {
                        $subscriptionQuery
                            ->where('target_type', AccessRequestTargetType::Subscription->value)
                            ->whereIn('subscription_id', $subscriptionIds);
                    });
                }
            });
        });
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request')->schema([
                TextEntry::make('user.name')->label('Requester'),
                TextEntry::make('user.email')->label('Email'),
                TextEntry::make('target_name')->label('Target'),
                TextEntry::make('target_type')->label('Target type')->formatStateUsing(fn (AccessRequestTargetType|string $state): string => $state instanceof AccessRequestTargetType ? $state->label() : (AccessRequestTargetType::tryFrom($state)?->label() ?? $state))->badge(),
                TextEntry::make('requested_role')->label('Requested role')->formatStateUsing(fn (UserRole|string $state): string => $state instanceof UserRole ? $state->label() : (UserRole::tryFrom($state)?->label() ?? $state)),
                TextEntry::make('duration')->label('Requested duration')->formatStateUsing(fn (AccessRequestDuration|string $state): string => $state instanceof AccessRequestDuration ? $state->label() : (AccessRequestDuration::tryFrom($state)?->label() ?? $state)),
                TextEntry::make('requested_until')->label('Requested expiry')->dateTime('Y-m-d H:i:s')->placeholder('No expiry'),
                TextEntry::make('status')->badge(),
                TextEntry::make('reason')->columnSpanFull(),
            ])->columns(2),
            Section::make('Approvers')->schema([
                TextEntry::make('approvers')->label('Approvers')->state(function (AccessRequest $record): string {
                    $approvers = app(\App\Services\AccessAuthorizationService::class)->approvers($record);
                    return $approvers->isEmpty() ? 'No active approvers found' : $approvers->map(fn (User $approver): string => $approver->name.' ('.$approver->email.')')->implode(', ');
                })->columnSpanFull(),
            ]),
            Section::make('Decision')->schema([
                TextEntry::make('decided_by')->label('Decided by')->state(function (AccessRequest $record): string {
                    if (! $record->decided_by_id || ! $record->decided_by_type) return 'Pending';
                    $actor = $record->decided_by_type::query()->find($record->decided_by_id);
                    return $actor?->name ?? $actor?->email ?? 'Unknown';
                }),
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
                TextColumn::make('user.name')->label('Requester')->searchable()->sortable()->toggleable(),
                TextColumn::make('target_name')->label('Target')->searchable()->sortable()->toggleable(),
                TextColumn::make('target_type')->label('Type')->badge()->toggleable(),
                TextColumn::make('requested_role')->label('Requested role')->formatStateUsing(fn (UserRole|string $state): string => $state instanceof UserRole ? $state->label() : (UserRole::tryFrom($state)?->label() ?? $state))->toggleable(),
                TextColumn::make('duration')->label('Duration')->formatStateUsing(fn (AccessRequestDuration|string $state): string => $state instanceof AccessRequestDuration ? $state->label() : (AccessRequestDuration::tryFrom($state)?->label() ?? $state))->toggleable(),
                TextColumn::make('status')->badge()->toggleable(),
                TextColumn::make('created_at')->label('Requested')->dateTime('Y-m-d H:i:s')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(collect(AccessRequestStatus::cases())->mapWithKeys(fn (AccessRequestStatus $status): array => [$status->value => $status->label()])->all()),
                SelectFilter::make('target_type')->options([
                    AccessRequestTargetType::Team->value => 'Team',
                    AccessRequestTargetType::Subscription->value => 'Subscription',
                ]),
            ])
            ->actions([ViewAction::make()])
            ->headerActions([
                XlsxExportAction::make()->title('Export Access Requests')->fileName(fn (): string => 'access-requests-'.now()->format('Y-m-d-His')),
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
