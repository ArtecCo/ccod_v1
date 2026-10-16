<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Role;
use App\Services\RolePermissionService;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
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
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($set, $get, ?string $state): void {
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
                    Textarea::make('description')->columnSpan(1),
                    Toggle::make('is_active')->default(true)->columnSpan(1),
                    Toggle::make('is_system_role')->hidden()->dehydrated(false),
                ]),
        ];

        $permissionSections = [];
        $permissions = RolePermissionService::managedPermissions();

        foreach (collect($permissions)->groupBy('category') as $category => $definitions) {
            $field = 'permission_' . md5($category);

            $permissionSections[] = Section::make($category)
                ->schema([
                    CheckboxList::make($field)
                        ->label('Actions')
                        ->options($definitions->mapWithKeys(
                            fn (array $definition, string $slug): array => [$slug => $definition['name']],
                        )->all())
                        ->columns(2)
                        ->bulkToggleable()
                        ->searchable(),
                ]);
        }

        if ($permissionSections !== []) {
            $sections[] = Grid::make(2)
                ->schema($permissionSections)
                ->columnSpanFull();
        }

        return $schema->components($sections);
    }
}
