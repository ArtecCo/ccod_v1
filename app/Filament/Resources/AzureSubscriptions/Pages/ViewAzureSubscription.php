<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\AzureSubscriptions\RelationManagers\SubscriptionWebUrlsRelationManager;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionBudgetDetails;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionCostSummary;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionResourceCostBreakdown;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionStatsOverview;
use App\Models\SubscriptionWebUrl;
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

class ViewAzureSubscription extends ViewRecord
{
    protected static string $resource = AzureSubscriptionResource::class;

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Subscription Navigation')
                    ->tabs([
                        Tab::make('Overview')
                            ->schema([
                                Section::make('Subscription Details')
                                    ->schema([
                                        $this->getInfolistContentComponent()
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),

                                Livewire::make(SubscriptionStatsOverview::class, [
                                    'record' => $this->record,
                                ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        Tab::make('Security')
                            ->schema([]),

                        Tab::make('Cost')
                            ->schema([
                                Livewire::make(SubscriptionCostSummary::class, [
                                    'record' => $this->record,
                                ])
                                    ->columnSpanFull(),

                                Livewire::make(SubscriptionBudgetDetails::class, [
                                    'record' => $this->record,
                                ])
                                    ->columnSpanFull(),

                                Livewire::make(SubscriptionResourceCostBreakdown::class, [
                                    'record' => $this->record,
                                ])
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        Tab::make('Resources')
                            ->schema([
                                $this->getRelationManagersContentComponent()
                                    ->columnSpanFull(),
                            ])
                            ->columns(1),

                        Tab::make('Web URLs')
                            ->schema([
                                Livewire::make(SubscriptionWebUrlsRelationManager::class, [
                                    'ownerRecord' => $this->record,
                                    'pageClass' => static::class,
                                ])->columnSpanFull(),
                            ])
                            ->columns(1),

                        Tab::make('Tickets')
                            ->schema([]),

                        Tab::make('Alerts')
                            ->schema([]),
                    ])
                    ->persistTabInQueryString()
                    ->contained(false)
                    ->extraAttributes([
                        'class' => '!bg-transparent !border-0 !shadow-none !ring-0 !rounded-none',
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addWebUrl')
                ->label('Add Web URL')
                ->icon(Heroicon::OutlinedGlobeAlt)
                ->visible(fn (): bool => Gate::forUser(auth()->user())->allows(
                    'create',
                    [SubscriptionWebUrl::class, $this->record],
                ))
                ->modalHeading('Add Web URL')
                ->modalSubmitActionLabel('Add URL')
                ->form([
                    TextInput::make('url')
                        ->label('Web URL')
                        ->url()
                        ->required()
                        ->maxLength(2048),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])
                ->action(function (array $data): void {
                    $url = rtrim(trim($data['url']), '/');

                    $this->record->webUrls()->create([
                        'url' => $url,
                        'url_hash' => hash('sha256', $url),
                        'is_active' => (bool) ($data['is_active'] ?? true),
                    ]);

                    $this->dispatch('web-url-added');
                })
                ->successNotificationTitle('Web URL added'),
            EditAction::make(),
        ];
    }
}
