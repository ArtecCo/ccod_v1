<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Services\UserAccessAssignmentService;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        app(UserAccessAssignmentService::class)->sync(
            $this->record,
            $this->data['accessAssignments'] ?? [],
        );
    }
}
