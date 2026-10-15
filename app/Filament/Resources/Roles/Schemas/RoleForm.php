<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $sections = [
            Section::make('Role')
                ->description('Create a custom role by defining its name and permissions. Custom roles are separate from built-in system roles.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true),
                    TextInput::make('slug')
                        ->required()
                        ->alphaDash()
                        ->maxLength(255)
                        ->helperText('A unique identifier for this role.'),
                    Textarea::make('description')
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->default(true),
                    Toggle::make('is_system_role')
                        ->hidden()
                        ->dehydrated(false),
                ]),
        ];

        foreach (
            Permission::query()
                ->where('is_active', true)
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->groupBy('category') as $category => $permissions
        ) {
            $sections[] = Section::make($category)
                ->schema([
                    CheckboxList::make('permission_' . md5($category))
                        ->label('Actions')
                        ->options($permissions->pluck('name', 'id')->all())
                        ->columns(2)
                        ->bulkToggleable()
                        ->searchable(),
                ]);
        }

        return $schema->components($sections);
    }
}
