<?php

namespace App\Livewire;

use App\Models\ChemicalRequest;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ChemicalByTypeInRegion extends TableWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => ChemicalRequest::query()->where('region_id', auth()->user()->region_id)
            ->where('status', 'dispatched')->with(['chemical', 'warehouse'])->orderByDesc('created_at'))
            ->columns([
                TextColumn::make('chemical.name')->label('Chemical Name'),
                TextColumn::make('quantity')->label('Quantity Dispatched'),
                TextColumn::make('warehouse.name')->label('Warehouse'),
                TextColumn::make('warehouse.location')->label('Warehouse Location'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
