<?php
namespace App\Filament\Resources\Roles\Pages;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\EditRecord;
class EditRole extends EditRecord
{
 protected static string $resource=RoleResource::class;
 protected function mutateFormDataBeforeFill(array $data):array{foreach($this->record->permissions()->get()->groupBy('category') as $category=>$permissions)$data['permission_'.md5($category)]=$permissions->pluck('id')->all();return $data;}
 protected function afterSave():void{$this->record->permissions()->sync($this->permissionIds($this->data));}
 protected function getHeaderActions():array{return [\Filament\Actions\DeleteAction::make()->disabled(fn()=> $this->record->is_system_role)];}
 private function permissionIds(array $data):array{return collect($data)->filter(fn($v,$k)=>str_starts_with($k,'permission_'))->flatten()->filter()->unique()->values()->all();}
}
