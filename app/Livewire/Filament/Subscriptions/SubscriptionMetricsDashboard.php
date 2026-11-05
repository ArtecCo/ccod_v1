<?php

declare(strict_types=1);

namespace App\Livewire\Filament\Subscriptions;

use App\Models\AzureSubscription;
use Livewire\Component;

class SubscriptionMetricsDashboard extends Component
{
    public AzureSubscription $record;

    public function mount(AzureSubscription $record): void
    {
        $this->record = $record;
    }

    public function render()
    {
        return view('livewire.filament.subscriptions.metrics-dashboard', [
            'subscriptionId' => $this->record->subscription_id,
        ]);
    }
}
