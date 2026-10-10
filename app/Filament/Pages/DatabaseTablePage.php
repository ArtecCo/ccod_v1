<?php

namespace App\Filament\Pages;

use App\Models\DatabaseTableRecord;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseTablePage extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.database-table';

    protected static ?string $slug = 'database/{table}';

    protected static bool $shouldRegisterNavigation = true;

    public string $tableName = '';

    public function mount(string $table): void
    {
        $this->tableName = $this->resolveTableName($table);

        abort_unless($this->tableName !== '', 404);
    }

    public static function getNavigationItems(): array
    {
        $database = DB::connection()->getDatabaseName();

        return DB::table('information_schema.tables')
            ->where('TABLE_SCHEMA', $database)
            ->where('TABLE_TYPE', 'BASE TABLE')
            ->orderBy('TABLE_NAME')
            ->pluck('TABLE_NAME')
            ->map(fn (string $table): NavigationItem => NavigationItem::make(Str::headline($table))
                ->icon(Heroicon::OutlinedTableCells)
                ->group('Database')
                ->url(static::getUrl(['table' => $table]))
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.developer.pages.database-table')
                    && request()->route('table') === $table))
            ->all();
    }

    public function getTitle(): string
    {
        return $this->tableName;
    }

    public function getHeading(): string
    {
        return $this->tableName;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->paginated([25, 50, 100, 250])
            ->searchable()
            ->headerActions([
                XlsxExportAction::make()
                    ->title('Export Table')
                    ->fileName(fn (): string => $this->tableName.'-'.now()->format('Y-m-d-His')),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    private function getTableQuery(): Builder
    {
        return (new DatabaseTableRecord())
            ->setTable($this->tableName)
            ->newQuery();
    }

    private function getTableColumns(): array
    {
        return collect($this->getTableColumnMetadata())
            ->map(function (array $column): TextColumn {
                $name = $column['COLUMN_NAME'];
                $type = strtolower((string) $column['DATA_TYPE']);

                $text = TextColumn::make($name)
                    ->label(Str::headline($name))
                    ->searchable()
                    ->placeholder('—')
                    ->wrap();

                if (in_array($type, ['tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint', 'decimal', 'numeric', 'float', 'double', 'real'], true)) {
                    $text->numeric();
                }

                if (in_array($type, ['date', 'datetime', 'timestamp'], true)) {
                    $text->dateTime('Y-m-d H:i:s');
                }

                return $text;
            })
            ->all();
    }

    private function getTableColumnMetadata(): array
    {
        return DB::table('information_schema.columns')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $this->tableName)
            ->orderBy('ORDINAL_POSITION')
            ->get()
            ->map(fn ($column): array => (array) $column)
            ->all();
    }

    private function resolveTableName(string $table): string
    {
        return (string) DB::table('information_schema.tables')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('TABLE_TYPE', 'BASE TABLE')
            ->value('TABLE_NAME');
    }
}
