<?php

namespace App\\Filament\\GlobalSearch;

use Filament\\GlobalSearch\\GlobalSearchResults;
use Filament\\GlobalSearch\\Providers\\Contracts\\GlobalSearchProvider;

class NavigationOnlyGlobalSearchProvider implements GlobalSearchProvider
{
    public function getResults(string $query): ?GlobalSearchResults
    {
        // Keep Filament's native search field, but do not run record searches
        // or display the "No results found" dropdown. The existing field is
        // used by the admin panel's client-side navigation filter instead.
        return null;
    }
}
