<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DeveloperActivityChart extends ChartWidget
{
    protected ?string $heading = 'Application activity · Last 14 days';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $start = now()->subDays(13)->startOfDay();
        $rows = DB::table('activity_log')
            ->selectRaw('DATE(created_at) as activity_date, COUNT(*) as total')
            ->where('created_at', '>=', $start)
            ->groupBy('activity_date')
            ->orderBy('activity_date')
            ->pluck('total', 'activity_date');

        $labels = [];
        $values = [];

        for ($day = 0; $day < 14; $day++) {
            $date = $start->copy()->addDays($day);
            $key = $date->toDateString();
            $labels[] = $date->format('d M');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return [
            'datasets' => [[
                'label' => 'Audit events',
                'data' => $values,
                'fill' => 'start',
                'tension' => 0.35,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
