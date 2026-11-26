<?php

namespace App\Filament\GlobalSearch;

use Filament\GlobalSearch\GlobalSearchResults;
use Filament\GlobalSearch\Providers\Contracts\GlobalSearchProvider;

class NavigationOnlyGlobalSearchProvider implements GlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        // The native field is retained for its existing appearance and placement.
        // Navigation filtering is performed client-side; do not query records.
        return null;
    }
}
