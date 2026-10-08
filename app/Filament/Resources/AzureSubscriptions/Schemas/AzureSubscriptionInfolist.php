<?php

namespace App\Filament\Resources\AzureSubscriptions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AzureSubscriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Subscription')
                ->columns(3)
                ->schema([
                    TextEntry::make('display_name')->label('Subscription Name'),
                    TextEntry::make('subscription_id')->label('Subscription ID')->copyable(),
                    TextEntry::make('key_vault_reference')->label('Key Vault Reference')->placeholder('—'),
                    TextEntry::make('teams.name')->label('Teams')->listWithLineBreaks()->columnSpanFull(),
                ]),
            Section::make('Operational Overview')
                ->columns(4)
                ->schema([
                    TextEntry::make('health_status')->badge()->placeholder('—'),
                    TextEntry::make('security_score')->numeric()->suffix('%')->placeholder('—'),
                    TextEntry::make('mtd_spend_eur')->money('EUR')->placeholder('—'),
                    TextEntry::make('last_synced')->dateTime()->placeholder('—'),
                ]),
            Section::make('Cost & Resources')
                ->columns(3)
                ->schema([
                    TextEntry::make('budgets_count')->label('Budgets')->state(fn ($record): int => $record->budgets()->count()),
                    TextEntry::make('billing_resources_count')->label('Resources')->state(fn ($record): int => $record->billingResources()->count()),
                    TextEntry::make('costForecast.forecast_amount')->label('Forecast')->money(fn ($record) => $record->costForecast?->currency ?: 'EUR')->placeholder('—'),
                ]),
        ]);
    }
}
