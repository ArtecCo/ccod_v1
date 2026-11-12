<?php

namespace App\\Filament\\Resources\\TeamsWebhookCategories\\Pages;

use App\\Filament\\Resources\\TeamsWebhookCategories\\TeamsWebhookCategoryResource;
use Filament\\Actions\\CreateAction;
use Filament\\Resources\\Pages\\ListRecords;

class ListTeamsWebhookCategories extends ListRecords
{
    protected static string $resource = TeamsWebhookCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
