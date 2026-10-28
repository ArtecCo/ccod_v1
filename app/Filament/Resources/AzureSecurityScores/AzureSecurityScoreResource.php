<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityScores;

use App\Filament\Resources\AzureSecurityScores\Pages\ListAzureSecurityScores;
use App\Filament\Resources\AzureSecurityScores\Pages\ViewAzureSecurityScore;
use App\Filament\Resources\AzureSecurityScores\Schemas\AzureSecurityScoreInfolist;
use App\Filament\Resources\AzureSecurityScores\Tables\AzureSecurityScoresTable;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Models\AzureSecurityScore;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use BackedEnum;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AzureSecurityScoreResource extends Resource
{
    protected static ?string $model = AzureSecurityScore::class;
    protected static ?string $parentResource = AzureSubscriptionResource::class;
    protected static ?string $modelLabel = 'Security Score Control';
    protected static ?string $pluralModelLabel = 'Security Score Controls';
    protected static string|BackedEnum|null $navigationIcon = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'security-score-controls';

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return AzureSubscriptionResource::asParent()
            ->relationship('securityScores')
            ->inverseRelationship('subscription');
    }

    public static function canViewAny(): bool
    {
        if (auth()->guard('developers')->user()?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();
        return $user instanceof User && app(AccessAuthorizationService::class)->hasPermission($user, 'security.view');
    }

    public static function getEloquentQuery(): Builder
    {
        if (auth()->guard('developers')->user()?->is_active === true) {
            return parent::getEloquentQuery();
        }

        $user = auth()->guard('web')->user();
        if (! $user instanceof User) {
            return parent::getEloquentQuery()->whereRaw('1 = 0');
        }

        $accessible = app(AccessAuthorizationService::class)->accessibleSubscriptionQuery($user);

        return parent::getEloquentQuery()
            ->whereIn('subscription_id', $accessible->select('subscription_id'));
    }

    public static function canView($record): bool
    {
        if (auth()->guard('developers')->user()?->is_active === true) {
            return true;
        }

        $user = auth()->guard('web')->user();
        return $user instanceof User
            && app(AccessAuthorizationService::class)->hasResourceAccess($user, $record->subscription);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return AzureSecurityScoreInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AzureSecurityScoresTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAzureSecurityScores::route('/'),
            'view' => ViewAzureSecurityScore::route('/{record}'),
        ];
    }
}
