<?php

namespace App\Filament\Resources\UserGroups\Schemas;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserGroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User Group')
                ->description('Define the role and membership that will determine effective project access.')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Group name')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),
                    Select::make('role_id')
                        ->label('Role')
                        ->options(fn (): array => Role::query()
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make('users')
                        ->relationship('users', 'name')
                        ->label('Users')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->helperText('Every selected user becomes a member of this group.')
                        ->columnSpanFull(),
                    Select::make('projects')
                        ->relationship('projects', 'name')
                        ->label('Projects')
                        ->multiple()
                        ->searchable()
                        ->preload()
                        ->helperText('Every group member receives the group role on these projects.')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
