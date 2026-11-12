<?php

namespace App\Filament\Resources\TeamsWebhookCategories\Pages;

use App\Filament\Resources\TeamsWebhookCategories\TeamsWebhookCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditTeamsWebhookCategory extends EditRecord
{
    protected static string $resource = TeamsWebhookCategoryResource::class;

    protected function getRedirectUrl(): string
    {
        return TeamsWebhookCategoryResource::getUrl('index');
    }
}
