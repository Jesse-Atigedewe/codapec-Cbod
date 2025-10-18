<?php

namespace App\Filament\Imports;

use App\Models\Cooperative;
use App\Models\District;
use App\Models\Region;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;

class CooperativeImporter extends Importer
{
    protected static ?string $model = Cooperative::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('region')
                ->relationship(resolveUsing: function ($state) {
                    $region = Region::query()
                        ->whereRaw('UPPER(name) = ?', [strtoupper(str_replace(' ', '-', trim($state)))])
                        ->first();

                    return $region ?? null;
                }),

            ImportColumn::make('district')
                ->relationship(resolveUsing: function ($state) {
                    $district = District::query()
                        ->whereRaw('UPPER(name) = ?', [strtoupper(str_replace(' ', '-', trim($state)))])
                        ->first();
                    return $district ?? null;
                }),
            ImportColumn::make('registration_number'),
            ImportColumn::make('name')
                ->rules(['required']),
            ImportColumn::make('leader_name'),
            ImportColumn::make('leader_contact'),
        ];
    }

    public function resolveRecord(): Cooperative
    {
        return new Cooperative();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your cooperative import has completed and ' . Number::format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
