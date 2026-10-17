<?php

namespace App\Filament\Resources\FailedJobs;

use App\Models\FailedJob;
use App\Services\AuditLogger;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;
use Throwable;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;
    protected static ?string $navigationLabel = 'Failed Jobs';
    protected static string|\UnitEnum|null $navigationGroup = 'Operations';
    protected static ?int $navigationSort = 20;

    public static function canViewAny(): bool
    {
        return auth()->guard('developers')->user()?->is_active === true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('failed_at');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('job_class')
                    ->label('Job')
                    ->state(fn (FailedJob $record): string => $record->jobClass())
                    ->wrap(),
                TextColumn::make('queue')->badge()->sortable(),
                TextColumn::make('failed_at')->dateTime('Y-m-d H:i:s')->sortable(),
                TextColumn::make('exception')
                    ->label('Failure')
                    ->limit(120)
                    ->wrap(),
            ])
            ->defaultSort('failed_at', 'desc')
            ->recordActions([
                Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Retry failed job')
                    ->modalDescription('This will move the failed job back onto its original queue. A running queue worker is required for it to execute.')
                    ->modalSubmitActionLabel('Retry job')
                    ->action(function (FailedJob $record): void {
                        try {
                            $exitCode = Artisan::call('queue:retry', [
                                'id' => (string) $record->getKey(),
                            ]);

                            if ($exitCode !== 0) {
                                throw new \RuntimeException(trim(Artisan::output()) ?: 'The queue retry command failed.');
                            }

                            app(AuditLogger::class)->log(
                                event: 'Queue Job Retried',
                                description: sprintf('Developer retried failed job #%s (%s).', $record->getKey(), $record->jobClass()),
                                subject: $record,
                                properties: [
                                    'job_id' => $record->getKey(),
                                    'job_class' => $record->jobClass(),
                                    'queue' => $record->queue,
                                ],
                                tags: ['developer', 'queue', 'retry'],
                            );

                            Notification::make()
                                ->title('Job queued for retry')
                                ->body(sprintf('Failed job #%s was moved back to the queue.', $record->getKey()))
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            app(AuditLogger::class)->log(
                                event: 'Queue Job Retry Failed',
                                description: sprintf('Developer failed to retry job #%s.', $record->getKey()),
                                subject: $record,
                                success: false,
                                failureReason: $exception->getMessage(),
                                properties: [
                                    'job_id' => $record->getKey(),
                                    'job_class' => $record->jobClass(),
                                ],
                                tags: ['developer', 'queue', 'retry', 'failure'],
                            );

                            Notification::make()
                                ->title('Job retry failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Delete failed job')
                    ->modalDescription('This permanently removes the failed-job record. It does not execute the job and cannot be undone.')
                    ->modalSubmitActionLabel('Delete failed job')
                    ->action(function (FailedJob $record): void {
                        try {
                            $jobId = $record->getKey();
                            $jobClass = $record->jobClass();

                            $exitCode = Artisan::call('queue:forget', [
                                'id' => (string) $jobId,
                            ]);

                            if ($exitCode !== 0) {
                                throw new \RuntimeException(trim(Artisan::output()) ?: 'The queue forget command failed.');
                            }

                            app(AuditLogger::class)->log(
                                event: 'Failed Queue Job Deleted',
                                description: sprintf('Developer deleted failed job #%s (%s).', $jobId, $jobClass),
                                properties: [
                                    'job_id' => $jobId,
                                    'job_class' => $jobClass,
                                ],
                                tags: ['developer', 'queue', 'delete'],
                            );

                            Notification::make()
                                ->title('Failed job deleted')
                                ->body(sprintf('Failed job #%s was removed.', $jobId))
                                ->success()
                                ->send();
                        } catch (Throwable $exception) {
                            app(AuditLogger::class)->log(
                                event: 'Failed Queue Job Delete Failed',
                                description: sprintf('Developer failed to delete failed job #%s.', $record->getKey()),
                                subject: $record,
                                success: false,
                                failureReason: $exception->getMessage(),
                                properties: [
                                    'job_id' => $record->getKey(),
                                    'job_class' => $record->jobClass(),
                                ],
                                tags: ['developer', 'queue', 'delete', 'failure'],
                            );

                            Notification::make()
                                ->title('Failed job deletion failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFailedJobs::route('/'),
        ];
    }
}
