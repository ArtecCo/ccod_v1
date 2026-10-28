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

    /**
     * This resource intentionally has no index/list page. The assessment
     * details page is reached from the subscription Security tab, so using
     * ViewRecord's default breadcrumbs would try to generate a nonexistent
     * resource index URL and cause a 500 during rendering.
     */
    public function getBreadcrumbs(): array
    {
        return [$this->getTitle()];
    }
}
