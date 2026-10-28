<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityScores\Pages;

use App\Filament\Resources\AzureSecurityScores\AzureSecurityScoreResource;
use Filament\Resources\Pages\ListRecords;

class ListAzureSecurityScores extends ListRecords
{
    protected static string $resource = AzureSecurityScoreResource::class;
}
