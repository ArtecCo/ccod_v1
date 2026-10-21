<?php

namespace App\Filament\Resources\AccessRequests\Pages;

use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Models\UserGroup;
use App\Services\AccessRequestService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAccessRequest extends CreateRecord
{
    protected static string $resource = AccessRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->guard('web')->user();
        $group = UserGroup::query()->with('role')->findOrFail($data['user_group_id']);

        return app(AccessRequestService::class)->createForUserGroup(
            $user,
            $group,
            $data['reason'],
            \App\Enums\AccessRequestDuration::from($data['duration']),
            $data['requested_until'] ?? null,
        );
    }

    protected function getRedirectUrl(): string
    {
        return AccessRequestResource::getUrl('index');
    }
}
