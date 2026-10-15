<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
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
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                            if (blank($get('slug'))) {
                                $set('slug', Str::slug((string) $state));
                            }
                        })
                        ->unique(table: Role::class, column: 'name', ignoreRecord: true),
                    TextInput::make('slug')
                        ->required()
                        ->alphaDash()
                        ->maxLength(255)
                        ->unique(table: Role::class, column: 'slug', ignoreRecord: true)
                        ->helperText('Automatically suggested from the role name. You can change it if needed.'),
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
                ->where('category', '!=', 'Administration')
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
