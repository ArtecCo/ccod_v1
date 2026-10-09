<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $projectId = DB::table('projects')->insertGetId([
            'name' => 'MA-BDO',
            'description' => 'MA-BDO applications and Azure environments.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $applications = [
            'eFOCuS' => 'eFOCuS application environments.',
            'Extra' => 'Extra application environments.',
            'CloudPrinting' => 'CloudPrinting application environments.',
        ];

        foreach ($applications as $name => $description) {
            DB::table('applications')->insert([
                'project_id' => $projectId,
                'name' => $name,
                'description' => $description,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $applicationIds = DB::table('applications')
            ->where('project_id', $projectId)
            ->pluck('id', 'name');

        $subscriptions = DB::table('azure_subscriptions')
            ->select('subscription_id', 'display_name')
            ->get();

        foreach ($subscriptions as $subscription) {
            $displayName = strtolower($subscription->display_name);
            $applicationName = null;

            if (str_contains($displayName, 'efocus')) {
                $applicationName = 'eFOCuS';
            } elseif (str_contains($displayName, 'extra')) {
                $applicationName = 'Extra';
            } elseif (str_contains($displayName, 'cloudprinting')) {
                $applicationName = 'CloudPrinting';
            }

            if ($applicationName === null) {
                continue;
            }

            $environment = match (true) {
                str_contains($displayName, 'prod'),
                str_contains($displayName, 'production') => 'PROD',
                str_contains($displayName, 'qa') => 'QA',
                str_contains($displayName, 'dev') => 'DEV',
                default => null,
            };

            if ($environment === null) {
                continue;
            }

            DB::table('azure_subscriptions')
                ->where('subscription_id', $subscription->subscription_id)
                ->update([
                    'application_id' => $applicationIds[$applicationName],
                    'environment' => $environment,
                ]);
        }
    }

    public function down(): void
    {
        $projectId = DB::table('projects')
            ->where('name', 'MA-BDO')
            ->value('id');

        if ($projectId === null) {
            return;
        }

        DB::table('azure_subscriptions')
            ->whereIn('application_id', function ($query) use ($projectId): void {
                $query->select('id')
                    ->from('applications')
                    ->where('project_id', $projectId);
            })
            ->update([
                'application_id' => null,
                'environment' => null,
            ]);

        DB::table('applications')->where('project_id', $projectId)->delete();
        DB::table('projects')->where('id', $projectId)->delete();
    }
};
