<?php

namespace App\Services;

use App\Models\AzureAlert;
use App\Models\AzureBudget;
use App\Models\AzureCostForecast;
use App\Models\AzureCostForecastSnapshot;
use App\Models\AzureSecurityAssessment;
use App\Models\AzureSecurityScore;
use App\Models\AzureSubscription;
use App\Models\AzureSubscriptionCost;
use App\Models\BillingResource;
use App\Models\BusinessUnit;
use App\Models\Project;
use App\Models\ReportExport;
use App\Models\SubscriptionWebUrl;
use App\Support\FilamentStyling;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;
use Throwable;

class ReportPdfService
{
    private const REPORT_LABELS = [
        'overview' => 'Scope overview',
        'costs' => 'Costs, budgets and forecasts',
        'security' => 'Security posture and recommendations',
        'alerts' => 'Azure alerts',
        'web_urls' => 'Web URLs and SSL',
        'resources' => 'Azure resources',
    ];

    public function generate(ReportExport $report): array
    {
        $subscriptionIds = $this->resolveSubscriptionIds($report);
        abort_if($subscriptionIds === [], 422, 'The selected scope contains no Azure subscriptions.');

        $directory = 'reports/'.$report->developer_id.'/'.$report->getKey();
        Storage::disk('local')->makeDirectory($directory);

        $individualFiles = [];
        $modulePaths = [];
        $selectedTypes = array_values(array_diff($report->report_types ?? [], ['consolidated']));
        $isConsolidated = in_array('consolidated', $report->report_types ?? [], true);

        // Selecting Consolidated by itself means include every available report section.
        if ($isConsolidated && $selectedTypes === []) {
            $selectedTypes = array_keys(self::REPORT_LABELS);
        }

        foreach ($selectedTypes as $type) {
            if ($type === 'consolidated' || ! isset(self::REPORT_LABELS[$type])) {
                continue;
            }

            $path = $directory.'/'.$type.'.pdf';
            $absolutePath = Storage::disk('local')->path($path);
            $payload = $this->buildModuleData($report, $type, $subscriptionIds);
            $primaryPalette = FilamentStyling::colors()['primary'] ?? [];
            $primaryColor = (string) ($primaryPalette[600] ?? $primaryPalette[500] ?? '#64748b');

            app('dompdf.wrapper')->loadView('reports.pdf.module', [
                'primaryColor' => $primaryColor,
                'brandName' => FilamentStyling::brandName(),
                'logoPath' => $this->logoPath(),
                'report' => $report,
                'label' => self::REPORT_LABELS[$type],
                'type' => $type,
                'payload' => $payload,
                'generatedAt' => now(),
            ])->setPaper('a4', 'portrait')->setOptions([
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ])->save($absolutePath);

            $modulePaths[$type] = $absolutePath;
            if (! $isConsolidated) {
                $individualFiles[] = [
                    'key' => 'individual-'.$type,
                    'label' => self::REPORT_LABELS[$type],
                    'name' => Str::slug($report->scope_name.'-'.$type.'-report').'.pdf',
                    'path' => $path,
                ];
            }
        }

        $files = $individualFiles;

        if ($isConsolidated) {
            $consolidatedPath = $directory.'/consolidated.pdf';
            $consolidatedAbsolute = Storage::disk('local')->path($consolidatedPath);
            $index = [];
            $page = 3;

            foreach ($modulePaths as $type => $path) {
                $count = $this->pageCount($path);
                $index[] = [
                    'title' => self::REPORT_LABELS[$type],
                    'page' => $page,
                    'pages' => $count,
                ];
                $page += $count;
            }

            $primaryPalette = FilamentStyling::colors()['primary'] ?? [];
            $primaryColor = (string) ($primaryPalette[600] ?? $primaryPalette[500] ?? '#64748b');
            $frontMatter = app('dompdf.wrapper')->loadView('reports.pdf.consolidated-front', [
                'primaryColor' => $primaryColor,
                'brandName' => FilamentStyling::brandName(),
                'logoPath' => $this->logoPath(),
                'report' => $report,
                'index' => $index,
                'generatedAt' => now(),
            ])->setPaper('a4', 'portrait')->setOptions([
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
            ])->output();

            $frontMatterPath = Storage::disk('local')->path($directory.'/front-matter.pdf');
            file_put_contents($frontMatterPath, $frontMatter);

            $this->mergePdfs(array_merge([$frontMatterPath], array_values($modulePaths)), $consolidatedAbsolute);

            @unlink($frontMatterPath);

            $files[] = [
                'key' => 'consolidated',
                'label' => 'Consolidated PDF',
                'name' => Str::slug($report->scope_name.'-consolidated-report').'.pdf',
                'path' => $consolidatedPath,
            ];
        }

        if ($files === []) {
            throw new \RuntimeException('No valid report types were selected.');
        }

        return $files;
    }

    private function resolveSubscriptionIds(ReportExport $report): array
    {
        $ids = match ($report->scope_type) {
            'subscription' => AzureSubscription::query()->where('subscription_id', $report->scope_id)->pluck('subscription_id'),
            'project' => Project::query()->find($report->scope_id)?->subscriptions()->pluck('azure_subscriptions.subscription_id') ?? collect(),
            'business_unit' => BusinessUnit::query()->find($report->scope_id)?->subscriptions()->pluck('azure_subscriptions.subscription_id') ?? collect(),
            default => collect(),
        };

        return $ids->filter()->unique()->values()->all();
    }

    private function buildModuleData(ReportExport $report, string $type, array $subscriptionIds): array
    {
        $subscriptions = AzureSubscription::query()
            ->whereIn('subscription_id', $subscriptionIds)
            ->orderBy('display_name')
            ->get(['subscription_id', 'display_name', 'state', 'health_status', 'security_score', 'mtd_spend_eur']);

        return match ($type) {
            'overview' => [
                'subscriptions' => $subscriptions,
                'summary' => [
                    'subscriptions' => $subscriptions->count(),
                    'healthy' => $subscriptions->where('health_status', 'Healthy')->count(),
                    'resources' => BillingResource::query()->whereIn('subscription_id', $subscriptionIds)->count(),
                    'alerts' => AzureAlert::query()->whereIn('subscription_id', $subscriptionIds)->whereBetween('fire_time', [$report->period_start->copy()->startOfDay(), $report->period_end->copy()->endOfDay()])->count(),
                ],
            ],
            'costs' => $this->costData($report, $subscriptionIds),
            'security' => $this->securityData($report, $subscriptionIds),
            'alerts' => [
                'rows' => AzureAlert::query()->with('subscription:subscription_id,display_name')
                    ->whereIn('subscription_id', $subscriptionIds)
                    ->whereBetween('fire_time', [$report->period_start->startOfDay(), $report->period_end->endOfDay()])
                    ->orderByDesc('fire_time')->get(),
            ],
            'web_urls' => [
                'rows' => SubscriptionWebUrl::query()->with('subscription:subscription_id,display_name')
                    ->whereIn('subscription_id', $subscriptionIds)
                    ->where('is_active', true)
                    ->orderBy('url')->get(),
            ],
            'resources' => [
                'rows' => BillingResource::query()->with('subscription:subscription_id,display_name')
                    ->whereIn('subscription_id', $subscriptionIds)
                    ->orderBy('subscription_id')->orderBy('name')->get(),
            ],
            default => [],
        };
    }

    private function costData(ReportExport $report, array $subscriptionIds): array
    {
        $subscriptions = AzureSubscription::query()
            ->whereIn('subscription_id', $subscriptionIds)
            ->orderBy('subscription_id')
            ->get(['subscription_id']);

        $scopeFilter = match ($report->scope_type) {
            'project' => 'project:'.$report->scope_id,
            'business_unit' => 'business_unit:'.$report->scope_id,
            default => 'all',
        };

        // Use the same persisted 12-month history capture and resource-cost
        // source used by the consolidated dashboard.
        $scopeHash = app(ConsolidatedCostHistoryService::class)->capture($subscriptions, $scopeFilter);
        $history = \App\Models\ConsolidatedCostHistory::query()
            ->where('scope_hash', $scopeHash)
            ->whereBetween('period_start', [
                now()->startOfMonth()->subMonths(11)->toDateString(),
                now()->startOfMonth()->toDateString(),
            ])
            ->orderBy('period_start')
            ->get();

        $resourceQuery = BillingResource::query()
            ->with('subscription:subscription_id,display_name')
            ->whereIn('subscription_id', $subscriptionIds)
            ->whereNotNull('cost_eur');

        $resourceTypeRows = (clone $resourceQuery)
            ->selectRaw("COALESCE(NULLIF(resource_type, ''), 'Unknown') AS resource_type")
            ->selectRaw('SUM(cost_eur) AS total_cost')
            ->groupBy('resource_type')
            ->orderByDesc('total_cost')
            ->get();

        $topResources = (clone $resourceQuery)
            ->orderByDesc('cost_eur')
            ->limit(10)
            ->get();

        $budgets = AzureBudget::query()
            ->with('subscription:subscription_id,display_name')
            ->whereIn('subscription_id', $subscriptionIds)
            ->where(fn ($query) => $query->whereNull('start_date')->orWhereDate('start_date', '<=', today()))
            ->where(fn ($query) => $query->whereNull('end_date')->orWhereDate('end_date', '>=', today()))
            ->orderBy('subscription_id')
            ->get();

        $forecasts = AzureCostForecast::query()
            ->with('subscription:subscription_id,display_name')
            ->whereIn('subscription_id', $subscriptionIds)
            ->get();

        $periodCosts = AzureSubscriptionCost::query()
            ->with('subscription:subscription_id,display_name')
            ->whereIn('subscription_id', $subscriptionIds)
            ->where('granularity', 'monthly')
            ->whereDate('period_start', '<=', $report->period_end)
            ->whereDate('period_end', '>=', $report->period_start)
            ->orderBy('period_start')->get();

        $periodCurrencies = $periodCosts->pluck('currency')->filter()->unique();
        $periodCurrency = $periodCurrencies->count() === 1 ? (string) $periodCurrencies->first() : null;
        $periodTotal = $periodCurrency !== null
            ? (float) $periodCosts->sum(fn ($row): float => (float) $row->amount)
            : null;

        $currentMonthRows = AzureSubscriptionCost::query()
            ->whereIn('subscription_id', $subscriptionIds)
            ->where('granularity', 'monthly')
            ->whereDate('period_start', now()->startOfMonth()->toDateString())
            ->get(['amount', 'currency']);
        $currentCurrencies = $currentMonthRows->pluck('currency')->filter()->unique();
        $currentCurrency = $currentCurrencies->count() === 1 ? (string) $currentCurrencies->first() : null;
        $currentActual = $currentCurrency !== null
            ? (float) $currentMonthRows->sum(fn ($row): float => (float) $row->amount)
            : null;
        $forecastCurrencies = $forecasts->pluck('currency')->filter()->unique();
        $forecastCurrency = $forecastCurrencies->count() === 1 ? (string) $forecastCurrencies->first() : null;
        $currentForecast = $currentActual !== null && $forecastCurrency === $currentCurrency
            ? $currentActual + (float) $forecasts->sum(fn ($row): float => (float) $row->forecast_amount)
            : null;

        return [
            'history' => $history,
            'resource_type_breakdown' => $resourceTypeRows,
            'top_resources' => $topResources,
            'budgets' => $budgets,
            'current_forecasts' => $forecasts,
            'period_total' => $periodTotal,
            'period_currency' => $periodCurrency,
            'current_actual' => $currentActual,
            'current_forecast' => $currentForecast,
            'current_currency' => $currentCurrency,
            'costs' => $periodCosts,
            'forecasts' => AzureCostForecastSnapshot::query()
                ->with('subscription:subscription_id,display_name')
                ->whereIn('subscription_id', $subscriptionIds)
                ->whereBetween('captured_on', [$report->period_start, $report->period_end])
                ->orderBy('captured_on')->get(),
        ];
    }

    private function securityData(ReportExport $report, array $subscriptionIds): array
    {
        return [
            'scores' => AzureSecurityScore::query()
                ->with('subscription:subscription_id,display_name')
                ->whereIn('subscription_id', $subscriptionIds)
                ->whereBetween('captured_at', [$report->period_start->copy()->startOfDay(), $report->period_end->copy()->endOfDay()])
                ->orderByDesc('captured_at')->get(),
            'assessments' => AzureSecurityAssessment::query()
                ->with('subscription:subscription_id,display_name')
                ->whereIn('subscription_id', $subscriptionIds)
                ->orderBy('severity')->orderBy('recommendation_name')->get(),
        ];
    }

    private function logoPath(): ?string
    {
        $settings = FilamentStyling::settings();
        $path = $settings?->logo_light;

        if (! $path) {
            return null;
        }

        // Resolve both relative public-disk paths and absolute /storage/... URLs
        // to a local filesystem path; DomPDF remote loading stays disabled.
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $assetPath = (string) parse_url($path, PHP_URL_PATH);
            $path = preg_replace('#^/storage/#', '', $assetPath) ?? $assetPath;
        } else {
            $path = ltrim(preg_replace('#^/?storage/#', '', $path) ?? $path, '/');
        }

        $disk = Storage::disk('public');
        return $disk->exists($path) ? $disk->path($path) : null;
    }

    private function pageCount(string $path): int
    {
        $pdf = new Fpdi();
        return $pdf->setSourceFile($path);
    }

    private function mergePdfs(array $paths, string $destination): void
    {
        $pdf = new Fpdi();

        foreach ($paths as $path) {
            if (! is_file($path)) {
                throw new \RuntimeException('A source PDF is missing during consolidation.');
            }

            $pageCount = $pdf->setSourceFile($path);

            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                $template = $pdf->importPage($pageNumber);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
            }
        }

        $pdf->Output('F', $destination);
    }
}
