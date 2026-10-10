<?php

namespace App\Filament\Resources\ShipLogReleases;

use App\Filament\Resources\ShipLogReleases\Pages\CreateShipLogRelease;
use App\Filament\Resources\ShipLogReleases\Pages\EditShipLogRelease;
use App\Filament\Resources\ShipLogReleases\Pages\ListShipLogReleases;
use Ysfkaya\ShipLog\Filament\Resources\Releases\ReleaseResource as BaseReleaseResource;

class ShipLogReleaseResource extends BaseReleaseResource
{
    public static function getPages(): array
    {
        return [
            'index' => ListShipLogReleases::route('/'),
            'create' => CreateShipLogRelease::route('/create'),
            'edit' => EditShipLogRelease::route('/{record}/edit'),
        ];
    }
}
