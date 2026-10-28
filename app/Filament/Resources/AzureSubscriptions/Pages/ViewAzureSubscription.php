<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\AzureAlertsRelationManager;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\AzureSecurityAssessmentsRelationManager;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\SubscriptionWebUrlsRelationManager;
use App\Models\Developer;
use App\Models\SubscriptionWebUrl;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Gate;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionAlertSeverityOverview;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionBudgetDetails;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionCostHistoryChart;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionCostSummary;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionResourceCostBreakdown;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionSecurityOverview;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionSecurityScoreHistoryChart;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionStatsOverview;

class ViewAzureSubscription extends ViewRecord
{
    protected static string $resource = AzureSubscriptionResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $tabPermissions = [
            'Alerts' => 'alerts.view',
            'Security' => 'security.view',
            'Tickets' => 'tickets.view',
            'Web URLs' => 'urls.view',
            'Resources' => 'resources.view',
            'Overview' => null,
            'Cost' => 'cost.view',
        ];

        $requestedTab = request()->query('tab');
        $permission = $requestedTab !== null ? ($tabPermissions[$requestedTab] ?? null) : null;

        if ($permission !== null && ! $this->canViewTab($permission)) {
            abort(403);
        }
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Subscription Navigation')->tabs([
                $this->makeTab('Overview', null)->schema(fn (): array => [Section::make('Subscription Details')->schema([$this->getInfolistContentComponent()->columnSpanFull()])->columnSpanFull(), Livewire::make(SubscriptionStatsOverview::class, ['record' => $this->record])->lazy()->columnSpanFull()])->columns(1),
                $this->makeTab('Cost', 'cost.view')->schema(fn (): array => array_values(array_filter([
                    Livewire::make(SubscriptionCostSummary::class, ['record' => $this->record])->lazy()->columnSpanFull(),
                    Livewire::make(SubscriptionCostHistoryChart::class, ['record' => $this->record])->lazy()->columnSpan(1),
                    Livewire::make(SubscriptionBudgetDetails::class, ['record' => $this->record])->lazy()->columnSpanFull(),
                    $this->canViewTab('cost.breakdown.view')
                        ? Livewire::make(SubscriptionResourceCostBreakdown::class, ['record' => $this->record])->lazy()->columnSpanFull()
                        : null,
                ])))->columns(2),
                $this->makeTab('Alerts', 'alerts.view')->schema(fn (): array => [Livewire::make(SubscriptionAlertSeverityOverview::class, ['record' => $this->record])->lazy()->columnSpanFull(), Livewire::make(AzureAlertsRelationManager::class, ['ownerRecord' => $this->record, 'pageClass' => static::class])->lazy()->columnSpanFull()])->columns(1),
                $this->makeTab('Security', 'security.view')->schema(fn (): array => [
                    Livewire::make(SubscriptionSecurityOverview::class, ['record' => $this->record])->lazy()->columnSpanFull(),
                    Livewire::make(SubscriptionSecurityScoreHistoryChart::class, ['record' => $this->record])->lazy()->columnSpan(1),
                    Livewire::make(AzureSecurityAssessmentsRelationManager::class, ['ownerRecord' => $this->record, 'pageClass' => static::class])->lazy()->columnSpanFull(),
                ])->columns(2),
                $this->makeTab('Tickets', 'tickets.view')->schema(fn (): array => [])->columns(1),
                $this->makeTab('Web URLs', 'urls.view')->schema(fn (): array => [Livewire::make(SubscriptionWebUrlsRelationManager::class, ['ownerRecord' => $this->record, 'pageClass' => static::class])->lazy()->columnSpanFull()])->columns(1),
                $this->makeTab('Resources', 'resources.view')->schema(fn (): array => [$this->getRelationManagersContentComponent()->columnSpanFull()])->columns(1),
            ])->persistTabInQueryString()->contained(false)->extraAttributes(['class' => '!bg-transparent !border-0 !shadow-none !ring-0 !rounded-none'])->columnSpanFull(),
        ]);
    }

    protected function makeTab(string $label, ?string $permission): Tab
    {
        $tab = Tab::make($label);
        if ($permission !== null) $tab->visible(fn (): bool => $this->canViewTab($permission));
        return $tab;
    }

    protected function canViewTab(string $permission): bool
    {
        $developer = auth()->guard('developers')->user();
        if ($developer instanceof Developer && $developer->is_active) return true;

        $user = auth()->guard('web')->user();
        return $user instanceof User && app(AccessAuthorizationService::class)->hasPermission($user, $permission, $this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addWebUrl')->label('Add Web URL')->icon(Heroicon::OutlinedGlobeAlt)
                ->visible(fn (): bool => Gate::forUser(auth()->user())->allows('create', [SubscriptionWebUrl::class, $this->record]))
                ->modalHeading('Add Web URL')->modalSubmitActionLabel('Add URL')
                ->form([TextInput::make('url')->label('Web URL')->url()->required()->maxLength(2048), Toggle::make('is_active')->label('Active')->default(true)])
                ->action(function (array $data): void {$url = rtrim(trim($data['url']), '/'); $this->record->webUrls()->create(['url' => $url, 'url_hash' => hash('sha256', $url), 'is_active' => (bool) ($data['is_active'] ?? true)]); $this->dispatch('web-url-added');})
                ->successNotificationTitle('Web URL added'),
            EditAction::make()->visible(fn (): bool => AzureSubscriptionResource::canEdit($this->record)),
        ];
    }
}
