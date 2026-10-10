<?php

namespace App\Filament\Resources\ShipLogReleases\Pages;

use App\Filament\Resources\ShipLogReleases\ShipLogReleaseResource;
use Ysfkaya\ShipLog\Filament\Resources\Releases\Pages\ListReleases;

class ListShipLogReleases extends ListReleases
{
    protected static string $resource = ShipLogReleaseResource::class;
}
