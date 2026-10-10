<?php

namespace App\Filament\Resources\Users\Pages;

use App\Enums\AccessRequestTargetType;
use App\Filament\Resources\Users\UserResource;
use App\Models\AzureSubscription;
use App\Models\Developer;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use App\Services\AccessRequestService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('revokeSubscriptionAccess')
                ->label('Revoke Subscription Access')
                ->icon('heroicon-o-no-symbol')
                ->color('danger')
                ->visible(fn (): bool => $this->canRevokeAnySubscriptionAccess())
                ->schema([
                    Select::make('subscription_ids')
                        ->label('Subscriptions')
                        ->multiple()
                        ->searchable()
                        ->required()
                        ->options(fn (): array => app(AccessAuthorizationService::class)
                            ->accessibleSubscriptions($this->record)
                            ->mapWithKeys(fn (AzureSubscription $subscription): array => [
                                $subscription->subscription_id => $subscription->display_name.' ('.$subscription->subscription_id.')',
                            ])
                            ->all()),
                    Textarea::make('reason')
                        ->label('Reason')
                        ->rows(3)
                        ->maxLength(1000),
                ])
                ->requiresConfirmation()
                ->modalHeading('Revoke subscription access')
                ->modalDescription('The selected subscription access will be revoked for this user. This also overrides access inherited from their team.')
                ->action(function (array $data): void {
                    $actor = auth()->guard('developers')->user() ?? auth()->guard('web')->user();
                    $service = app(AccessRequestService::class);

                    foreach ($data['subscription_ids'] as $subscriptionId) {
                        $subscription = AzureSubscription::query()->whereKey($subscriptionId)->firstOrFail();
                        $service->revokeSubscriptionAccess(
                            $this->record,
                            $subscription,
                            $actor,
                            $data['reason'] ?? null,
                        );
                    }

                    Notification::make()
                        ->title('Subscription access revoked')
                        ->success()
                        ->send();
                }),
            EditAction::make(),
        ];
    }

    private function canRevokeAnySubscriptionAccess(): bool
    {
        $actor = auth()->guard('developers')->user() ?? auth()->guard('web')->user();

        if (! $actor instanceof User && ! $actor instanceof Developer) {
            return false;
        }

        if ($actor instanceof Developer || $actor->isGlobalOwner()) {
            return app(AccessAuthorizationService::class)
                ->accessibleSubscriptions($this->record)
                ->isNotEmpty();
        }

        if ($actor->roleEnum()->value !== 'restricted_owner') {
            return false;
        }

        return app(AccessAuthorizationService::class)
            ->accessibleSubscriptions($this->record)
            ->contains(fn (AzureSubscription $subscription): bool => app(AccessAuthorizationService::class)
                ->canRevokeSubscriptionAccess($actor, $this->record, $subscription));
    }
}
