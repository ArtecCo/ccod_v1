<?php

namespace App\Filament\Resources\AccessRequests\Pages;

use App\Filament\Resources\AccessRequests\AccessRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListAccessRequests extends ListRecords
{
    protected static string $resource = AccessRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
