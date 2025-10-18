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

    /**
     * 👇 Filament v4 way to hide widget completely
     */
    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('codapecrep');
    }

    public function table(Tables\Table $table): Tables\Table
    {
        $user = auth()->user();
        
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
                    ->leftJoin('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
                    ->whereIn('warehouse_stocks.warehouse_id', $user->warehouses->pluck('id'))
                    ->groupBy('chemical_types.id', 'chemical_types.name')
            )
            ->columns([
              
                TextColumn::make('total_available')
                    ->label('Total Available')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
            ]);
    }
}
