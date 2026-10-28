<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityAssessments\Pages;

use App\Filament\Resources\AzureSecurityAssessments\AzureSecurityAssessmentResource;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAzureSecurityAssessment extends ViewRecord
{
    protected static string $resource = AzureSecurityAssessmentResource::class;

    public function getTitle(): string
    {
        return $this->record->recommendation_name ?? 'Security Recommendation';
    }

    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();

        $securityUrl = AzureSubscriptionResource::getUrl('view', [
            'record' => $this->record->subscription_id,
            'tab' => 'Security',
        ]);

        $securityLabel = AzureSecurityAssessmentResource::getPluralModelLabel();
        $updated = [];

        foreach ($breadcrumbs as $url => $label) {
            if ($label === $securityLabel) {
                $updated[$securityUrl] = $label;
                continue;
            }

            $updated[$url] = $label;
        }

        return $updated;
    }
}
