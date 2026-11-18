<?php

namespace App\Filament\Resources\UserGroups\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserGroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Group Information')
                ->schema([
                    TextEntry::make('name')->label('Group Name')->weight('medium'),
                    TextEntry::make('role.name')->label('Role')->placeholder('—'),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('Users')
                ->schema([
                    RepeatableEntry::make('users')
                        ->schema([
                            TextEntry::make('name')->hiddenLabel(),
                        ])
                        ->columns(1)
                        ->grid(3)
                        ->hiddenLabel()
                        ->placeholder('No users assigned.')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Projects')
                ->schema([
                    RepeatableEntry::make('projects')
                        ->schema([
                            TextEntry::make('name')->hiddenLabel(),
                        ])
                        ->columns(1)
                        ->grid(2)
                        ->hiddenLabel()
                        ->placeholder('No projects assigned.')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
