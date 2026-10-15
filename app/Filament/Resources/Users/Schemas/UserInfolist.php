<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Services\AccessAuthorizationService;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Account')
                ->description('Identity, account status and direct Business Unit membership.')
                ->icon('heroicon-o-user-circle')
                ->columnSpanFull()
                ->columns(2)
                ->schema([
                    TextEntry::make('name')
                        ->label('Name')
                        ->weight('medium'),
                    TextEntry::make('email')
                        ->label('Email')
                        ->copyable(),
                    IconEntry::make('is_active')
                        ->boolean()
                        ->label('Active'),
                    TextEntry::make('created_at')
                        ->dateTime()
                        ->label('Activation Date'),
                    TextEntry::make('roleAssignments.role.name')
                        ->label('Roles')
                        ->badge()
                        ->listWithLineBreaks(),
                    TextEntry::make('businessUnits.name')
                        ->label('Business Units')
                        ->listWithLineBreaks()
                        ->placeholder('None'),
                ]),

            Section::make('Resource Access')
                ->description('Where this user can access resources. Business Unit and Project access inherit the resources below them.')
                ->icon('heroicon-o-squares-2x2')
                ->columnSpanFull()
                ->schema([
                    RepeatableEntry::make('resourceAccesses')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('access_type')
                                ->label('Access level')
                                ->badge(),
                            TextEntry::make('businessUnit.name')
                                ->label('Business Unit')
                                ->placeholder('—'),
                            TextEntry::make('project.name')
                                ->label('Project')
                                ->placeholder('—'),
                            TextEntry::make('subscription.display_name')
                                ->label('Subscription')
                                ->placeholder('—'),
                        ])
                        ->columns(4),
                ]),

            Section::make('Accessible Subscriptions')
                ->description('Effective subscription access after hierarchy inheritance and authorization rules.')
                ->icon('heroicon-o-cloud')
                ->columnSpanFull()
                ->schema([
                    RepeatableEntry::make('accessible_subscriptions')
                        ->hiddenLabel()
                        ->state(fn ($record): array => app(AccessAuthorizationService::class)
                            ->accessibleSubscriptions($record)
                            ->map(fn ($s): array => [
                                'name' => $s->display_name,
                                'subscription_id' => $s->subscription_id,
                                'role' => $s->access_role?->name ?? '—',
                                'source' => implode(', ', $s->access_sources ?? []),
                            ])
                            ->all())
                        ->schema([
                            TextEntry::make('name')->label('Subscription'),
                            TextEntry::make('subscription_id')->label('Subscription ID')->copyable(),
                            TextEntry::make('role')->label('Role')->badge(),
                            TextEntry::make('source')->label('Access source'),
                        ])
                        ->columns(4),
                ]),
        ]);
    }
}
