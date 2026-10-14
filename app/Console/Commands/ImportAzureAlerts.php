<?php

namespace App\Console\Commands;

use App\Models\AzureAlert;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportAzureAlerts extends Command
{
    protected $signature = 'alerts:import {file}';

    protected $description = 'Import Azure Monitor alerts from a CSV file';

    public function handle(): int
    {
        $path = $this->argument('file');

        if (! is_file($path)) {
            $path = base_path($path);
        }

        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->error('Unable to open CSV file.');

            return self::FAILURE;
        }

        $headers = fgetcsv($handle);

        if ($headers === false) {
            fclose($handle);
            $this->error('CSV file is empty.');

            return self::FAILURE;
        }

        $required = [
            'Name',
            'Severity',
            'Affected resource',
            'Target resource type',
            'Alert condition',
            'User response',
            'Monitor service',
            'Signal type',
            'Fire time',
            'Last modified time',
            'Subscription',
            'Target resource group',
            'Suppression status',
        ];

        $missing = array_diff($required, $headers);

        if ($missing) {
            fclose($handle);
            $this->error('Missing CSV columns: '.implode(', ', $missing));

            return self::FAILURE;
        }

        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                $this->warn('Skipped malformed CSV row.');

                continue;
            }

            $data = array_combine($headers, $row);

            $subscriptionId = trim($data['Subscription']);

            $alertId = hash(
                'sha256',
                $subscriptionId.'|'.
                $data['Name'].'|'.
                $data['Affected resource'].'|'.
                $data['Fire time']
            );

            AzureAlert::updateOrCreate(
                ['azure_alert_id' => $alertId],
                [
                    'name' => $data['Name'] ?: null,
                    'severity' => $data['Severity'] ?: null,
                    'affected_resource' => $data['Affected resource'] ?: null,
                    'target_resource_type' => $data['Target resource type'] ?: null,
                    'alert_condition' => $data['Alert condition'] ?: null,
                    'user_response' => $data['User response'] ?: null,
                    'monitor_service' => $data['Monitor service'] ?: null,
                    'signal_type' => $data['Signal type'] ?: null,
                    'fire_time' => $data['Fire time']
                        ? Carbon::parse($data['Fire time'])
                        : null,
                    'last_modified_time' => $data['Last modified time']
                        ? Carbon::parse($data['Last modified time'])
                        : null,
                    'subscription_id' => $subscriptionId,
                    'target_resource_group' => $data['Target resource group'] ?: null,
                    'suppression_status' => $data['Suppression status'] ?: null,
                ]
            );

            $imported++;
        }

        fclose($handle);

        $this->info("Imported/updated {$imported} alert records.");

        return self::SUCCESS;
    }
}