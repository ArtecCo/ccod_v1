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
                    TextEntry::make('overview_tags')
                        ->label('Tags')
                        ->badge()
                        ->state(function ($record): array {
                            return $record->projects()
                                ->with('subscriptionAssignments')
                                ->get()
                                ->flatMap(fn ($project) => $project->subscriptionAssignments
                                    ->where('subscription_id', $record->subscription_id)
                                    ->flatMap(fn ($assignment) => (array) $assignment->tags))
                                ->filter()
                                ->map(fn ($tag): string => strtoupper((string) $tag))
                                ->unique()
                                ->sort()
                                ->values()
                                ->all();
                        })
                        ->placeholder('No tags assigned'),
                    TextEntry::make('overview_user_groups')
                        ->label('Assigned User Groups')
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
                        ->placeholder('No user groups assigned')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ]);
    }
}
