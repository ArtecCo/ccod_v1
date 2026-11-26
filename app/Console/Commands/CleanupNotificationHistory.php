<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupNotificationHistory extends Command
{
    protected $signature = 'notifications:cleanup {--dry-run : Show what would be deleted without deleting anything}';

    protected $description = 'Remove notification and failed-job history older than seven days';

    public function handle(): int
    {
        $cutoff = now()->subDays(7);
        $dryRun = (bool) $this->option('dry-run');

        $notifications = DB::table('notifications')
            ->where('created_at', '<', $cutoff);

        // Only remove unreserved Teams webhook delivery jobs. Other queued
        // jobs and jobs currently reserved by a queue worker remain untouched.
        $teamsJobs = DB::table('jobs')
            ->where('created_at', '<', $cutoff->timestamp)
            ->whereNull('reserved_at')
            ->where('payload', 'like', '%DeliverTeamsWebhookNotification%');

        $failedJobs = DB::table('failed_jobs')
            ->where('failed_at', '<', $cutoff);

        $counts = [
            'in_app_notifications' => (clone $notifications)->count(),
            'queued_teams_notifications' => (clone $teamsJobs)->count(),
            'failed_jobs' => (clone $failedJobs)->count(),
        ];

        $this->info('Notification cleanup cutoff: '.$cutoff->toDateTimeString());
        $this->table(
            ['Category', 'Records '.($dryRun ? 'to delete' : 'deleted')],
            [
                ['In-app notifications', $counts['in_app_notifications']],
                ['Queued Teams notifications', $counts['queued_teams_notifications']],
                ['Failed jobs', $counts['failed_jobs']],
            ],
        );

        if ($dryRun) {
            $this->comment('Dry run only. No records were deleted.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($notifications, $teamsJobs, $failedJobs): void {
            $notifications->delete();
            $teamsJobs->delete();
            $failedJobs->delete();
        });

        Log::info('Notification history cleanup completed.', [
            'cutoff' => $cutoff->toDateTimeString(),
            'deleted' => $counts,
        ]);

        $this->info('Cleanup completed successfully.');

        return self::SUCCESS;
    }
}
