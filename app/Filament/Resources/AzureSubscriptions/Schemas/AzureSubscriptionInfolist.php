<?php

namespace App\Filament\Resources\AzureSubscriptions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AzureSubscriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('subscription_id'),
                TextEntry::make('display_name'),
                TextEntry::make('health_status')
                    ->placeholder('-'),
                TextEntry::make('security_score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('mtd_spend_eur')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('last_synced')
                    ->dateTime(),
            ]);
    }
}
