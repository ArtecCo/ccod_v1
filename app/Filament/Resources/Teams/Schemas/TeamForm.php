<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Textarea::make('description')
                ->rows(3)
                ->columnSpanFull(),
            Select::make('users')
                ->label('Users')
                ->relationship('users', 'name')
                ->multiple()
                ->searchable()
                ->preload()
                ->columnSpanFull(),
            Select::make('subscriptions')
                ->label('Subscriptions')
                ->relationship('subscriptions', 'display_name')
                ->multiple()
                ->searchable()
                ->preload()
                ->columnSpanFull(),
        ]);
    }
}
