<?php

namespace App\Filament\Exports;

use App\Models\Farmer;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class FarmerExporter extends Exporter
{
    protected static ?string $model = Farmer::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('region'),
            ExportColumn::make('district'),
            ExportColumn::make('operational_area'),
            ExportColumn::make('farmer_id'),
            ExportColumn::make('farmer_name'),
            ExportColumn::make('contact_number'),
            ExportColumn::make('id_card_number'),
            ExportColumn::make('farm_location'),
            ExportColumn::make('year_established'),
            ExportColumn::make('farm_code'),
            ExportColumn::make('cocoa_type'),
            ExportColumn::make('hectares'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your farmer export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
