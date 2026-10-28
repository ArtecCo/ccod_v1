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

    /**
     * Security assessments are detail-only records and intentionally have no
     * standalone index page. Build the breadcrumb trail from the subscription
     * resource instead of asking Filament to generate a nonexistent
     * AzureSecurityAssessmentResource::getUrl('index') URL.
     */
    public function getBreadcrumbs(): array
    {
        $subscription = $this->record->subscription;
        $subscriptionUrl = AzureSubscriptionResource::getUrl('view', [
            'record' => $subscription,
            'tab' => 'Security',
        ]);

        return [
            'Subscriptions' => AzureSubscriptionResource::getUrl('index'),
            $subscription->display_name ?? 'Subscription' => $subscriptionUrl,
            'Security' => $subscriptionUrl,
            $this->getTitle(),
        ];
    }
}
