<?php
namespace App\Filament\Resources\AccessRequests\Pages;
use App\Enums\AccessRequestTargetType;
use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Models\AzureSubscription;
use App\Models\BusinessUnit;
use App\Models\Role;
use App\Services\AccessRequestService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
class CreateAccessRequest extends CreateRecord{protected static string $resource=AccessRequestResource::class;protected function handleRecordCreation(array $data):Model{$u=auth()->guard('web')->user();$target=$data['target_type']===AccessRequestTargetType::Team->value?BusinessUnit::query()->findOrFail($data['business_unit_id']):AzureSubscription::query()->whereKey($data['subscription_id'])->firstOrFail();$role=Role::query()->findOrFail($data['requested_role']);return app(AccessRequestService::class)->create($u,AccessRequestTargetType::from($data['target_type']),$target,$role,$data['reason'],\App\Enums\AccessRequestDuration::from($data['duration']),$data['requested_until']??null);}
protected function getRedirectUrl(): string{return AccessRequestResource::getUrl('index');}}
