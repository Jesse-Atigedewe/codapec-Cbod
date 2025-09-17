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
use Livewire\Component;

class ListWarehouseStocks extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    protected function getUserWarehouseId(): ?int
    {
        $userId = Auth::id();
        return Warehouse::query()->where('user_id', $userId)->value('id');
    }

    public function table(Table $table): Table
    {
        $warehouseId = $this->getUserWarehouseId();

        return $table
            ->query(fn (): Builder => WarehouseStock::query()
                ->when($warehouseId, fn ($q) => $q->where('warehouse_id', $warehouseId))
                ->with(['chemical']))
            ->columns([
                TextColumn::make('chemical.name')->label('Chemical')->searchable(),
                TextColumn::make('quantity_received')->label('Received'),
                TextColumn::make('quantity_available')->label('Available'),
                TextColumn::make('batch_number')->label('Batch'),
                TextColumn::make('received_date')->date()->label('Received At'),
            ])
            ->headerActions([
                CreateAction::make()->url(fn(): string => route('warehouse_stocks.create')),
                // EditAction::make()
            ]);
    }

    public function getTotalAvailableProperty(): float
    {
        $warehouseId = $this->getUserWarehouseId();
        return WarehouseStock::where('warehouse_id', $warehouseId)->sum('quantity_available');
    }

    public function render(): View
    {
        return view('livewire.warehouse-stocks.list-warehouse-stocks');
    }
}


