<?php

namespace App\Filament\Resources\AzureSubscriptions\RelationManagers;

use App\Models\SubscriptionWebUrl;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
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
        return Gate::forUser(auth()->user())->authorize(
            'create',
            [SubscriptionWebUrl::class, $this->getOwnerRecord()],
        );
    }

    #[On('web-url-added')]
    public function refreshWebUrlsTable(): void
    {
        $this->resetTable();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('url')
                ->label('Web URL')
                ->url()
                ->required()
                ->maxLength(2048),
            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('URL copied')
                    ->wrap()
                    ->weight('medium'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                IconColumn::make('is_live')
                    ->label('Live')
                    ->boolean()
                    ->placeholder('Not checked'),
                TextColumn::make('http_status')
                    ->label('HTTP')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('response_time_ms')
                    ->label('Response')
                    ->formatStateUsing(fn ($state): string => $state === null ? '—' : $state.' ms')
                    ->sortable(),
                IconColumn::make('ssl_status')
                    ->label('SSL')
                    ->boolean()
                    ->placeholder('N/A'),
                TextColumn::make('ssl_provider')
                    ->label('SSL Provider')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_subject')
                    ->label('SSL Subject')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_issuer')
                    ->label('SSL Issuer')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_valid_from')
                    ->label('SSL Valid From')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_expires_at')
                    ->label('SSL Expires')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('ssl_days_remaining')
                    ->label('SSL Days')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('last_checked_at')
                    ->label('Last Checked')
                    ->dateTime()
                    ->placeholder('Never')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Add Web URL')
                    ->icon(Heroicon::OutlinedGlobeAlt)
                    ->modalHeading('Add Web URL')
                    ->modalSubmitActionLabel('Add URL')
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['url'] = rtrim(trim($data['url']), '/');
                        $data['url_hash'] = hash('sha256', $data['url']);
                        $data['is_active'] = (bool) ($data['is_active'] ?? true);

                        return $data;
                    })
                    ->after(function (): void {
                        $this->dispatch('web-url-added');
                    }),
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
                    ->extraModalFooterActions([
                        DeleteAction::make('delete')
                            ->label('Delete URL')
                            ->color('danger')
                            ->requiresConfirmation(),
                    ]),
                DeleteAction::make(),
            ])
            ->defaultSort('url');
    }
}
