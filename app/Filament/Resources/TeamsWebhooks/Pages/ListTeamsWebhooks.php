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
        return [CreateAction::make()];
    }
}
