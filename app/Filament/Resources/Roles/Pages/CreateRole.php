<?php
namespace App\Filament\Resources\Roles\Pages;
use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;
class CreateRole extends CreateRecord{protected static string $resource=RoleResource::class;protected function afterCreate():void{$this->record->permissions()->sync($this->permissionIds($this->data));}private function permissionIds(array $data):array{return collect($data)->filter(fn($v,$k)=>str_starts_with($k,'permission_'))->flatten()->filter()->unique()->values()->all();}}
