<?php

namespace App\Filament\Resources\AzureSubscriptions\Pages;

use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionBudgetDetails;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionCostSummary;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionResourceCostBreakdown;
use App\Filament\Resources\AzureSubscriptions\Widgets\SubscriptionStatsOverview;
use App\Filament\Resources\SubscriptionDocumentations\SubscriptionDocumentationResource;
use App\Models\SubscriptionDocumentation;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

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

                        Tab::make('Documentation')
                            ->schema([
                                Section::make('Subscription Documentation')
                                    ->description('Use the visual editor to maintain the documentation for this subscription.')
                                    ->schema([
                                        Actions::make([
                                            Action::make('editDocumentation')
                                                ->label(function (): string {
                                                    return $this->record->documentation === null
                                                        ? 'Add Documentation'
                                                        : 'Edit Documentation';
                                                })
                                                ->icon('heroicon-o-document-text')
                                                ->disabled(function (): bool {
                                                    $user = auth()->user();
                                                    $documentation = $this->record->documentation;

                                                    if (! $user) {
                                                        return true;
                                                    }

                                                    return $documentation
                                                        ? ! $user->can('update', $documentation)
                                                        : ! $user->can('createForSubscription', $this->record);
                                                })
                                                ->tooltip(function (): ?string {
                                                    $user = auth()->user();
                                                    $documentation = $this->record->documentation;

                                                    if (! $user) {
                                                        return 'You are not permitted to edit this documentation.';
                                                    }

                                                    $allowed = $documentation
                                                        ? $user->can('update', $documentation)
                                                        : $user->can('createForSubscription', $this->record);

                                                    return $allowed
                                                        ? null
                                                        : 'You are not permitted to edit this documentation.';
                                                })
                                                ->url(function (): ?string {
                                                    $documentation = $this->record->documentation;

                                                    return $documentation
                                                        ? SubscriptionDocumentationResource::getUrl('design', ['record' => $documentation])
                                                        : null;
                                                })
                                                ->action(function (): void {
                                                    $user = auth()->user();

                                                    abort_unless(
                                                        $user?->can('createForSubscription', $this->record),
                                                        403
                                                    );

                                                    $documentation = SubscriptionDocumentation::firstOrCreate([
                                                        'subscription_id' => $this->record->subscription_id,
                                                    ]);

                                                    redirect()->to(
                                                        SubscriptionDocumentationResource::getUrl('design', [
                                                            'record' => $documentation,
                                                        ])
                                                    );
                                                }),
                                        ])
                                            ->columnSpanFull(),
                                    ])
                                    ->columnSpanFull(),
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
            EditAction::make(),
        ];
    }
}
