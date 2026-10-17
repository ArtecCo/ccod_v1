<?php

namespace App\Filament\Resources\AccessRequests;

use App\Enums\AccessRequestStatus;
use App\Filament\Resources\AccessRequests\Pages\ListAccessRequests;
use App\Filament\Resources\AccessRequests\Pages\ViewAccessRequest;
use App\Models\AccessRequest;
use App\Models\Developer;
use App\Models\Role;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\TextEntry;
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
        $u = auth()->guard('web')->user();
        return auth()->guard('developers')->user()?->is_active === true
            || ($u instanceof User && app(AccessAuthorizationService::class)->hasPermission($u, 'users.access_requests.view'));
    }

    public static function canView($record): bool
    {
        return static::canViewAny();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function getEloquentQuery(): Builder
    {
        $u = auth()->guard('web')->user();
        if (auth()->guard('developers')->user()?->is_active === true) return parent::getEloquentQuery()->with(['user', 'businessUnit', 'subscription']);
        if (! $u) return parent::getEloquentQuery()->whereKey(0);
        return parent::getEloquentQuery()->with(['user', 'businessUnit', 'subscription']);
    }

    public static function form(Schema $schema): Schema { return $schema->components([]); }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request')->columns(2)->schema([
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
            Section::make('Approval')->columns(2)->schema([
                TextEntry::make('approvers')->label('Approvers')->state(fn (AccessRequest $record): string => app(AccessAuthorizationService::class)->approvers($record)->map(fn (User $approver): string => $approver->name.' ('.$approver->email.')')->implode("\n"))->placeholder('No eligible approvers found.')->columnSpanFull(),
                TextEntry::make('decision_maker')->label('Decision by')->state(function (AccessRequest $record): ?string { if (!$record->decided_by_id || !$record->decided_by_type) return null; $model = $record->decided_by_type === Developer::class ? Developer::query()->find($record->decided_by_id) : User::query()->find($record->decided_by_id); return $model?->name ?? null; })->placeholder('Not decided'),
                TextEntry::make('decided_at')->label('Decision at')->dateTime('Y-m-d H:i:s')->placeholder('Not decided'),
                TextEntry::make('decision_reason')->label('Decision reason')->placeholder('No decision reason recorded.')->columnSpanFull(),
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
