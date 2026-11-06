<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;


use App\Livewire\Concerns\HasLoadingPlaceholder;
use App\Filament\Resources\AzureSecurityAssessments\AzureSecurityAssessmentResource;
use App\Models\AzureSecurityAssessment;
use App\Services\AccessAuthorizationService;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AzureSecurityAssessmentsRelationManager extends RelationManager
{
    use HasLoadingPlaceholder;

    protected static string $loadingPlaceholderVariant = 'table';
    protected static string $relationship = 'securityAssessments';
    protected static ?string $title = 'Unhealthy Security Recommendations';
    protected static bool $isLazy = true;
    protected static ?string $relatedResource = AzureSecurityAssessmentResource::class;

    private ?array $cachedFilterOptions = null;

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
                    'synced_at',
                    'remediation_description',
                    'portal_link',
                ])
                ->orderByRaw("CASE LOWER(severity)
                    WHEN 'critical' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'medium' THEN 3
                    WHEN 'low' THEN 4
                    ELSE 5
                END ASC")
                ->orderByDesc('synced_at'))
            ->columns([
                TextColumn::make('recommendation_name')->label('Recommendation')->searchable()->sortable()->weight('medium')->wrap(),
                TextColumn::make('severity')->label('Severity')->badge()->color(fn (?string $state): string => match (strtolower((string) $state)) {
                    'critical', 'high' => 'danger',
                    'medium' => 'warning',
                    'low' => 'gray',
                    default => 'gray',
                })->sortable(query: fn (Builder $query, string $direction): Builder => $query
                    ->orderByRaw("CASE LOWER(severity)
                        WHEN 'critical' THEN 1
                        WHEN 'high' THEN 2
                        WHEN 'medium' THEN 3
                        WHEN 'low' THEN 4
                        ELSE 5
                    END {$direction}")
                    ->orderByDesc('synced_at')),
                TextColumn::make('resource_id')->label('Affected Resource')->formatStateUsing(fn (?string $state): string => $this->resourceName($state))->searchable()->limit(60),
                TextColumn::make('synced_at')->label('Last Synced')->dateTime('d M Y, H:i')->timezone('Asia/Kolkata')->sortable(),
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
                    ->options(fn (): array => $this->filterOptions()['resource_id']),
                SelectFilter::make('assessment_type')
                    ->label('Assessment Type')
                    ->multiple()
                    ->options(fn (): array => $this->filterOptions()['assessment_type']),
                SelectFilter::make('implementation_effort')
                    ->label('Effort')
                    ->multiple()
                    ->options(fn (): array => $this->filterOptions()['implementation_effort']),
                SelectFilter::make('user_impact')
                    ->label('User Impact')
                    ->multiple()
                    ->options(fn (): array => $this->filterOptions()['user_impact']),
                SelectFilter::make('status_cause')
                    ->label('Status Cause')
                    ->multiple()
                    ->options(fn (): array => $this->filterOptions()['status_cause']),
            ])
            ->groups([
                Group::make('severity')->label('Severity'),
                Group::make('resource_id')->label('Affected Resource')->getTitleFromRecordUsing(fn (AzureSecurityAssessment $record): string => $this->resourceName($record->resource_id)),
                Group::make('recommendation_name')->label('Recommendation'),
                Group::make('assessment_type')->label('Assessment Type'),
            ])
            ->recordUrl(fn (AzureSecurityAssessment $record): string => AzureSecurityAssessmentResource::getUrl('view', [
                'subscription' => $record->subscription_id,
                'record' => $record,
            ]))
            ->striped(false)
            ->recordActions([])
            ->toolbarActions([
                XlsxExportAction::make()
                    ->visible(fn (): bool => $this->canExport()),
            ]);
    }

    /**
     * Build all recommendation filter choices from one distinct database read.
     *
     * @return array<string, array<string, string>>
     */
    private function filterOptions(): array
    {
        if ($this->cachedFilterOptions !== null) {
            return $this->cachedFilterOptions;
        }

        $rows = AzureSecurityAssessment::query()
            ->where('subscription_id', $this->getOwnerRecord()->subscription_id)
            ->where('state', 'Unhealthy')
            ->distinct()
            ->get([
                'resource_id',
                'assessment_type',
                'implementation_effort',
                'user_impact',
                'status_cause',
            ]);

        $resourceIds = $rows->pluck('resource_id')
            ->filter(fn ($value): bool => is_string($value) && $value !== '')
            ->unique()
            ->mapWithKeys(fn (string $resourceId): array => [$resourceId => $this->resourceName($resourceId)])
            ->sort()
            ->all();

        $options = ['resource_id' => $resourceIds];

        foreach (['assessment_type', 'implementation_effort', 'user_impact', 'status_cause'] as $field) {
            $values = $rows->pluck($field)
                ->filter(fn ($value): bool => is_string($value) && $value !== '')
                ->unique()
                ->sort()
                ->values();

            $options[$field] = $values->mapWithKeys(fn (string $value): array => [$value => $value])->all();
        }

        return $this->cachedFilterOptions = $options;
    }

    private function canExport(): bool
    {
        $developer = auth()->guard('developers')->user();

        if ($developer?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();

        return $user !== null
            && app(AccessAuthorizationService::class)->hasPermission($user, 'security.export', $this->getOwnerRecord());
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
