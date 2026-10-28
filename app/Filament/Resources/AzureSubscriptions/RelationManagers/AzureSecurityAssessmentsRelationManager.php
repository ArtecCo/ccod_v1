<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;

use App\Filament\Exports\AzureSecurityAssessmentExporter;
use App\Filament\Resources\AzureSecurityAssessments\AzureSecurityAssessmentResource;
use App\Models\AzureSecurityAssessment;
use App\Services\AccessAuthorizationService;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AzureSecurityAssessmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'securityAssessments';
    protected static ?string $title = 'Unhealthy Security Recommendations';
    protected static bool $isLazy = true;
    protected static ?string $relatedResource = AzureSecurityAssessmentResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query
                ->where('state', 'Unhealthy')
                ->select([
                    'id',
                    'assessment_id',
                    'subscription_id',
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
                TextColumn::make('recommendation_name')->label('Recommendation')->searchable()->sortable()->weight('medium')->wrap(),
                TextColumn::make('severity')->label('Severity')->badge()->color(fn (?string $state): string => match (strtolower((string) $state)) {
                    'critical', 'high' => 'danger',
                    'medium' => 'warning',
                    'low' => 'gray',
                    default => 'gray',
                })->sortable(),
                TextColumn::make('resource_id')->label('Affected Resource')->formatStateUsing(fn (?string $state): string => $this->resourceName($state))->searchable()->limit(60),
                TextColumn::make('status_changed_at')->label('Status Changed')->dateTime('d M Y, H:i')->sortable(),
                TextColumn::make('assessment_type')->label('Assessment Type')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('implementation_effort')->label('Effort')->badge()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user_impact')->label('User Impact')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status_cause')->label('Status Cause')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('first_evaluated_at')->label('First Evaluated')->dateTime('d M Y, H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('remediation_description')->label('Remediation')->limit(70)->wrap()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('portal_link')->label('Azure Portal')->url(fn (?string $state): ?string => $state)->openUrlInNewTab()->formatStateUsing(fn (?string $state): string => $state ? 'Open' : '—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('severity')->options([
                    'Critical' => 'Critical',
                    'High' => 'High',
                    'Medium' => 'Medium',
                    'Low' => 'Low',
                ]),
                SelectFilter::make('resource_id')
                    ->label('Affected Resource')
                    ->searchable()
                    ->options(fn (): array => AzureSecurityAssessment::query()
                        ->where('subscription_id', $this->getOwnerRecord()->subscription_id)
                        ->where('state', 'Unhealthy')
                        ->whereNotNull('resource_id')
                        ->distinct()
                        ->pluck('resource_id')
                        ->mapWithKeys(fn (string $resourceId): array => [$resourceId => $this->resourceName($resourceId)])
                        ->sort()
                        ->all()),
                SelectFilter::make('assessment_type')->label('Assessment Type')->multiple()->options(fn (): array => AzureSecurityAssessment::query()
                    ->where('subscription_id', $this->getOwnerRecord()->subscription_id)
                    ->where('state', 'Unhealthy')
                    ->whereNotNull('assessment_type')
                    ->distinct()
                    ->orderBy('assessment_type')
                    ->pluck('assessment_type', 'assessment_type')
                    ->all()),
                SelectFilter::make('implementation_effort')->label('Effort')->multiple()->options(fn (): array => AzureSecurityAssessment::query()
                    ->where('subscription_id', $this->getOwnerRecord()->subscription_id)
                    ->where('state', 'Unhealthy')
                    ->whereNotNull('implementation_effort')
                    ->distinct()
                    ->orderBy('implementation_effort')
                    ->pluck('implementation_effort', 'implementation_effort')
                    ->all()),
                SelectFilter::make('user_impact')->label('User Impact')->multiple()->options(fn (): array => AzureSecurityAssessment::query()
                    ->where('subscription_id', $this->getOwnerRecord()->subscription_id)
                    ->where('state', 'Unhealthy')
                    ->whereNotNull('user_impact')
                    ->distinct()
                    ->orderBy('user_impact')
                    ->pluck('user_impact', 'user_impact')
                    ->all()),
                SelectFilter::make('status_cause')->label('Status Cause')->multiple()->options(fn (): array => AzureSecurityAssessment::query()
                    ->where('subscription_id', $this->getOwnerRecord()->subscription_id)
                    ->where('state', 'Unhealthy')
                    ->whereNotNull('status_cause')
                    ->distinct()
                    ->orderBy('status_cause')
                    ->pluck('status_cause', 'status_cause')
                    ->all()),
            ])
            ->groups([
                Group::make('severity')->label('Severity'),
                Group::make('resource_id')->label('Affected Resource')->getTitleFromRecordUsing(fn (AzureSecurityAssessment $record): string => $this->resourceName($record->resource_id)),
                Group::make('recommendation_name')->label('Recommendation'),
                Group::make('assessment_type')->label('Assessment Type'),
            ])
            ->headerActions([
                ExportAction::make('exportSecurityRecommendations')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exporter(AzureSecurityAssessmentExporter::class)
                    ->formats([ExportFormat::Xlsx])
                    ->visible(fn (): bool => $this->canExport()),
            ])
            ->recordUrl(fn (AzureSecurityAssessment $record): string => AzureSecurityAssessmentResource::getUrl('view', [
                'subscription' => $record->subscription_id,
                'record' => $record,
            ]))
            ->defaultSort('status_changed_at', 'desc')
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([]);
    }

    private function canExport(): bool
    {
        $developer = auth()->guard('developers')->user();

        if ($developer?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();

        return $user !== null
            && app(AccessAuthorizationService::class)->hasPermission($user, 'security.view', $this->getOwnerRecord());
    }

    private function resourceName(?string $resourceId): string
    {
        if (! is_string($resourceId) || trim($resourceId) === '') {
            return '—';
        }

        $trimmed = trim($resourceId, '/');
        $segments = array_values(array_filter(explode('/', $trimmed), static fn (string $segment): bool => $segment !== ''));

        return $segments !== [] ? (string) end($segments) : $resourceId;
    }
}
