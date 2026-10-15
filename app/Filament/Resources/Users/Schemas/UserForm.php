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
use Filament\Forms\Components\Repeater\TableColumn;

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
                        ->table([
                            TableColumn::make('Access level')->width('10rem'),
                            TableColumn::make('Resource')->width('18rem'),
                            TableColumn::make('Role')->width('15rem'),
                            TableColumn::make('Starts')->width('13rem'),
                            TableColumn::make('Expires')->width('15rem'),
                        ])
                        ->compact()
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
                                ->afterStateUpdated(fn (callable $set): mixed => $set('resource_id', null)),
                            Select::make('resource_id')
                                ->label('Resource')
                                ->options(function (Get $get): array {
                                    return match ($get('access_type')) {
                                        'business_unit' => BusinessUnit::query()->orderBy('name')->pluck('name', 'id')->all(),
                                        'project' => Project::query()->orderBy('name')->pluck('name', 'id')->all(),
                                        'subscription' => AzureSubscription::query()
                                            ->orderBy('display_name')
                                            ->pluck('display_name', 'subscription_id')
                                            ->all(),
                                        default => [],
                                    };
                                })
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live(),
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
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
