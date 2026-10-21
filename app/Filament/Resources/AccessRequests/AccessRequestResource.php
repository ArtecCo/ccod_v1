<?php

namespace App\Filament\Resources\AccessRequests;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Filament\Resources\AccessRequests\Pages\ListAccessRequests;
use App\Filament\Resources\AccessRequests\Pages\ViewAccessRequest;
use App\Models\AccessRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGroup;
use App\Services\AccessAuthorizationService;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
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
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';
    protected static string|\UnitEnum|null $navigationGroup = 'Administration';

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->user()?->is_active === true || auth()->guard('web')->check();
    }

    public static function canView($record): bool
    {
        $developer = auth()->guard('developers')->user();
        if ($developer?->is_active === true) return true;

        $user = auth()->guard('web')->user();
        if (! $user instanceof User) return false;

        return $record->user_id === $user->getKey()
            || app(AccessAuthorizationService::class)->hasPermission($user, 'users.access_requests.view');
    }

    public static function shouldRegisterNavigation(): bool
    {
        $developer = auth()->guard('developers')->user();
        if ($developer?->is_active === true) return true;

        $user = auth()->guard('web')->user();
        return $user instanceof User
            && app(AccessAuthorizationService::class)->hasPermission($user, 'users.access_requests.view');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['user', 'userGroup']);
        $developer = auth()->guard('developers')->user();

        if ($developer?->is_active === true) return $query;

        $user = auth()->guard('web')->user();
        if (! $user instanceof User) return $query->whereKey(0);

        $canViewAll = app(AccessAuthorizationService::class)->hasPermission($user, 'users.access_requests.view');
        return $canViewAll ? $query : $query->where('user_id', $user->getKey());
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Request access')
                ->description('Request membership in a User Group. The group determines the role and project access you receive.')
                ->columns(2)
                ->schema([
                    Select::make('user_group_id')
                        ->label('User Group')
                        ->options(fn (): array => UserGroup::query()->with('role')->orderBy('name')->get()->mapWithKeys(fn (UserGroup $group): array => [$group->id => $group->name.' — '.$group->role->name])->all())
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('duration')
                        ->label('Requested duration')
                        ->options([
                            AccessRequestDuration::Permanent->value => AccessRequestDuration::Permanent->label(),
                            AccessRequestDuration::TimeBound->value => AccessRequestDuration::TimeBound->label(),
                        ])
                        ->default(AccessRequestDuration::Permanent->value)
                        ->live()
                        ->required(),
                    DateTimePicker::make('requested_until')
                        ->label('Requested expiry')
                        ->minDate(now())
                        ->required(fn ($get): bool => $get('duration') === AccessRequestDuration::TimeBound->value)
                        ->visible(fn ($get): bool => $get('duration') === AccessRequestDuration::TimeBound->value),
                    Textarea::make('reason')
                        ->label('Reason')
                        ->required()
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Section::make('Approval workflow')->columnSpanFull()->schema([
                ViewEntry::make('workflow')->view('filament.infolists.entries.access-request-timeline')->hiddenLabel()->columnSpanFull(),
            ]),
            Section::make('Request')->columns(2)->columnSpanFull()->schema([
                TextEntry::make('user.name')->label('Requester'),
                TextEntry::make('user.email')->label('Email'),
                TextEntry::make('userGroup.name')->label('User Group'),
                TextEntry::make('requested_role')->label('Role'),
                TextEntry::make('duration')->label('Duration'),
                TextEntry::make('requested_until')->label('Requested expiry')->dateTime('Y-m-d H:i:s')->placeholder('No expiry'),
                TextEntry::make('created_at')->label('Submitted at')->dateTime('Y-m-d H:i:s'),
                TextEntry::make('status')->badge(),
                TextEntry::make('reason')->label('Request reason')->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            TextColumn::make('user.name')->label('Requester')->searchable()->sortable(),
            TextColumn::make('userGroup.name')->label('User Group')->searchable()->sortable(),
            TextColumn::make('requested_role')->label('Role')->formatStateUsing(fn ($state): string => Role::query()->where('slug', $state instanceof \BackedEnum ? $state->value : $state)->value('name') ?? (string) $state),
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
