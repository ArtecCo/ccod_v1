<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\BusinessUnit;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
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
                    TextInput::make('name')->label('Full name')->placeholder('Enter the user name')->required()->maxLength(255),
                    TextInput::make('email')->email()->required()->unique(ignoreRecord: true)->placeholder('name@example.com'),
                    TextInput::make('password')->password()->revealable()->required(fn (string $operation): bool => $operation === 'create')->dehydrated(fn (?string $state): bool => filled($state))->minLength(12)->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Leave blank to keep the current password.' : 'Use at least 12 characters.'),
                    Toggle::make('is_active')->label('Active account')->default(true)->inline(false)->helperText('Inactive users cannot sign in to the admin panel.'),
                    Select::make('businessUnits')->relationship('businessUnits', 'name')->label('Business Units')->multiple()->searchable()->preload()->placeholder('Select business units')->helperText('Direct Business Unit membership.')->columnSpanFull(),
                ]),
        ]);
    }
}
