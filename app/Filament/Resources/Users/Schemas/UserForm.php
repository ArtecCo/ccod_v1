<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\AzureSubscription;
use App\Models\UserResourceAccess;
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
                        ->helperText('Direct Business Unit membership. Resource access is managed separately below.')
                        ->columnSpanFull(),
                ]),

            Section::make('Resource Access')
                ->description('Define where this user can access resources. Higher levels inherit everything below them.')
                ->icon('heroicon-o-squares-2x2')
                ->schema([
                    Repeater::make('resourceAccesses')
                        ->relationship()
                        ->addActionLabel('Add access rule')
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => match ($state['access_type'] ?? null) {
                            'business_unit' => 'Business Unit access',
                            'project' => 'Project access',
                            'subscription' => 'Subscription access',
                            default => 'New access rule',
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
                                ->helperText('Choose the narrowest level that matches the user’s access requirement.'),
                            Select::make('business_unit_id')
                                ->label('Business Unit')
                                ->relationship('businessUnit', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Select a Business Unit')
                                ->visible(fn (Get $get): bool => $get('access_type') === 'business_unit')
                                ->required(fn (Get $get): bool => $get('access_type') === 'business_unit'),
                            Select::make('project_id')
                                ->label('Project')
                                ->relationship('project', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Select a Project')
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
                                ->placeholder('Select a Subscription')
                                ->visible(fn (Get $get): bool => $get('access_type') === 'subscription')
                                ->required(fn (Get $get): bool => $get('access_type') === 'subscription'),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),

            Section::make('Role Assignments')
                ->description('Roles define what the user can do within each resource-access rule.')
                ->icon('heroicon-o-shield-check')
                ->schema([
                    Repeater::make('roleAssignments')
                        ->relationship()
                        ->addActionLabel('Add role assignment')
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => 'Role assignment')
                        ->schema([
                            Select::make('role_id')
                                ->label('Role')
                                ->relationship('role', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->placeholder('Select a role'),
                            Select::make('resource_access_id')
                                ->label('Resource access')
                                ->relationship('resourceAccess', 'id')
                                ->getOptionLabelFromRecordUsing(fn (UserResourceAccess $record): string => $record->label())
                                ->required()
                                ->searchable()
                                ->preload()
                                ->placeholder('Select an access rule'),
                            DateTimePicker::make('starts_at')
                                ->label('Starts')
                                ->default(now()),
                            DateTimePicker::make('expires_at')
                                ->label('Expires')
                                ->helperText('Leave blank for no expiry.'),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
