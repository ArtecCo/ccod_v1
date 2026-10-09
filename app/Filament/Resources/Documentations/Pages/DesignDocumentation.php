<?php

namespace App\Filament\Resources\Documentations\Pages;

use App\Filament\Resources\Documentations\DocumentationResource;
use CarlJanzell\FilamentPageBuilder\Filament\Pages\DesignPage as BaseDesignPage;

class DesignDocumentation extends BaseDesignPage
{
    protected static string $resource = DocumentationResource::class;
}
