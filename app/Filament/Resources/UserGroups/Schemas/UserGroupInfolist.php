<?php

namespace App\Filament\Resources\UserGroups\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User Group')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextEntry::make('name')->label('Group name')->weight('medium'),
                    TextEntry::make('role.name')->label('Role')->badge(),
                    TextEntry::make('users.name')->label('Users')->listWithLineBreaks()->placeholder('None')->columnSpanFull(),
                    TextEntry::make('projects.name')->label('Projects')->listWithLineBreaks()->placeholder('None')->columnSpanFull(),
                ]),
        ]);
    }
}
