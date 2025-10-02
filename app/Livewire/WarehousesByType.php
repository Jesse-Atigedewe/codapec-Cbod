<?php

namespace App\Livewire;

use Filament\Widgets\TableWidget;
use Filament\Tables;
use App\Models\WarehouseStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class WarehousesByType extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(fn (): Builder =>
                WarehouseStock::query()
                    ->selectRaw("warehouses.id as warehouse_id, warehouses.name as warehouse_name, COALESCE(chemical_types.name, 'Uncategorized') as chemical_type, COALESCE(SUM(warehouse_stocks.quantity_available), 0) as remaining, (warehouses.id || '-' || COALESCE(chemical_types.name, 'Uncategorized')) as id")
                    ->join('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
                    ->join('chemicals', 'chemicals.id', '=', 'warehouse_stocks.chemical_id')
                    ->leftJoin('chemical_types', 'chemical_types.id', '=', 'chemicals.type_id')
                    ->groupBy('warehouses.id', 'warehouses.name', DB::raw("COALESCE(chemical_types.name, 'Uncategorized')"))
            )
            ->columns([
                Tables\Columns\TextColumn::make('warehouse_name')->label('Warehouse')->sortable(),
                Tables\Columns\TextColumn::make('chemical_type')->label('Chemical Type')->sortable(),
                Tables\Columns\TextColumn::make('remaining')->label('Remaining Qty')->numeric()->color(fn($state) => $state > 0 ? 'success' : 'danger')->sortable(),
            ])
            ->defaultSort('warehouse_name');
    }
}
