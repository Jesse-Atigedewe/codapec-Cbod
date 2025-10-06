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
                        COALESCE(SUM(CASE WHEN chemicals.state = "granular" THEN warehouse_stocks.quantity_available END), 0) as granular_total,
                        COALESCE(SUM(CASE WHEN chemicals.state = "solid" THEN warehouse_stocks.quantity_available END), 0) as solid_total,
                        COALESCE(SUM(CASE WHEN chemicals.state = "liquid" THEN warehouse_stocks.quantity_available END), 0) as liquid_total,
                        COALESCE(SUM(CASE WHEN chemicals.state = "powder" THEN warehouse_stocks.quantity_available END), 0) as powder_total,
                        COALESCE(SUM(warehouse_stocks.quantity_available), 0) as total_available
                    ')
                    ->leftJoin('chemicals', 'chemicals.type_id', '=', 'chemical_types.id')
                    ->leftJoin('warehouse_stocks', 'warehouse_stocks.chemical_id', '=', 'chemicals.id')
                    ->leftJoin('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
                    ->whereIn('warehouse_stocks.warehouse_id', $user->warehouses->pluck('id'))
                    ->groupBy('chemical_types.id', 'chemical_types.name')
            )
            ->columns([
                TextColumn::make('type_name')
                    ->label('Input Type')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('granular_total')
                    ->label('Granular')
                    ->numeric()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('solid_total')
                    ->label('Solid')
                    ->numeric()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('liquid_total')
                    ->label('Liquid')
                    ->numeric()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('powder_total')
                    ->label('Powder')
                    ->numeric()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),

                TextColumn::make('total_available')
                    ->label('Total Available')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
            ]);
    }
}
