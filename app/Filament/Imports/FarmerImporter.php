<?php

namespace App\Filament\Imports;

use App\Models\Cooperative;
use App\Models\District;
use App\Models\Farmer;
use App\Models\Region;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Number;

class FarmerImporter extends Importer
{
    protected static ?string $model = Farmer::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('region')
                ->label('Region')
                ->relationship(resolveUsing: function ($state) {
                    $region = Region::where('name', strtoupper($state))->first();
                    return $region;
                })
                ->requiredMapping(),

            ImportColumn::make('district')
                ->label('District')
                ->relationship(resolveUsing: function ($state) {
                    $district = District::where('name', strtoupper($state))->first();
                    return $district;
                })
                ->requiredMapping(),

                ImportColumn::make('cooperative')
                ->label('Cooperative')
                ->relationship(resolveUsing: function ($state) {
                    $cooperative = Cooperative::where('name', strtoupper($state))->first();
                    return $cooperative;
                }),

            ImportColumn::make('operational_area')->requiredMapping(),
            ImportColumn::make('farmer_id')->requiredMapping(),
            ImportColumn::make('farmer_name'),
            ImportColumn::make('contact_number'),
            ImportColumn::make('id_card_number'),
            ImportColumn::make('farm_location'),
            ImportColumn::make('year_established'),
            ImportColumn::make('farm_code'),
            ImportColumn::make('cocoa_type'),
            ImportColumn::make('hectares'),
        ];
    }


    public function resolveRecord(): ?Farmer
    {
        return new Farmer();
    }




    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your farmer import has completed and ' .
            Number::format($import->successful_rows) . ' ' .
            str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' .
                str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
