<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('email'),
            TextEntry::make('role')->badge(),
            IconEntry::make('is_active')->boolean(),
            TextEntry::make('teams.name')->label('Teams')->listWithLineBreaks(),
            TextEntry::make('created_at')->dateTime()->label('Activation Date'),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }
}
