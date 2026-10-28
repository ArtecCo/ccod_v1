<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityAssessments\Pages;

use App\Filament\Resources\AzureSecurityAssessments\AzureSecurityAssessmentResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAzureSecurityAssessment extends ViewRecord
{
    protected static string $resource = AzureSecurityAssessmentResource::class;

    public function getTitle(): string
    {
        return $this->record->recommendation_name ?? 'Security Recommendation';
    }
}
