<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project Information')
                ->schema([
                    TextEntry::make('name')->label('Project Name')->weight('bold'),
                    TextEntry::make('businessUnit.name')->label('Business Unit')->placeholder('—'),
                    TextEntry::make('description')->label('Description')->placeholder('No description provided.')->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('User Groups')
                ->description('Groups assigned access to this project.')
                ->schema([
                    RepeatableEntry::make('userGroups')
                        ->schema([
                            TextEntry::make('name')
                                ->label('Group')
                                ->badge()
                                ->color('gray'),
                        ])
                        ->columns(1)
                        ->grid(3)
                        ->placeholder('No user groups assigned.')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Subscriptions')
                ->description('Azure subscriptions assigned to this project.')
                ->schema([
                    RepeatableEntry::make('subscriptionAssignments')
                        ->schema([
                            TextEntry::make('subscription.display_name')
                                ->label('Subscription')
                                ->weight('bold')
                                ->columnSpanFull(),
                            TextEntry::make('subscription_id')
                                ->label('Subscription ID')
                                ->copyable()
                                ->columnSpanFull(),
                            TextEntry::make('tags')
                                ->label('Tags')
                                ->formatStateUsing(fn ($state): string => strtoupper(implode(', ', (array) $state)))
                                ->badge()
                                ->placeholder('No tags')
                                ->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->grid(2)
                        ->placeholder('No subscriptions assigned.')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Record Details')
                ->schema([
                    TextEntry::make('created_at')->label('Created')->dateTime(),
                    TextEntry::make('updated_at')->label('Last Updated')->dateTime(),
                ])
                ->columns(2)
                ->columnSpanFull()
                ->collapsed(),
        ]);
    }
}
