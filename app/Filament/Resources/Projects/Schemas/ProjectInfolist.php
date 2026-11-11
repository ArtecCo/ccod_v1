<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name'),
            TextEntry::make('businessUnit.name')->label('Business Unit'),
            TextEntry::make('description')->placeholder('—')->columnSpanFull(),
            RepeatableEntry::make('subscriptionAssignments')->label('Subscriptions')->schema([
                TextEntry::make('tags')
                    ->label('Tags')
                    ->formatStateUsing(fn ($state): string => strtoupper(implode(', ', (array) $state)))
                    ->badge()
                    ->placeholder('—'),
                TextEntry::make('subscription.display_name')->label('Subscription'),
                TextEntry::make('subscription_id')->label('Subscription ID')->copyable(),
            ])->columns(3)->columnSpanFull(),
            TextEntry::make('created_at')->dateTime(),
            TextEntry::make('updated_at')->dateTime(),
        ]);
    }
}
