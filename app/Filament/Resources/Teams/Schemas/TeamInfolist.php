<?php

namespace App\Filament\Resources\Teams\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TeamInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('description')->placeholder('—')->columnSpanFull(),
            TextEntry::make('users.name')->label('Users')->listWithLineBreaks(),
            TextEntry::make('subscriptions.display_name')->label('Subscriptions')->listWithLineBreaks(),
            TextEntry::make('created_at')->dateTime(),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }
}
