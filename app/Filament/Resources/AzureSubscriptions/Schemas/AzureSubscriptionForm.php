<?php

namespace App\Filament\Resources\AzureSubscriptions\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AzureSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Subscription Configuration')
                    ->columns(2)
                    ->schema([
                        TextInput::make('display_name')
                            ->label('Subscription Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('subscription_id')
                            ->label('Subscription ID')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                        TextInput::make('key_vault_reference')
                            ->label('Key Vault Reference')
                            ->helperText('Reference to the service-principal secret in Azure Key Vault. Do not enter the secret here.')
                            ->maxLength(255),
                        Select::make('teams')
                            ->label('Teams')
                            ->relationship('teams', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->required(),
                    ]),
            ]);
    }
}
