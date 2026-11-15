<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SyncReport extends Page
{
    protected static ?string $slug = 'sync-report';

    protected static ?string $title = 'Azure synchronization report';

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.sync-report';

    public array $report = [];

    public function mount(): void
    {
        abort_unless(auth('developers')->user()?->is_active === true, 403);

        $path = storage_path('logs/scheduler/reports/latest.json');

        abort_unless(is_file($path), 404, 'No synchronization report has been generated yet.');

        $report = json_decode((string) file_get_contents($path), true);

        abort_unless(is_array($report) && isset($report['modules']), 404, 'The synchronization report is invalid.');

        $this->report = $report;
    }

    public function getHeading(): string
    {
        return 'Azure synchronization report';
    }

    public function getSubheading(): ?string
    {
        return 'Run '.($this->report['run_id'] ?? '—').' · Completed '.($this->report['completed_at'] ?? '—');
    }
}
