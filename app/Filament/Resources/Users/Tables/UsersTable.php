<?php
namespace App\Filament\Resources\Users\Tables;
use App\Support\PillColorRegistry;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
class UsersTable
{
 public static function configure(Table $table):Table{return $table->columns([TextColumn::make('name')->searchable()->sortable(),TextColumn::make('email')->searchable()->sortable(),TextColumn::make('roleAssignments.role.name')->label('Roles')->listWithLineBreaks()->wrap(),IconColumn::make('is_active')->boolean()->label('Active'),TextColumn::make('businessUnits.name')->label('Business Units')->listWithLineBreaks()->wrap(),TextColumn::make('created_at')->dateTime()->sortable()->label('Activation Date')])->striped(false)->recordActions([ViewAction::make(),EditAction::make()])->toolbarActions([XlsxExportAction::make()]);}
}
