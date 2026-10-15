<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\AzureSubscription;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Project')
                ->description('Define the project and the Business Unit it belongs to.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Project name')
                        ->required()
                        ->maxLength(255),
                    Select::make('business_unit_id')
                        ->label('Business Unit')
                        ->relationship('businessUnit', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ]),

            Section::make('Project subscriptions')
                ->description('Select the Azure subscriptions that belong to this project. Add one or more tags to identify each subscription, such as prod, qa, or dev. Tags are only labels and can be customized.')
                ->schema([
                    Repeater::make('subscriptionAssignments')
                        ->relationship()
                        ->label('Subscriptions')
                        ->schema([
                            Select::make('subscription_id')
                                ->label('Azure Subscription')
                                ->options(fn (): array => AzureSubscription::query()
                                    ->orderBy('display_name')
                                    ->pluck('display_name', 'subscription_id')
                                    ->all())
                                ->searchable()
                                ->preload()
                                ->required(),
                            TagsInput::make('tags')
                                ->label('Tags')
                                ->suggestions(['prod', 'qa', 'dev'])
                                ->placeholder('Add tags')
                                ->helperText('Suggested: prod, qa, dev. You can enter custom tags too.'),
                        ])
                        ->columns(2)
                        ->addActionLabel('Add subscription')
                        ->defaultItems(0)
                        ->reorderable(false)
                        ->cloneable(false)
                        ->itemLabel(fn (array $state): ?string => filled($state['subscription_id'] ?? null)
                            ? AzureSubscription::query()->where('subscription_id', $state['subscription_id'])->value('display_name')
                            : 'Subscription'),
                ]),
        ]);
    }
}
