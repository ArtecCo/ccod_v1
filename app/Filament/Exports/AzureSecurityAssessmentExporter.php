<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use App\Models\AzureSecurityAssessment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class AzureSecurityAssessmentExporter extends Exporter
{
    protected static ?string $model = AzureSecurityAssessment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('recommendation_name')->label('Recommendation'),
            ExportColumn::make('severity')->label('Severity'),
            ExportColumn::make('resource_id')->label('Affected Resource'),
            ExportColumn::make('status_changed_at')->label('Status Changed'),
            ExportColumn::make('assessment_type')->label('Assessment Type'),
            ExportColumn::make('implementation_effort')->label('Effort'),
            ExportColumn::make('user_impact')->label('User Impact'),
            ExportColumn::make('status_cause')->label('Status Cause'),
            ExportColumn::make('first_evaluated_at')->label('First Evaluated'),
            ExportColumn::make('remediation_description')->label('Remediation'),
            ExportColumn::make('portal_link')->label('Azure Portal'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your security recommendations export has completed and '.number_format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' were exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.number_format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
