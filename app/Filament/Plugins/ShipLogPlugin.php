<?php

namespace App\Filament\Plugins;

use App\Filament\Resources\ShipLogReleases\ShipLogReleaseResource;
use Filament\Panel;
use Ysfkaya\ShipLog\ShipLogPlugin as BaseShipLogPlugin;

class ShipLogPlugin extends BaseShipLogPlugin
{
    public function register(Panel $panel): void
    {
        $panel->pages([$this->getPage()]);

        if ($this->hasResource()) {
            $panel->resources([ShipLogReleaseResource::class]);
        }
    }
}
