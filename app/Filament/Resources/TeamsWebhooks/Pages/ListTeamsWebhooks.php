<?php

namespace App\Filament\Resources\TeamsWebhooks\Pages;

use App\Filament\Resources\TeamsWebhooks\TeamsWebhookResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeamsWebhooks extends ListRecords
{
    protected static string $resource = TeamsWebhookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalHeading('Create webhook destination')
                ->modalSubmitActionLabel('Create destination')
                ->mutateDataUsing(function (array $data): array {
                    // Retain compatibility with legacy non-null database columns.
                    $data['category'] = 'server_start';
                    $data['template_id'] = null;
                    $data['event_keys'] = null;
                    $data['conditions'] = null;
                    $data['condition_mode'] = 'all';
                    $data['title_template'] = 'CCOD notification';
                    $data['message_template'] = 'Event: {event_label}';
                    $data['message_format'] = 'adaptive_card';

                    return $data;
                }),
        ];
    }
}
