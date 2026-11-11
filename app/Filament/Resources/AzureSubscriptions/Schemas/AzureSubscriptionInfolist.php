<?php

namespace App\\Filament\\Resources\\AzureSubscriptions\\Schemas;

use Filament\\Infolists\\Components\\TextEntry;
use Filament\\Schemas\\Components\\Grid;
use Filament\\Schemas\\Components\\Section;
use Filament\\Schemas\\Schema;

class AzureSubscriptionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Subscription')
                ->schema([
                    Grid::make(3)
                        ->schema([
                            TextEntry::make('display_name')
                                ->label('Subscription Name')
                                ->weight('bold'),
                            TextEntry::make('subscription_id')
                                ->label('Subscription ID')
                                ->copyable()
                                ->copyMessage('Subscription ID copied'),
                            TextEntry::make('overview_business_units')
                                ->label('Business Unit')
                                ->state(function ($record): string {
                                    $direct = $record->businessUnits()->pluck('business_units.name');
                                    $projectUnits = $record->projects()
                                        ->with('businessUnit:id,name')
                                        ->get()
                                        ->pluck('businessUnit.name');

                                    return $direct->merge($projectUnits)
                                        ->filter()
                                        ->unique()
                                        ->sort()
                                        ->implode(', ') ?: '—';
                                }),
                        ]),
                ])
                ->columnSpanFull(),
            Section::make('Assigned User Groups')
                ->schema([
                    TextEntry::make('overview_user_groups')
                        ->hiddenLabel()
                        ->badge()
                        ->color('info')
                        ->state(function ($record): array {
                            return $record->projects()
                                ->with('userGroups:id,name')
                                ->get()
                                ->flatMap(fn ($project) => $project->userGroups->pluck('name'))
                                ->filter()
                                ->unique()
                                ->sort()
                                ->values()
                                ->all();
                        })
                        ->placeholder('No user groups assigned'),
                ])
                ->columnSpanFull(),
        ]);
    }
}
