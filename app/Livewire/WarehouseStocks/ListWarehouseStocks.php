<?php

namespace App\Livewire\WarehouseStocks;

use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\Summarizers\Sum;
use Livewire\Component;

class ListWarehouseStocks extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

   public function table(Table $table): Table
    {
        $userId = Auth::id();
        $userWarehouseIds = Warehouse::query()
            ->where('user_id', $userId)
            ->pluck('id')
            ->toArray();

        return $table
            ->query(fn (): Builder => WarehouseStock::query()
                ->when(
                    count($userWarehouseIds) > 0,
                    fn ($q) => $q->whereIn('warehouse_id', $userWarehouseIds)
                )
                ->with(['chemical.type', 'warehouse'])
            )
            ->columns([
                TextColumn::make('warehouse.name')
                    ->label('Warehouse')
                    ->sortable(),

                TextColumn::make('chemical.type.name')
                    ->label('Type'),
                    // ->sortable(),

                TextColumn::make('chemical.name')
                    ->label('Chemical')
                    ->searchable(),

                TextColumn::make('chemical.state')
                    ->label('State'),

                TextColumn::make('quantity_available')
                    ->label('Remaining'),
                    // ->summarize(Sum::make()->label('Total Remaining')),
            ])
            ->groups([
                'warehouse.name', // group rows by warehouse
            ])
            ->headerActions([
                CreateAction::make()->url(fn():string=>route('warehouse_stocks.create'))
            ])
            ->defaultGroup('warehouse.name');
    }
    public function render(): View
    {
        return view('livewire.warehouse-stocks.list-warehouse-stocks');
    }
}


