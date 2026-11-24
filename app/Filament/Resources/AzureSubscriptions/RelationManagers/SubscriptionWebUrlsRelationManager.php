<?php

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;

use App\Models\SubscriptionWebUrl;
use App\Models\User;
use App\Services\AccessAuthorizationService;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;

class SubscriptionWebUrlsRelationManager extends RelationManager
{
    protected static string $relationship = 'webUrls';
    protected static ?string $title = 'Web URLs';
    protected static string|\BackedEnum|null $icon = Heroicon::OutlinedGlobeAlt;
    protected static bool $isLazy = true;

    protected function getCreateAuthorizationResponse(): Response
    {
        return Gate::forUser(auth()->user())->authorize('create', [SubscriptionWebUrl::class, $this->getOwnerRecord()]);
    }

    public string $urlFilter = 'all';

    #[On('filter-subscription-web-urls')]
    public function filterWebUrls(?string $filter = 'all'): void
    {
        $filter = in_array($filter, ['all', 'live', 'down', 'ssl_expiring'], true) ? $filter : 'all';
        $this->urlFilter = $this->urlFilter === $filter && $filter !== 'all' ? 'all' : $filter;
        $this->resetTable();
    }

    #[On('web-url-added')]
    public function refreshWebUrlsTable(): void { $this->resetTable(); }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('url')->label('Web URL')->url()->required()->maxLength(2048),
            Toggle::make('is_active')->label('Active')->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                if ($this->urlFilter === 'live') {
                    $query->where('is_active', true)->where('is_live', true);
                } elseif ($this->urlFilter === 'down') {
                    $query->where('is_active', true)->where('is_live', false);
                } elseif ($this->urlFilter === 'ssl_expiring') {
                    $query->where('is_active', true)->whereNotNull('ssl_expires_at')
                        ->whereBetween('ssl_expires_at', [now(), now()->addDays(15)]);
                }
                return $query;
            })
            ->striped(false)
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active')
                    ->trueLabel('Active')
                    ->falseLabel('Inactive'),
                TernaryFilter::make('is_live')
                    ->label('Live Status')
                    ->trueLabel('Live')
                    ->falseLabel('Down'),
                TernaryFilter::make('ssl_status')
                    ->label('SSL Valid')
                    ->trueLabel('Valid')
                    ->falseLabel('Invalid'),
                Filter::make('ssl_expiring')
                    ->label('SSL Expiring in 15 Days')
                    ->query(fn (\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder => $query
                        ->whereNotNull('ssl_expires_at')
                        ->whereBetween('ssl_expires_at', [now(), now()->addDays(15)])),
            ])
            ->columns([
                TextColumn::make('url')->label('URL')->searchable()->sortable()->copyable()->copyMessage('URL copied')->wrap()->weight('medium'),
                IconColumn::make('is_active')->label('Active')->boolean(),
                IconColumn::make('is_live')->label('Live')->boolean()->placeholder('Not checked'),
                TextColumn::make('http_status')->label('HTTP')->placeholder('—')->sortable(),
                TextColumn::make('response_time_ms')->label('Response')->formatStateUsing(fn ($state): string => $state === null ? '—' : $state.' ms')->sortable(),
                IconColumn::make('ssl_status')->label('SSL')->boolean()->placeholder('N/A'),
                TextColumn::make('ssl_provider')->label('SSL Provider')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_subject')->label('SSL Subject')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_issuer')->label('SSL Issuer')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_valid_from')->label('SSL Valid From')->dateTime()->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_expires_at')->label('SSL Expires')->dateTime()->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_days_remaining')->label('SSL Days')->placeholder('—')->sortable(),
                TextColumn::make('last_checked_at')->label('Last Checked')->dateTime()->placeholder('Never')->sortable(),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Edit')
                    ->authorize(fn (SubscriptionWebUrl $record): bool => Gate::forUser(auth()->user())->allows('update', $record))
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['url'] = rtrim(trim($data['url']), '/');
                        $data['url_hash'] = hash('sha256', $data['url']);
                        return $data;
                    })
                    ->before(function (EditAction $action, array $data, SubscriptionWebUrl $record): void {
                        $urlHash = hash('sha256', rtrim(trim((string) ($data['url'] ?? '')), '/'));
                        $exists = $record->subscription()?->first()?->webUrls()->where('url_hash', $urlHash)->whereKeyNot($record->getKey())->exists();
                        if ($exists) {
                            Notification::make()->danger()->title('Web URL already exists')->body('Another URL with the same address is already configured for this subscription.')->send();
                            $action->halt();
                        }
                    })
                    ->extraModalFooterActions([
                        Action::make('delete')->label('Delete URL')->color('danger')->requiresConfirmation()->authorize(fn (SubscriptionWebUrl $record): bool => Gate::forUser(auth()->user())->allows('delete', $record))->action(function (SubscriptionWebUrl $record): void {$record->delete(); $this->dispatch('web-url-added');})->cancelParentActions(),
                    ]),
                DeleteAction::make()->authorize(fn (SubscriptionWebUrl $record): bool => Gate::forUser(auth()->user())->allows('delete', $record)),
            ])
            ->toolbarActions([
                Action::make('addWebUrl')
                    ->label('Add Web URL')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->visible(fn (): bool => Gate::forUser(auth()->user())->allows(
                        'create',
                        [SubscriptionWebUrl::class, $this->getOwnerRecord()],
                    ))
                    ->modalHeading('Add Web URL')
                    ->modalSubmitActionLabel('Add URL')
                    ->form([
                        TextInput::make('url')->label('Web URL')->url()->required()->maxLength(2048),
                        Toggle::make('is_active')->label('Active')->default(true),
                    ])
                    ->action(function (array $data): void {
                        $url = rtrim(trim($data['url']), '/');
                        $this->getOwnerRecord()->webUrls()->create([
                            'url' => $url,
                            'url_hash' => hash('sha256', $url),
                            'is_active' => (bool) ($data['is_active'] ?? true),
                        ]);

                        $this->dispatch('web-url-added');
                    })
                    ->successNotificationTitle('Web URL added'),
                XlsxExportAction::make()
                    ->visible(fn (): bool => $this->canExport()),
            ])
            ->defaultSort('url');
    }

    private function canExport(): bool
    {
        $user = auth()->guard('web')->user();
        return $user instanceof User && app(AccessAuthorizationService::class)->hasPermission($user, 'urls.export', $this->getOwnerRecord());
    }
}
