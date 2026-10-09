<?php

namespace App\Filament\Resources\SubscriptionDocumentations;

use App\Filament\Resources\SubscriptionDocumentations\Pages\DesignSubscriptionDocumentation;
use App\Models\SubscriptionDocumentation;
use Filament\Facades\Filament;
use Filament\Resources\Resource;

class SubscriptionDocumentationResource extends Resource
{
    protected static ?string $model = SubscriptionDocumentation::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function canEdit($record): bool
    {
        return Filament::auth()->user()?->can('update', $record) ?? false;
    }

    public static function getPages(): array
    {
        return [
            'design' => DesignSubscriptionDocumentation::route('/{record}/design'),
        ];
    }
}
