<?php

declare(strict_types=1);

namespace App\Livewire\Concerns;

use Illuminate\Contracts\View\View;

trait HasLoadingPlaceholder
{
    public function placeholder(): View
    {
        return view('livewire.placeholders.cloud');
    }
}
