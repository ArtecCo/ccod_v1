<?php

namespace App\Filament\Pages;

use App\Enums\AccessRequestDuration;
use App\Models\User;
use App\Models\UserGroup;
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

    public function getUserGroups(): Collection
    {
        return $this->getUser()
            ->userGroups()
            ->with(['role', 'projects'])
            ->where(function ($query): void {
                $query->whereNull('user_group_user.starts_at')
                    ->orWhere('user_group_user.starts_at', '<=', now());
            })
            ->where(function ($query): void {
                $query->whereNull('user_group_user.expires_at')
                    ->orWhere('user_group_user.expires_at', '>', now());
            })
            ->orderBy('name')
            ->get();
    }

    public function getRequestableUserGroups(): Collection
    {
        $user = $this->getUser();
        $authorization = app(AccessAuthorizationService::class);

        return UserGroup::query()
            ->with('role')
            ->whereHas('role', fn ($query) => $query->where('is_active', true))
            ->orderBy('name')
            ->get()
            ->filter(fn (UserGroup $group): bool => $authorization->canRequestUserGroup($user, $group))
            ->values();
    }

    public function content(Schema $schema): Schema
    {
        $user = $this->getUser();
        $groups = $this->getUserGroups();

        return $schema->components([
            Section::make('Account overview')
                ->description('Your identity and current User Group membership.')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('account_name')->label('Account')->state($user->name),
                        TextEntry::make('email')->label('Email')->state($user->email),
                        TextEntry::make('group_count')->label('User Groups')->state((string) $groups->count()),
                    ]),
                ])
                ->columnSpanFull(),
            Section::make('Personal information')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('name')->label('Name')->state($user->name),
                        TextEntry::make('email_address')->label('Email')->state($user->email),
                        TextEntry::make('joined')->label('Date joined')->state($user->created_at?->format('d M Y, H:i')),
                    ]),
                ])
                ->columnSpanFull(),
            Section::make('User Groups')
                ->description('Your access and role are determined by your active User Group memberships.')
                ->schema($groups->isEmpty()
                    ? [TextEntry::make('none')->hiddenLabel()->state('You are not currently a member of any User Group.')]
                    : $groups->map(fn (UserGroup $group): Section => Section::make($group->name)
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('role_'.$group->id)->label('Role')->state($group->role->name)->badge(),
                                TextEntry::make('projects_'.$group->id)->label('Projects')->state($group->projects->pluck('name')->implode(', ') ?: 'No projects assigned'),
                            ]),
                        ])
                        ->collapsible()
                        ->collapsed())->all())
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
                ->form([
                    Select::make('user_group_id')
                        ->label('User Group')
                        ->options(fn (): array => $this->getRequestableUserGroups()
                            ->mapWithKeys(fn (UserGroup $group): array => [$group->id => $group->name.' — '.$group->role->name])
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required(),
                    Textarea::make('reason')
                        ->label('Reason')
                        ->required()
                        ->rows(4),
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
                    $group = UserGroup::query()->findOrFail($data['user_group_id']);

                    app(AccessRequestService::class)->createForUserGroup(
                        $this->getUser(),
                        $group,
                        $data['reason'],
                        AccessRequestDuration::from($data['duration']),
                        isset($data['requested_until']) ? new \DateTimeImmutable($data['requested_until']) : null,
                    );

                    Notification::make()->title('Access request submitted')->success()->send();
                })
                ->modalSubmitActionLabel('Submit request'),
        ];
    }
}
