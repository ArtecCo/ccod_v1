<?php

namespace App\Filament\Resources\TeamsWebhookTemplates\Pages;

use App\Filament\Resources\TeamsWebhookTemplates\TeamsWebhookTemplateResource;
use App\Filament\Resources\TeamsWebhookCategories\TeamsWebhookCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTeamsWebhookTemplates extends ListRecords
{
    protected static string $resource = TeamsWebhookTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageCategories')->label('Manage categories')->icon('heroicon-o-rectangle-stack')->url(TeamsWebhookCategoryResource::getUrl('index')),
            CreateAction::make(),
        ];
    }
}
