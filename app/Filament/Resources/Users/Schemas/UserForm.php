<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\Project;
use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')
                ->description('Basic identity and account status.')
                ->icon('heroicon-o-user-circle')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Full name')
                        ->placeholder('Enter the user name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->placeholder('name@example.com'),
                    TextInput::make('password')
                        ->password()
                        ->revealable()
                        ->required(fn (string $operation): bool => $operation === 'create')
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->minLength(12)
                        ->helperText(fn (string $operation): ?string => $operation === 'edit'
                            ? 'Leave blank to keep the current password.'
                            : 'Use at least 12 characters.'),
                    Toggle::make('is_active')
                        ->label('Active account')
                        ->default(true)
                        ->inline(false)
                        ->helperText('Inactive users cannot sign in to the admin panel.'),
                    Select::make('businessUnits')
                        ->relationship('businessUnits', 'name')
                        ->label('Business Units')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->placeholder('Select business units')
                        ->helperText('Direct Business Unit membership.')
                        ->columnSpanFull(),
                ]),

            Section::make('Access & Roles')
                ->description('Define where the user can access resources and what they can do there.')
                ->icon('heroicon-o-shield-check')
                ->columnSpanFull()
                ->schema([
                    Repeater::make('accessAssignments')
                        ->addActionLabel('Add access')
                        ->collapsible()
                        ->itemLabel(fn (array $state): string => match ($state['access_type'] ?? null) {
                            'business_unit' => 'Business Unit access',
                            'project' => 'Project access',
                            'subscription' => 'Subscription access',
                            default => 'New access',
                        })
                        ->schema([
                            Select::make('access_type')
                                ->label('Access level')
                                ->options([
                                    'business_unit' => 'Business Unit',
                                    'project' => 'Project',
                                    'subscription' => 'Subscription',
                                ])
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, callable $set): void {
                                    $set('business_unit_id', null);
                                    $set('project_id', null);
                                    $set('subscription_id', null);
                                }),
                            Select::make('business_unit_id')
                                ->label('Business Unit')
                                ->options(fn (): array => BusinessUnit::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()
                                ->preload()
                                ->visible(fn (Get $get): bool => $get('access_type') === 'business_unit')
                                ->required(fn (Get $get): bool => $get('access_type') === 'business_unit'),
                            Select::make('project_id')
                                ->label('Project')
                                ->options(fn (): array => Project::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()
                                ->preload()
                                ->visible(fn (Get $get): bool => $get('access_type') === 'project')
                                ->required(fn (Get $get): bool => $get('access_type') === 'project'),
                            Select::make('subscription_id')
                                ->label('Subscription')
                                ->options(fn (): array => AzureSubscription::query()
                                    ->orderBy('display_name')
                                    ->pluck('display_name', 'subscription_id')
                                    ->all())
                                ->searchable()
                                ->preload()
                                ->visible(fn (Get $get): bool => $get('access_type') === 'subscription')
                                ->required(fn (Get $get): bool => $get('access_type') === 'subscription'),
                            Select::make('role_id')
                                ->label('Role')
                                ->options(fn (): array => Role::query()
                                    ->where('is_active', true)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->required()
                                ->searchable()
                                ->preload()
                                ->placeholder('Select a role'),
                            DateTimePicker::make('starts_at')
                                ->label('Starts')
                                ->default(now()),
                            DateTimePicker::make('expires_at')
                                ->label('Expires')
                                ->helperText('Leave blank for no expiry.'),
                            TextInput::make('resource_access_id')->hidden()->dehydrated(),
                            TextInput::make('role_assignment_id')->hidden()->dehydrated(),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
