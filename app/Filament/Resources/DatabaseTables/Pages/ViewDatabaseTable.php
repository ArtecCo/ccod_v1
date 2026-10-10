<?php

namespace App\Filament\Resources\DatabaseTables\Pages;

use App\Filament\Resources\DatabaseTables\DatabaseTableResource;
use App\Models\DatabaseTableRecord;
use Asignua\FilamentXlsxExport\Actions\XlsxExportAction;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ViewDatabaseTable extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = DatabaseTableResource::class;

    protected string $view = 'filament.resources.database-tables.pages.view-database-table';

    public string $tableName = '';

    public function mount(string $table): void
    {
        $this->tableName = $this->resolveTableName($table);

        abort_unless($this->tableName !== '', 404);
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
        $columns = $this->getTableColumns();

        return $table
            ->query($this->getTableQuery())
            ->columns($columns)
            ->defaultSort($this->getPrimaryKeyColumn())
            ->paginated([25, 50, 100, 250])
            ->searchable()
            ->filters([
                Filter::make('all')
                    ->label('Search all columns')
                    ->form([]),
            ])
            ->headerActions([
                XlsxExportAction::make()
                    ->title(Str::headline($this->tableName))
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
                $label = Str::headline($name);

                $text = TextColumn::make($name)
                    ->label($label)
                    ->searchable()
                    ->sortable()
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

    private function getPrimaryKeyColumn(): string
    {
        return DB::table('information_schema.key_column_usage')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $this->tableName)
            ->where('CONSTRAINT_NAME', 'PRIMARY')
            ->orderBy('ORDINAL_POSITION')
            ->value('COLUMN_NAME') ?? $this->getTableColumnMetadata()[0]['COLUMN_NAME'];
    }

    private function resolveTableName(string $table): string
    {
        return (string) DB::table('information_schema.tables')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->value('TABLE_NAME');
    }
}
