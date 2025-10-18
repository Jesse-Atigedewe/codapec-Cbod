<?php

namespace App\Filament\Imports;

use App\Models\Chemical;
use App\Models\ChemicalType;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ChemicalImporter extends Importer
{
    protected static ?string $model = Chemical::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('formula_quantity'),
            ImportColumn::make('type')
            ->relationship(resolveUsing: function($state){
                $type = ChemicalType::where('name',$state)->first();
                return $type;
            }),
            ImportColumn::make('unit')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): Chemical
    {
        return new Chemical();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your chemical import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
