<?php

namespace App\Filament\Pages;

use App\Models\PillColor;
use App\Support\PillColorRegistry;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PillColors extends Page
{
    protected static ?string $navigationLabel = 'Colors';
    protected static ?string $title = 'Pill Colors';
    protected static ?string $slug = 'colors';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';
    protected static string|\UnitEnum|null $navigationGroup = 'Developers';
    protected static ?int $navigationSort = 20;

    protected string $view = 'filament.pages.pill-colors';

    public array $selectedColors = [];

    public function mount(): void
    {
        foreach (PillColorRegistry::all() as $key => $definition) {
            $pillColor = PillColor::query()->firstOrCreate(
                ['key' => $key],
                ['color' => $definition['default']],
            );

            $this->selectedColors[$key] = $pillColor->color;
        }
    }

    public function pillColors(): array
    {
        $registry = PillColorRegistry::all();

        return collect($registry)->map(fn (array $definition, string $key): array => [
            'key' => $key,
            'label' => $definition['label'],
            'default' => $definition['default'],
        ])->values()->all();
    }

    public function colorOptions(): array
    {
        return [
            'gray' => 'Gray',
            'danger' => 'Red',
            'warning' => 'Amber',
            'success' => 'Green',
            'info' => 'Blue',
        ];
    }

    public function saveColor(string $key): void
    {
        abort_unless(array_key_exists($key, PillColorRegistry::all()), 404);

        $color = $this->selectedColors[$key] ?? null;
        abort_unless(array_key_exists($color, $this->colorOptions()), 422);

        PillColor::query()->updateOrCreate(
            ['key' => $key],
            ['color' => $color],
        );

        Notification::make()
            ->title('Pill color updated')
            ->success()
            ->send();
    }
}
