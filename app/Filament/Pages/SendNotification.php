<?php

namespace App\Filament\Pages;

use App\Models\Team;
use App\Models\User;
use App\Services\NotificationService;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
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

    public ?array $data = [];

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
                                'teams' => 'Team members',
                                'users' => 'Selected users',
                            ])
                            ->default('all')
                            ->live()
                            ->required(),
                        Select::make('team_ids')
                            ->label('Teams')
                            ->multiple()
                            ->searchable()
                            ->options(fn (): array => Team::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->visible(fn (Get $get): bool => $get('audience') === 'teams')
                            ->required(fn (Get $get): bool => $get('audience') === 'teams'),
                        Select::make('user_ids')
                            ->label('Users')
                            ->multiple()
                            ->searchable()
                            ->options(fn (): array => User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id')->all())
                            ->visible(fn (Get $get): bool => $get('audience') === 'users')
                            ->required(fn (Get $get): bool => $get('audience') === 'users'),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function send(NotificationService $notifications): void
    {
        $data = $this->form->getState();
        $audience = $data['audience'] ?? 'all';

        $count = $notifications->send(
            title: $data['title'],
            message: $data['message'],
            type: $data['type'] ?? 'general',
            severity: $data['severity'] ?? 'info',
            actionUrl: $data['action_url'] ?? null,
            actionLabel: $data['action_label'] ?? null,
            allUsers: $audience === 'all',
            teamIds: $audience === 'teams' ? ($data['team_ids'] ?? []) : [],
            userIds: $audience === 'users' ? ($data['user_ids'] ?? []) : [],
        );

        Notification::make()
            ->title('Notification sent')
            ->body("Delivered to {$count} active user(s).")
            ->success()
            ->send();

        $this->form->fill([
            'audience' => 'all',
            'type' => 'general',
            'severity' => 'info',
        ]);
    }
}
