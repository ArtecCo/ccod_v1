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
     * This resource is detail-only. It has no standalone index page, so the
     * breadcrumb must not ask Filament to generate an index URL and must not
     * imply that the recommendation is a child page of a subscription.
     */
    public function getBreadcrumbs(): array
    {
        return [
            'Security Recommendations',
            $this->getTitle(),
        ];
    }
}
