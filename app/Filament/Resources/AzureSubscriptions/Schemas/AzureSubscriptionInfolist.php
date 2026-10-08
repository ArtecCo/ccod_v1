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
            Section::make()
                ->columns(3)
                ->schema([
                    TextEntry::make('display_name')
                        ->label('Subscription Name'),
                    TextEntry::make('subscription_id')
                        ->label('Subscription ID')
                        ->copyable(),
                    TextEntry::make('last_synced')
                        ->label('Last Synced')
                        ->dateTime('d M Y, H:i')
                        ->placeholder('—'),
                ]),

            Section::make()
                ->columns(3)
                ->schema([
                    TextEntry::make('security_score')
                        ->label('Security Score')
                        ->numeric(decimalPlaces: 2)
                        ->suffix('%')
                        ->placeholder('—'),
                    TextEntry::make('health_status')
                        ->label('Health')
                        ->color(fn (?string $state): string => match (strtolower($state ?? '')) {
                            'healthy' => 'success',
                            'warning' => 'warning',
                            'critical', 'unhealthy' => 'danger',
                            default => 'gray',
                        })
                        ->weight('bold')
                        ->placeholder('—'),
                    TextEntry::make('mtd_spend_eur')
                        ->label('Month-to-Date Cost')
                        ->money('EUR')
                        ->placeholder('—'),
                ]),
        ]);
    }
}
