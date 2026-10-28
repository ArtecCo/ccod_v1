<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityScores\Pages;

use App\Filament\Resources\AzureSecurityScores\AzureSecurityScoreResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAzureSecurityScore extends ViewRecord
{
    protected static string $resource = AzureSecurityScoreResource::class;

    public function getTitle(): string
    {
        return $this->record->initiative_name ?? 'Security Score Control';
    }
}
