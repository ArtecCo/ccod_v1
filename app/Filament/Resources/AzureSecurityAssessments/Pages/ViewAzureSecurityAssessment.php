<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityAssessments\Pages;

use App\Filament\Resources\AzureSecurityAssessments\AzureSecurityAssessmentResource;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewAzureSecurityAssessment extends ViewRecord
{
    protected static string $resource = AzureSecurityAssessmentResource::class;

    public function getTitle(): string
    {
        return $this->record->recommendation_name ?? 'Security Recommendation';
    }

    public function getBreadcrumbs(): array
    {
        $subscription = $this->record->subscription;
        $securityUrl = AzureSubscriptionResource::getUrl('view', [
            'record' => $subscription,
            'tab' => 'Security',
        ]);

        return [
            'Subscriptions' => AzureSubscriptionResource::getUrl('index'),
            $subscription->display_name ?? 'Subscription' => $securityUrl,
            'Security' => $securityUrl,
            $this->getTitle(),
        ];
    }

    protected function getHeaderActions(): array
    {
        $subscription = $this->record->subscription;

        return [
            Action::make('backToSecurity')
                ->label('Back to Security')
                ->icon(Heroicon::OutlinedArrowLeft)
                ->url(AzureSubscriptionResource::getUrl('view', [
                    'record' => $subscription,
                    'tab' => 'Security',
                ])),
        ];
    }
}
