<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;

use App\Models\AzureSecurityAssessment;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AzureSecurityAssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'securityAssessments';
    protected static ?string $title = 'Security Recommendations';
    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->select([
                'id',
                'assessment_id',
                'recommendation_name',
                'state',
                'severity',
                'resource_id',
                'assessment_type',
                'implementation_effort',
                'user_impact',
                'status_cause',
                'first_evaluated_at',
                'status_changed_at',
                'remediation_description',
                'portal_link',
            ]))
            ->columns([
                TextColumn::make('recommendation_name')
                    ->label('Recommendation')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),
                TextColumn::make('state')
                    ->label('State')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Unhealthy' => 'danger',
                        'Healthy' => 'success',
                        'NotApplicable' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('severity')
                    ->label('Severity')
                    ->badge()
                    ->color(fn (?string $state): string => match (strtolower((string) $state)) {
                        'critical', 'high' => 'danger',
                        'medium' => 'warning',
                        'low' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('resource_id')
                    ->label('Affected Resource')
                    ->searchable()
                    ->limit(55)
                    ->toggleable(),
                TextColumn::make('assessment_type')
                    ->label('Assessment Type')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('implementation_effort')
                    ->label('Effort')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user_impact')
                    ->label('User Impact')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status_cause')
                    ->label('Status Cause')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('first_evaluated_at')
                    ->label('First Evaluated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status_changed_at')
                    ->label('Status Changed')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('remediation_description')
                    ->label('Remediation')
                    ->limit(70)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('portal_link')
                    ->label('Azure Portal')
                    ->url(fn (?string $state): ?string => $state)
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn (?string $state): string => $state ? 'Open' : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('state')->options([
                    'Unhealthy' => 'Unhealthy',
                    'Healthy' => 'Healthy',
                    'NotApplicable' => 'Not Applicable',
                ]),
                SelectFilter::make('severity')->options([
                    'Critical' => 'Critical',
                    'High' => 'High',
                    'Medium' => 'Medium',
                    'Low' => 'Low',
                ]),
            ])
            ->groups([
                Group::make('state')->label('State'),
                Group::make('severity')->label('Severity'),
            ])
            ->defaultSort('status_changed_at', 'desc')
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([]);
    }
}
