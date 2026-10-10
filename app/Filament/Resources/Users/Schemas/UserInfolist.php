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
            Section::make('User')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email'),
                    TextEntry::make('role')->badge(),
                    IconEntry::make('is_active')->boolean(),
                    TextEntry::make('teams.name')->label('Teams')->listWithLineBreaks(),
                    TextEntry::make('created_at')->dateTime()->label('Activation Date'),
                    TextEntry::make('updated_at')->dateTime(),
                ])
                ->columns(2),

            Section::make('Accessible Subscriptions')
                ->description('All subscriptions where this user currently has effective access, including team-derived and specially granted access.')
                ->schema([
                    RepeatableEntry::make('accessible_subscriptions')
                        ->hiddenLabel()
                        ->state(function ($record): array {
                            return app(AccessAuthorizationService::class)
                                ->accessibleSubscriptions($record)
                                ->map(fn ($subscription): array => [
                                    'name' => $subscription->display_name,
                                    'subscription_id' => $subscription->subscription_id,
                                    'role' => $subscription->access_role?->label() ?? '—',
                                    'source' => implode(', ', $subscription->access_sources ?? []),
                                ])
                                ->all();
                        })
                        ->schema([
                            TextEntry::make('name')->label('Subscription'),
                            TextEntry::make('subscription_id')->label('Subscription ID')->copyable(),
                            TextEntry::make('role')->badge(),
                            TextEntry::make('source')->label('Access source'),
                        ])
                        ->columns(4),
                ])
                ->collapsible(),
        ]);
    }
}
