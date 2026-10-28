<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityAssessments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AzureSecurityAssessmentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Recommendation')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('recommendation_name')->label('Recommendation')->columnSpan(2),
                        TextEntry::make('severity')->badge(),
                        TextEntry::make('state')->badge()->color('danger'),
                        TextEntry::make('assessment_type')->label('Assessment Type'),
                        TextEntry::make('implementation_effort')->label('Implementation Effort'),
                        TextEntry::make('user_impact')->label('User Impact'),
                        TextEntry::make('resource_id')
                            ->label('Affected Resource')
                            ->copyable()
                            ->columnSpanFull(),
                    ]),
                ]),
            Section::make('Description')
                ->schema([
                    TextEntry::make('description')->hiddenLabel()->columnSpanFull(),
                ])
                ->columnSpanFull(),
            Section::make('CVEs')
                ->schema([
                    TextEntry::make('cves')
                        ->hiddenLabel()
                        ->formatStateUsing(function (mixed $state): string {
                            if (is_array($state)) {
                                $cves = array_values(array_filter($state, 'is_string'));
                                return $cves !== []
                                    ? implode(', ', $cves)
                                    : 'No CVEs identified for this recommendation.';
                            }

                            return is_string($state) && $state !== ''
                                ? $state
                                : 'No CVEs identified for this recommendation.';
                        }),
                ])
                ->columnSpanFull(),
            Section::make('Remediation')
                ->schema([
                    TextEntry::make('remediation_description')->hiddenLabel()->columnSpanFull(),
                ])
                ->columnSpanFull(),
            Section::make('Evaluation')
                ->schema([
                    Grid::make(3)->schema([
                        TextEntry::make('status_cause')->label('Status Cause'),
                        TextEntry::make('first_evaluated_at')->label('First Evaluated')->dateTime('d M Y, H:i'),
                        TextEntry::make('status_changed_at')->label('Status Changed')->dateTime('d M Y, H:i'),
                        TextEntry::make('status_description')->label('Status Details')->columnSpanFull(),
                        TextEntry::make('portal_link')
                            ->label('Azure Portal')
                            ->url(fn (mixed $state): ?string => is_string($state) && $state !== '' ? $state : null)
                            ->openUrlInNewTab()
                            ->formatStateUsing(fn (mixed $state): string => is_string($state) && $state !== '' ? 'Open in Azure Portal' : '—'),
                    ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}
