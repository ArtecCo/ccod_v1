<?php

namespace App\Filament\Resources\AccessRequests\Pages;

use App\Enums\AccessRequestStatus;
use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Services\AccessRequestService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAccessRequest extends ViewRecord
{
    protected static string $resource = AccessRequestResource::class;

    protected function getHeaderActions(): array
    {
        if ($this->record->status !== AccessRequestStatus::Pending) {
            return [];
        }

        return [
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check')
                ->color('success')
                ->requiresConfirmation()
                ->form([
                    Textarea::make('reason')->label('Decision reason')->rows(3)->placeholder('Optional approval note.'),
                ])
                ->action(function (array $data): void {
                    app(AccessRequestService::class)->approve(
                        $this->record,
                        auth()->guard('web')->user(),
                        $data['reason'] ?? null,
                    );

                    Notification::make()->title('Access request approved')->success()->send();
                    $this->redirect(AccessRequestResource::getUrl('index'));
                })
                ->visible(fn (): bool => AccessRequestResource::canViewAny()),
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->requiresConfirmation()
                ->form([
                    Textarea::make('reason')->label('Reason')->rows(3)->required()->placeholder('Explain why the request is being rejected.'),
                ])
                ->action(function (array $data): void {
                    app(AccessRequestService::class)->reject(
                        $this->record,
                        auth()->guard('web')->user(),
                        $data['reason'],
                    );

                    Notification::make()->title('Access request rejected')->success()->send();
                    $this->redirect(AccessRequestResource::getUrl('index'));
                })
                ->visible(fn (): bool => AccessRequestResource::canViewAny()),
        ];
    }
}
