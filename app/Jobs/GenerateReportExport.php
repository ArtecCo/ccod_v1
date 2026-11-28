<?php

namespace App\Jobs;

use App\Models\Developer;
use App\Models\ReportExport;
use App\Services\ReportPdfService;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class GenerateReportExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 80;

    public function __construct(public readonly int $reportExportId)
    {
        // Use the application's default database queue so existing workers process report jobs.
    }

    public function handle(ReportPdfService $pdf): void
    {
        $report = ReportExport::query()->findOrFail($this->reportExportId);

        if ($report->status === 'completed') {
            return;
        }

        $report->update(['status' => 'processing', 'failure_reason' => null]);

        try {
            $files = $pdf->generate($report);

            $report->update([
                'status' => 'completed',
                'files' => $files,
                'completed_at' => now(),
                'expires_at' => now()->addDays(7),
            ]);

            $developer = Developer::query()->find($report->developer_id);

            if ($developer) {
                Notification::make()
                    ->title('Your report PDFs are ready')
                    ->body($report->scope_name.' · '.$report->period_start->format('d M Y').' – '.$report->period_end->format('d M Y'))
                    ->success()
                    ->actions([
                        Action::make('view_reports')
                            ->label('Open Reports')
                            ->url('/developer/reports')
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($developer);
            }
        } catch (Throwable $exception) {
            $report->update([
                'status' => 'failed',
                'failure_reason' => class_basename($exception).': '.mb_substr($exception->getMessage(), 0, 700),
            ]);

            report($exception);

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        ReportExport::query()
            ->whereKey($this->reportExportId)
            ->whereNotIn('status', ['completed'])
            ->update([
                'status' => 'failed',
                'failure_reason' => class_basename($exception).': '.mb_substr($exception->getMessage(), 0, 700),
            ]);
    }
}
