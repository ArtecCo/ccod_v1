<?php

namespace App\Filament\Resources\AccessRequests\Pages;

use App\Filament\Resources\AccessRequests\AccessRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAccessRequest extends ViewRecord
{
    protected static string $resource = AccessRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
