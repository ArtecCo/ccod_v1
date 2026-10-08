<?php

namespace App\Filament\Resources\AzureSubscriptions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AzureSubscriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(3)
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
        ]);
    }
}
