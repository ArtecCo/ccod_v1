<?php

namespace App\Filament\Resources\ShipLogReleases\Pages;

use App\Filament\Resources\ShipLogReleases\ShipLogReleaseResource;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Pages\EditRelease;

class EditShipLogRelease extends EditRelease
{
    protected static string $resource = ShipLogReleaseResource::class;

    protected function getRedirectUrl(): string
    {
        return ShipLogReleaseResource::getUrl('index');
    }
}
