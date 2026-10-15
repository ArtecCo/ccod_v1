<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('businessUnit.name')->label('Business Unit'),
            TextEntry::make('description')->placeholder('—')->columnSpanFull(),
            TextEntry::make('subscriptionAssignments')->label('Environment subscriptions')->formatStateUsing(fn ($state) => collect($state)->map(fn ($assignment) => strtoupper($assignment->environment).': '.$assignment->subscription?->display_name)->implode("\n"))->listWithLineBreaks(),
            TextEntry::make('is_active')->badge(),
            TextEntry::make('created_at')->dateTime(),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }
}
