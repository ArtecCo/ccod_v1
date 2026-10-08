<?php

namespace App\Filament\Resources\AzureSubscriptions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AzureSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('display_name')
                    ->required(),
                TextInput::make('health_status')
                    ->default('Healthy'),
                TextInput::make('security_score')
                    ->numeric()
                    ->default(100.0),
                TextInput::make('mtd_spend_eur')
                    ->numeric()
                    ->default(0.0),
                DateTimePicker::make('last_synced')
                    ->required(),
            ]);
    }
}
