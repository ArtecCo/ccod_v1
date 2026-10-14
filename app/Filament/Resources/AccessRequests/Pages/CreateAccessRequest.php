<?php

namespace App\Filament\Resources\AccessRequests\Pages;

use App\Enums\AccessRequestTargetType;
use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Models\AccessRequest;
use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\User;
use App\Services\AccessRequestService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAccessRequest extends CreateRecord
{
    protected static string $resource = AccessRequestResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->guard('web')->user();

        /** @var BusinessUnit|AzureSubscription $target */
        $target = $data['target_type'] === AccessRequestTargetType::Team->value
            ? BusinessUnit::query()->findOrFail($data['business_unit_id'])
            : AzureSubscription::query()->whereKey($data['subscription_id'])->firstOrFail();

        return app(AccessRequestService::class)->create(
            user: $user,
            targetType: AccessRequestTargetType::from($data['target_type']),
            target: $target,
            requestedRole: \App\Enums\UserRole::from($data['requested_role']),
            reason: $data['reason'],
            duration: \App\Enums\AccessRequestDuration::from($data['duration']),
            requestedUntil: $data['requested_until'] ?? null,
        );
    }
}
