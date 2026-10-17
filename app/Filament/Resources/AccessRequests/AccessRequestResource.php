<?php

namespace App\Filament\Resources\AccessRequests;

use App\Enums\AccessRequestStatus;
use App\Filament\Resources\AccessRequests\Pages\ListAccessRequests;
use App\Filament\Resources\AccessRequests\Pages\ViewAccessRequest;
use App\Models\AccessRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AccessRequestResource extends Resource
{
    protected static ?string $model = AccessRequest::class;
    protected static ?string $navigationLabel = 'Access Requests';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->user()?->is_active === true
            || auth()->guard('web')->check();
    }

    public static function canView($record): bool
    {
        $developer = auth()->guard('developers')->user();
        if ($developer?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();
        if (! $user instanceof User) {
            return false;
        }

        return $record->user_id === $user->getKey()
            || app(AccessAuthorizationService::class)->hasPermission($user, 'users.access_requests.view');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $developer = auth()->guard('developers')->user();
        if ($developer?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();
        return $user instanceof User
            && app(AccessAuthorizationService::class)->hasPermission($user, 'users.access_requests.view');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user', 'businessUnit', 'subscription']);
        $developer = auth()->guard('developers')->user();

        if ($developer?->is_active === true) {
            return $query;
        }

        $user = auth()->guard('web')->user();
        if (! $user instanceof User) {
            return $query->whereKey(0);
        }

        $canViewAll = app(AccessAuthorizationService::class)->hasPermission($user, 'users.access_requests.view');

        return $canViewAll
            ? $query
            : $query->where('user_id', $user->getKey());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Request')
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextEntry::make('user.name')->label('Requester'),
                    TextEntry::make('user.email')->label('Email'),
                    TextEntry::make('target_name')->label('Target'),
                    TextEntry::make('target_type')->label('Target type')->badge(),
                    TextEntry::make('requested_role')->label('Requested role')->formatStateUsing(fn ($state): string => Role::query()->where('slug', $state instanceof \BackedEnum ? $state->value : $state)->value('name') ?? (string) $state),
                    TextEntry::make('duration')->label('Duration'),
                    TextEntry::make('requested_until')->label('Requested expiry')->dateTime('Y-m-d H:i:s')->placeholder('No expiry'),
                    TextEntry::make('created_at')->label('Submitted at')->dateTime('Y-m-d H:i:s'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('reason')->label('Request reason')->columnSpanFull(),
                ]),
            Section::make('Approval workflow')
                ->columnSpanFull()
                ->schema([
                    Wizard::make([
                        Step::make('Submitted')
                            ->description(fn (AccessRequest $record): string => $record->created_at?->format('d M Y, H:i:s') ?? '—')
                            ->schema([
                                TextEntry::make('workflow_submitted_by')
                                    ->label('Submitted by')
                                    ->state(fn (AccessRequest $record): string => $record->user?->name ?? 'The requester'),
                                TextEntry::make('workflow_submitted_at')
                                    ->label('Submitted at')
                                    ->state(fn (AccessRequest $record): string => $record->created_at?->format('d M Y, H:i:s') ?? '—'),
                            ])
                            ->columns(2),
                        Step::make('Awaiting approval')
                            ->description('Eligible approvers')
                            ->schema([
                                TextEntry::make('workflow_approvers')
                                    ->label('Eligible approvers')
                                    ->state(function (AccessRequest $record): string {
                                        $approvers = app(AccessAuthorizationService::class)->approvers($record);

                                        return $approvers->isNotEmpty()
                                            ? $approvers->map(fn (User $approver): string => $approver->name . ' (' . $approver->email . ')')->implode(', ')
                                            : 'No eligible approvers found.';
                                    })
                                    ->columnSpanFull(),
                            ]),
                        Step::make(fn (AccessRequest $record): string => match ($record->status) {
                            AccessRequestStatus::Approved, AccessRequestStatus::Revoked => 'Approved',
                            AccessRequestStatus::Rejected => 'Rejected',
                            default => 'Not decided',
                        })
                            ->description(fn (AccessRequest $record): string => $record->decided_at?->format('d M Y, H:i:s') ?? 'Awaiting decision')
                            ->schema([
                                TextEntry::make('workflow_decision_by')
                                    ->label('Decision by')
                                    ->state(function (AccessRequest $record): ?string {
                                        if (! $record->decided_by_id || ! $record->decided_by_type) {
                                            return null;
                                        }

                                        $model = $record->decided_by_type === \App\Models\Developer::class
                                            ? \App\Models\Developer::query()->find($record->decided_by_id)
                                            : User::query()->find($record->decided_by_id);

                                        return $model?->name;
                                    })
                                    ->placeholder('Not decided'),
                                TextEntry::make('workflow_decision_at')
                                    ->label('Decision at')
                                    ->state(fn (AccessRequest $record): ?string => $record->decided_at?->format('d M Y, H:i:s'))
                                    ->placeholder('Not decided'),
                                TextEntry::make('workflow_decision_reason')
                                    ->label('Decision reason')
                                    ->state(fn (AccessRequest $record): ?string => $record->decision_reason)
                                    ->placeholder('No decision reason recorded.')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                        ->skippable()
                        ->startOnStep(fn (AccessRequest $record): int => match ($record->status) {
                            AccessRequestStatus::Approved,
                            AccessRequestStatus::Rejected,
                            AccessRequestStatus::Revoked => 3,
                            default => 2,
                        })
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            TextColumn::make('user.name')->label('Requester')->searchable()->sortable(),
            TextColumn::make('target_name')->label('Target')->searchable()->sortable(),
            TextColumn::make('requested_role')->label('Requested role')->formatStateUsing(fn ($state): string => Role::query()->where('slug', $state instanceof \BackedEnum ? $state->value : $state)->value('name') ?? (string) $state),
            TextColumn::make('status')->badge(),
            TextColumn::make('created_at')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options(collect(AccessRequestStatus::cases())->mapWithKeys(fn ($s) => [$s->value => $s->label()])->all()),
        ])->actions([ViewAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAccessRequests::route('/'),
            'view' => ViewAccessRequest::route('/{record}'),
            'create' => \App\Filament\Resources\AccessRequests\Pages\CreateAccessRequest::route('/create'),
        ];
    }
}
