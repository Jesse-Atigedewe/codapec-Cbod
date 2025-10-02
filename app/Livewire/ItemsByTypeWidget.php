<?php

namespace App\Livewire;

use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ChemicalType;

class ItemsByTypeWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(fn (): Builder =>
               ChemicalType::query()
    ->selectRaw('
        chemical_types.id,
        chemical_types.name as type_name,
        COALESCE(SUM(warehouse_stocks.quantity_available), 0) as total_available
    ')
    ->leftJoin('chemicals', 'chemicals.type_id', '=', 'chemical_types.id')
    ->leftJoin('warehouse_stocks', 'warehouse_stocks.chemical_id', '=', 'chemicals.id')
    ->groupBy('chemical_types.id', 'chemical_types.name')

            )
            ->columns([
                TextColumn::make('type_name')->label('Chemical Type')->searchable(),
                TextColumn::make('total_available')->label('Available Quantity')->numeric()->color(fn($state) => $state > 0 ? 'success' : 'danger'),
            ]);
    }
}
