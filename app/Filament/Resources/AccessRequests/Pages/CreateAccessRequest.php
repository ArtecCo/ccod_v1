<?php

namespace App\Filament\Resources\AccessRequests\Pages;

use App\Enums\AccessRequestDuration;
use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Models\UserGroup;
use App\Services\AccessRequestService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class CreateAccessRequest extends CreateRecord
{
    protected static string $resource = AccessRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->guard('web')->user();
        $group = UserGroup::query()->with('role')->findOrFail($data['user_group_id']);
        $duration = AccessRequestDuration::from($data['duration']);
        $requestedUntil = $duration === AccessRequestDuration::TimeBound
            ? Carbon::parse($data['requested_until'])
            : null;

        return app(AccessRequestService::class)->createForUserGroup(
            $user,
            $group,
            $data['reason'],
            $duration,
            $requestedUntil,
        );
    }

    protected function getRedirectUrl(): string
    {
        return AccessRequestResource::getUrl('index');
    }
}
