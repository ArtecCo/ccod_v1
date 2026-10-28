<?php

declare(strict_types=1);

namespace App\Filament\Resources\AzureSecurityAssessments;

use App\Filament\Resources\AzureSecurityAssessments\Pages\ViewAzureSecurityAssessment;
use App\Filament\Resources\AzureSecurityAssessments\Schemas\AzureSecurityAssessmentInfolist;
use App\Filament\Resources\AzureSubscriptions\AzureSubscriptionResource;
use App\Models\AzureSecurityAssessment;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use BackedEnum;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class AzureSecurityAssessmentResource extends Resource
{
    protected static ?string $model = AzureSecurityAssessment::class;
    protected static ?string $parentResource = AzureSubscriptionResource::class;
    protected static ?string $modelLabel = 'Security Recommendation';
    protected static ?string $pluralModelLabel = 'Security Recommendations';
    protected static string|BackedEnum|null $navigationIcon = null;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'security-assessments';

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return AzureSubscriptionResource::asParent()
            ->relationship('securityAssessments')
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

    public static function infolist(Schema $schema): Schema
    {
        return AzureSecurityAssessmentInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewAzureSecurityAssessment::route('/{record}'),
        ];
    }
}
