<?php

namespace App\Filament\Resources\AccessRequests\Pages;

use App\Enums\AccessRequestDuration;
use App\Enums\AccessRequestStatus;
use App\Filament\Resources\AccessRequests\AccessRequestResource;
use App\Services\AccessAuthorizationService;
use App\Services\AccessRequestService;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;
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

        $user = auth()->guard('web')->user();

        if (! $user || ! app(AccessAuthorizationService::class)->canApprove($user, $this->record)) {
            return [];
        }

        return [
            Action::make('approve')
                ->label('Approve')
                ->icon('heroicon-o-check')
                ->color('success')
                ->form([
                    Select::make('duration')
                        ->label('Approved access duration')
                        ->options([
                            AccessRequestDuration::Permanent->value => AccessRequestDuration::Permanent->label(),
                            AccessRequestDuration::TimeBound->value => AccessRequestDuration::TimeBound->label(),
                        ])
                        ->default($this->record->duration->value)
                        ->required()
                        ->live(),
                    DateTimePicker::make('requested_until')
                        ->label('Access expires at')
                        ->minDate(now())
                        ->required(fn (Get $get): bool => $get('duration') === AccessRequestDuration::TimeBound->value)
                        ->visible(fn (Get $get): bool => $get('duration') === AccessRequestDuration::TimeBound->value)
                        ->default($this->record->duration === AccessRequestDuration::TimeBound ? $this->record->requested_until : null),
                    Textarea::make('reason')
                        ->label('Decision reason')
                        ->rows(3)
                        ->placeholder('Optional approval note.'),
                ])
                ->modalHeading('Approve access request')
                ->modalDescription('You may change the requested duration. Permanent access can be made time-bound, and time-bound access can be made permanent or given a different expiry.')
                ->modalSubmitActionLabel('Approve access')
                ->action(function (array $data): void {
                    $duration = AccessRequestDuration::from($data['duration']);
                    $requestedUntil = $duration === AccessRequestDuration::TimeBound
                        ? $data['requested_until']
                        : null;

                    app(AccessRequestService::class)->approve(
                        $this->record,
                        auth()->guard('web')->user(),
                        $data['reason'] ?? null,
                        $duration,
                        $requestedUntil,
                    );

                    Notification::make()->title('Access request approved')->success()->send();
                    $this->redirect(AccessRequestResource::getUrl('index'));
                }),
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
                }),
        ];
    }
}
