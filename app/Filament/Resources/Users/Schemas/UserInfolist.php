<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')
                ->description('Identity, account status and user group membership.')
                ->icon('heroicon-o-user-circle')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Name')->weight('medium'),
                    TextEntry::make('email')->label('Email')->copyable(),
                    IconEntry::make('is_active')->boolean()->label('Active'),
                    TextEntry::make('created_at')->dateTime()->label('Activation Date'),
                    TextEntry::make('userGroups.name')->label('User Groups')->listWithLineBreaks()->placeholder('None'),
                    TextEntry::make('businessUnits.name')->label('Business Units')->listWithLineBreaks()->placeholder('None'),
                ]),
        ]);
    }
}
