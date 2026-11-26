<?php

namespace App\Filament\Pages;

use App\Models\UserGroup;
use App\Models\User;
use App\Services\NotificationService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SendNotification extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationLabel = 'Send Notification';
    protected static ?string $title = 'Send Notification';
    protected static ?string $slug = 'send-notification';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell-alert';
    protected static string|\UnitEnum|null $navigationGroup = 'Notifications';
    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.send-notification';

    public static function canAccess(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'developer'
            && auth()->guard('developers')->user()?->is_active === true;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'audience' => 'all',
            'user_group_ids' => [],
            'user_ids' => [],
            'type' => 'general',
            'severity' => 'info',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Notification')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(150),
                        Textarea::make('message')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000),
                        Select::make('type')
                            ->label('Type')
                            ->options([
                                'general' => 'General',
                                'announcement' => 'Announcement',
                                'maintenance' => 'Maintenance',
                                'alert' => 'Alert',
                                'action_required' => 'Action required',
                            ])
                            ->default('general')
                            ->required(),
                        Select::make('severity')
                            ->options([
                                'info' => 'Information',
                                'success' => 'Success',
                                'warning' => 'Warning',
                                'danger' => 'Critical',
                            ])
                            ->default('info')
                            ->required(),
                        TextInput::make('action_label')
                            ->label('Action label')
                            ->maxLength(80),
                        TextInput::make('action_url')
                            ->label('Action URL')
                            ->url()
                            ->maxLength(2048),
                    ])
                    ->columns(2),

                Section::make('Recipients')
                    ->description('Select one or more audiences. Duplicate recipients are automatically collapsed.')
                    ->schema([
                        Select::make('audience')
                            ->options([
                                'all' => 'All active users',
                                'user_groups' => 'User groups',
                                'users' => 'Selected users',
                            ])
                            ->default('all')
                            ->live()
                            ->required(),
                        Select::make('user_group_ids')
                            ->label('User groups')
                            ->multiple()
                            ->searchable()
                            ->dehydrated(true)
                            ->options(fn (): array => UserGroup::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->visible(fn (Get $get): bool => $get('audience') === 'user_groups')
                            ->minItems(fn (Get $get): int => $get('audience') === 'user_groups' ? 1 : 0),
                        Select::make('user_ids')
                            ->label('Users')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->live()
                            ->default([])
                            ->dehydrated(true)
                            ->options(fn (): array => User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                            ->visible(fn (Get $get): bool => $get('audience') === 'users')
                            ->minItems(fn (Get $get): int => $get('audience') === 'users' ? 1 : 0),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function send(NotificationService $notifications): void
    {
        $data = $this->form->getState();
        $audience = $data['audience'] ?? data_get($this->data, 'audience', 'all');
        $rawSelectedUserIds = data_get($this->data, 'user_ids');
        $selectedUserIds = $audience === 'users'
            ? (
                is_array($rawSelectedUserIds) && $rawSelectedUserIds !== []
                    ? $rawSelectedUserIds
                    : ($data['user_ids'] ?? [])
            )
            : [];

        $count = $notifications->send(
            title: $data['title'],
            message: $data['message'],
            type: $data['type'] ?? 'general',
            severity: $data['severity'] ?? 'info',
            actionUrl: $data['action_url'] ?? null,
            actionLabel: $data['action_label'] ?? null,
            allUsers: $audience === 'all',
            userGroupIds: $audience === 'user_groups' ? ($data['user_group_ids'] ?? []) : [],
            userIds: $selectedUserIds,
        );

        if ($count === 0) {
            Notification::make()
                ->title('No recipients found')
                ->body('No active users matched this selection. The form has been kept so you can review the audience and try again.')
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title('Notification queued')
            ->body("Queued for {$count} active user(s).")
            ->success()
            ->send();

        $this->form->fill([
            'audience' => 'all',
            'user_group_ids' => [],
            'user_ids' => [],
            'type' => 'general',
            'severity' => 'info',
        ]);
    }
}
