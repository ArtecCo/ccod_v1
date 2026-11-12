<?php

namespace App\Filament\Resources\TeamsWebhooks\Pages;

use App\Filament\Resources\TeamsWebhooks\TeamsWebhookResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamsWebhook extends CreateRecord
{
    protected static string $resource = TeamsWebhookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $category = $data['category'] ?? 'server_start';
        $data['message_format'] = ($data['destination_type'] ?? null) === 'chat' ? 'plain_text' : ($data['message_format'] ?? 'adaptive_card');

        if (trim((string) ($data['title_template'] ?? '')) === '') {
            $data['title_template'] = $category === 'server_start' ? 'CCOD server startup' : 'CCOD Azure sync status';
        }

        if (trim((string) ($data['message_template'] ?? '')) === '') {
            $data['message_template'] = $category === 'server_start'
                ? "Server boot: {server_boot_time}\nIIS: {iis_status} — {iis_started_at}\nPHP/FastCGI: {php_status} — {php_started_at}\nMySQL2610: {mysql_status} — {mysql_started_at}\nLaravel queue: {queue_status} — {queue_started_at}"
                : "Overall sync: {overall_status}\nSubscriptions: {subscriptions_status}\nResources: {resources_status}\nCosts: {costs_status}\nSecurity: {security_status}\nAlerts: {alerts_status}\nWeb URLs: {web_urls_status}\nCompleted: {completed_at}";
        }

        return $data;
    }
}
