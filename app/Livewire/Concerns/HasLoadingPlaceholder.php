<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Contracts\View\View;

trait HasLoadingPlaceholder
{
    public function placeholder(): View
    {
        $variant = property_exists(static::class, 'loadingPlaceholderVariant')
            ? static::$loadingPlaceholderVariant
            : 'cards';

        return view('livewire.placeholders.loading', ['variant' => $variant]);
    }
}
