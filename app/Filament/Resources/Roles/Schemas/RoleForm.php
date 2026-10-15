<?php
namespace App\Filament\Resources\Roles\Schemas;
use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
class RoleForm
{
 public static function configure(Schema $schema):Schema
 {
  $sections=[Section::make('Role')->columns(2)->schema([TextInput::make('name')->required()->maxLength(255),TextInput::make('slug')->required()->alphaDash()->maxLength(255),Textarea::make('description')->columnSpanFull(),Toggle::make('is_active')->default(true)])];
  foreach(Permission::query()->where('is_active',true)->orderBy('category')->orderBy('name')->get()->groupBy('category') as $category=>$permissions)$sections[]=Section::make($category)->schema([CheckboxList::make('permission_'.md5($category))->label('Actions')->options($permissions->pluck('name','id')->all())->columns(2)->bulkToggleable()->searchable()]);
  return $schema->components($sections);
 }
}
