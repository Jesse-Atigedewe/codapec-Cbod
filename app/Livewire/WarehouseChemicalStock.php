<?php

namespace App\Livewire;

use App\Models\WarehouseStock;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class WarehouseChemicalStock extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder =>
                WarehouseStock::query()
                    ->select([
                        DB::raw("CONCAT(warehouse_id, '-', chemical_id) as id"), // synthetic key
                        'warehouse_id',
                        'chemical_id',
                        DB::raw('SUM(quantity_received) as total_received'),
                        DB::raw('SUM(quantity_available) as total_available'),
                    ])
                    ->with(['warehouse', 'chemical'])
                    ->groupBy('warehouse_id', 'chemical_id')
            )
            ->columns([
                Tables\Columns\TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('chemical.name')
                    ->label('Chemical')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_received')
                    ->label('Total Received')
                    ->numeric()
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->label('Grand Total Received'),

                        // by type
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('By Type')
                            ->using(function () {
                                $rows = WarehouseStock::query()
                                    ->join('chemicals', 'warehouse_stocks.chemical_id', '=', 'chemicals.id')
                                    ->select('chemicals.type', DB::raw('SUM(warehouse_stocks.quantity_received) as total'))
                                    ->groupBy('chemicals.type')
                                    ->get();

                                if ($rows->isEmpty()) {
                                    return '—';
                                }

                                $list = $rows
                                    ->map(fn ($r) => ucfirst($r->type) . ': ' . (int) $r->total)
                                    ->join('<br/>');

                                return new HtmlString($list);
                            }),

                        // by state
                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('By State')
                            ->using(function () {
                                $rows = WarehouseStock::query()
                                    ->join('chemicals', 'warehouse_stocks.chemical_id', '=', 'chemicals.id')
                                    ->select('chemicals.state', DB::raw('SUM(warehouse_stocks.quantity_received) as total'))
                                    ->groupBy('chemicals.state')
                                    ->get();

                                if ($rows->isEmpty()) {
                                    return '—';
                                }

                                $list = $rows
                                    ->map(fn ($r) => ucfirst($r->state) . ': ' . (int) $r->total)
                                    ->join('<br/>');

                                return new HtmlString($list);
                            }),
                    ]),

                Tables\Columns\TextColumn::make('total_available')
                    ->label('Available Qty')
                    ->numeric()
                    ->sortable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()->label('Grand Total Available'),

                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('By Type')
                            ->using(function () {
                                $rows = WarehouseStock::query()
                                    ->join('chemicals', 'warehouse_stocks.chemical_id', '=', 'chemicals.id')
                                    ->select('chemicals.type', DB::raw('SUM(warehouse_stocks.quantity_available) as total'))
                                    ->groupBy('chemicals.type')
                                    ->get();

                                if ($rows->isEmpty()) {
                                    return '—';
                                }

                                $list = $rows
                                    ->map(fn ($r) => ucfirst($r->type) . ': ' . (int) $r->total)
                                    ->join('<br/>');

                                return new HtmlString($list);
                            }),

                        Tables\Columns\Summarizers\Summarizer::make()
                            ->label('By State')
                            ->using(function () {
                                $rows = WarehouseStock::query()
                                    ->join('chemicals', 'warehouse_stocks.chemical_id', '=', 'chemicals.id')
                                    ->select('chemicals.state', DB::raw('SUM(warehouse_stocks.quantity_available) as total'))
                                    ->groupBy('chemicals.state')
                                    ->get();

                                if ($rows->isEmpty()) {
                                    return '—';
                                }

                                $list = $rows
                                    ->map(fn ($r) => ucfirst($r->state) . ': ' . (int) $r->total)
                                    ->join('<br/>');

                                return new HtmlString($list);
                            }),
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('warehouse_id')
                    ->label('Warehouse')
                    ->relationship('warehouse', 'name'),

                Tables\Filters\SelectFilter::make('chemical_id')
                    ->label('Chemical')
                    ->relationship('chemical', 'name'),

                Tables\Filters\SelectFilter::make('chemical.type')
                    ->label('Chemical Type')
                    ->options([
                        'insecticide' => 'Insecticide',
                        'fungicide'   => 'Fungicide',
                        'herbicide'   => 'Herbicide',
                        'fertilizer'  => 'Fertilizer',
                    ]),

                Tables\Filters\SelectFilter::make('chemical.state')
                    ->label('Physical State')
                    ->options([
                        'granular' => 'Granular',
                        'solid'    => 'Solid',
                        'liquid'   => 'Liquid',
                        'powder'   => 'Powder',
                    ]),
            ])
            ->defaultSort('warehouse_id');
    }
}
