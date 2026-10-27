<?php

namespace App\Filament\Resources\Roles\Tables;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Role;
use App\Models\UserGroup;
use App\Services\RolePermissionService;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Auth\Access\AuthorizationException;

class RolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug')->searchable(),
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->state(fn ($record): int => $record->permissions()
                        ->whereIn('permissions.slug', array_keys(RolePermissionService::managedPermissions()))
                        ->count()),
                IconColumn::make('is_system_role')->boolean()->label('System role'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('name')
            ->recordUrl(fn ($record): ?string => RoleResource::canView($record)
                ? RoleResource::getUrl('view', ['record' => $record])
                : null)
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record): bool => RoleResource::canEdit($record)),
                DeleteAction::make()
                    ->visible(fn ($record): bool => RoleResource::canDelete($record))
                    ->using(function (DeleteAction $action, Role $record): bool {
                        if (! RoleResource::canDelete($record)) {
                            throw new AuthorizationException('You are not authorized to delete this role.');
                        }

                        $groups = UserGroup::query()
                            ->where('role_id', $record->getKey())
                            ->orderBy('name')
                            ->pluck('name');

                        if ($groups->isNotEmpty()) {
                            Notification::make()
                                ->danger()
                                ->title('Role cannot be deleted')
                                ->body('This role is assigned to the following user group(s): ' . $groups->implode(', ') . '. Reassign or remove those groups before deleting the role.')
                                ->persistent()
                                ->send();

                            $action->halt();
                        }

                        $record->permissions()->detach();

                        return (bool) $record->delete();
                    }),
            ]);
    }
}
