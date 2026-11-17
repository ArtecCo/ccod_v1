<?php

namespace App\Filament\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function mount(): void
    {
        $user = auth()->guard('web')->user();

        abort_unless($user instanceof User, 403);

        if (! session()->has('auth_session_started_at')) {
            session()->put('auth_session_started_at', now()->timestamp);
        }

        // Temporarily use the first subscription the signed-in user can access.
        // Redirect to its actual ViewAzureSubscription page so the dashboard
        // renders the exact same tabs, components, layout, and permissions.
        $subscription = app(AccessAuthorizationService::class)
            ->accessibleSubscriptionQuery($user)
            ->first();

        abort_unless($subscription !== null, 404);

        $this->redirect(AzureSubscriptionResource::getUrl('view', [
            'record' => $subscription,
        ]));
    }

    public function getWidgets(): array
    {
        return [];
    }
}
