<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\UserAccessAssignmentService;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->record->loadMissing(['resourceAccesses.roleAssignments.role']);

        $data['accessAssignments'] = $this->record->resourceAccesses
            ->map(function ($access): array {
                $assignment = $access->roleAssignments->sortByDesc(function ($item): int {
                    return $item->role?->weight ?? 0;
                })->first();

                $resourceId = match ($access->access_type) {
                    'business_unit' => $access->business_unit_id,
                    'project' => $access->project_id,
                    'subscription' => $access->subscription_id,
                    default => null,
                };

                return [
                    'resource_access_id' => $access->id,
                    'role_assignment_id' => $assignment?->id,
                    'access_type' => $access->access_type,
                    'resource_id' => $resourceId,
                    'role_id' => $assignment?->role_id,
                    'starts_at' => $assignment?->starts_at,
                    'expires_at' => $assignment?->expires_at,
                ];
            })
            ->values()
            ->all();

        return $data;
    }

    protected function afterSave(): void
    {
        app(UserAccessAssignmentService::class)->sync(
            $this->record,
            $this->data['accessAssignments'] ?? [],
        );
    }
}
