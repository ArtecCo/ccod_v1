<?php

namespace App\Filament\Resources\TeamsWebhookTemplates\Pages;

use App\Filament\Resources\TeamsWebhookTemplates\TeamsWebhookTemplateResource;
use App\Models\TeamsWebhook;
use App\Models\TeamsWebhookCategory;
use App\Models\TeamsWebhookTemplate;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Utilities\Get;

class ListTeamsWebhookTemplates extends ListRecords
{
    protected static string $resource = TeamsWebhookTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageCategories')
                ->label('Manage categories')
                ->icon('heroicon-o-tag')
                ->color('gray')
                ->modalHeading('Category management')
                ->modalDescription('Create a category, rename an existing one, or remove an unused category. Categories in use by templates or webhooks cannot be deleted.')
                ->modalSubmitActionLabel('Apply changes')
                ->schema([
                    Select::make('operation')
                        ->label('What would you like to do?')
                        ->options([
                            'create' => 'Create a category',
                            'rename' => 'Rename a category',
                            'delete' => 'Delete an unused category',
                        ])
                        ->default('create')
                        ->live()
                        ->required(),

                    Select::make('category_id')
                        ->label('Category')
                        ->options(fn (): array => TeamsWebhookCategory::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->required(fn (Get $get): bool => in_array($get('operation'), ['rename', 'delete'], true))
                        ->visible(fn (Get $get): bool => in_array($get('operation'), ['rename', 'delete'], true)),

                    TextInput::make('name')
                        ->label(fn (Get $get): string => $get('operation') === 'rename' ? 'New category name' : 'Category name')
                        ->required(fn (Get $get): bool => in_array($get('operation'), ['create', 'rename'], true))
                        ->maxLength(80)
                        ->visible(fn (Get $get): bool => in_array($get('operation'), ['create', 'rename'], true)),

                    TextInput::make('confirm_name')
                        ->label('Type the category name to confirm deletion')
                        ->required(fn (Get $get): bool => $get('operation') === 'delete')
                        ->visible(fn (Get $get): bool => $get('operation') === 'delete')
                        ->helperText('This extra check prevents accidental deletion.'),
                ])
                ->action(function (array $data): void {
                    $operation = $data['operation'];

                    if ($operation === 'create') {
                        $name = trim($data['name']);

                        if (TeamsWebhookCategory::query()->where('name', $name)->exists()) {
                            Notification::make()->title('Category name already exists')->danger()->send();
                            return;
                        }

                        TeamsWebhookCategory::query()->create(['name' => $name]);
                        Notification::make()->title('Category created')->success()->send();
                        return;
                    }

                    $category = TeamsWebhookCategory::query()->find($data['category_id']);

                    if (! $category) {
                        Notification::make()->title('Category not found')->danger()->send();
                        return;
                    }

                    if ($operation === 'rename') {
                        $name = trim($data['name']);

                        if (TeamsWebhookCategory::query()->where('name', $name)->whereKeyNot($category->getKey())->exists()) {
                            Notification::make()->title('Category name already exists')->danger()->send();
                            return;
                        }

                        $category->update(['name' => $name]);
                        Notification::make()->title('Category renamed')->success()->send();
                        return;
                    }

                    if ($operation === 'delete') {
                        if (trim($data['confirm_name'] ?? '') !== $category->name) {
                            Notification::make()->title('Category name does not match')->body('The category was not deleted.')->danger()->send();
                            return;
                        }

                        $hasTemplates = TeamsWebhookTemplate::query()->where('category', $category->slug)->exists();
                        $hasWebhooks = TeamsWebhook::query()->where('category', $category->slug)->exists();

                        if ($hasTemplates || $hasWebhooks) {
                            Notification::make()
                                ->title('Category is still in use')
                                ->body('Delete or move its templates and reassign its webhooks before deleting this category.')
                                ->danger()
                                ->send();
                            return;
                        }

                        $category->delete();
                        Notification::make()->title('Category deleted')->success()->send();
                    }
                }),

            CreateAction::make(),
        ];
    }
}
