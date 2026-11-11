<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DeveloperSyncStatus extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $modules = [
            'subscriptions' => 'Subscriptions',
            'resources' => 'Resources',
            'costs' => 'Costs',
            'security' => 'Security',
            'alerts' => 'Alerts',
            'web_urls' => 'Web URLs',
        ];

        $stats = [];

        foreach ($modules as $key => $label) {
            $latest = DB::table('subscription_module_syncs')
                ->where('module', $key)
                ->orderByDesc('updated_at')
                ->first();

            $lastSuccess = DB::table('subscription_module_syncs')
                ->where('module', $key)
                ->max('last_successful_at');

            $status = $latest?->status ?? 'never';
            $description = $lastSuccess
                ? 'Last successful: ' . \Illuminate\Support\Carbon::parse($lastSuccess)->format('d M Y, H:i')
                : 'No successful sync recorded';

            if ($status === 'failed') {
                $description = 'Latest status failed · ' . $description;
            } elseif ($status === 'running') {
                $description = 'A sync is currently running · ' . $description;
            } elseif ($status === 'never') {
                $description = 'No sync status recorded yet';
            }

            $stats[] = Stat::make($label, strtoupper($status))
                ->description($description)
                ->icon(match ($status) {
                    'success' => 'heroicon-o-check-circle',
                    'running' => 'heroicon-o-arrow-path',
                    'failed' => 'heroicon-o-exclamation-circle',
                    default => 'heroicon-o-clock',
                })
                ->color(match ($status) {
                    'success' => 'success',
                    'running' => 'warning',
                    'failed' => 'danger',
                    default => 'gray',
                });
        }

        $reportPath = storage_path('logs/scheduler/reports/latest.html');
        $reportAvailable = is_file($reportPath);

        $stats[] = Stat::make('Latest Sync Report', $reportAvailable ? 'Available' : 'Not generated')
            ->description($reportAvailable ? 'Open the latest full scheduler report' : 'Run sync-all.php to generate a report')
            ->icon('heroicon-o-document-chart-bar')
            ->color($reportAvailable ? 'success' : 'gray')
            ->url($reportAvailable ? route('developer.sync-report') : null);

        return $stats;
    }
}
