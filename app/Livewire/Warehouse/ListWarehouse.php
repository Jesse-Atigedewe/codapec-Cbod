<?php

namespace App\Livewire\Warehouse;

use App\Models\Warehouse;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

class ListWarehouse extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithTable;
    use InteractsWithSchemas;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => Warehouse::query()->orderByDesc('created_at'))
            ->columns([
                TextColumn::make('user.name')->label('Codapec Rep')->sortable()->searchable(),
                TextColumn::make('name')->label('Warehouse Name')->sortable()->searchable(),
                TextColumn::make('location')->limit(15)->sortable()->searchable(),
                TextColumn::make('description')->limit(15)->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->url(fn (): string => route(name: 'warehouse.create'))
                    
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Warehouse $warehouse): string => route('warehouses.edit', $warehouse)),

                DeleteAction::make(), // delete warehouse
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(), // bulk delete

                ]),
            ]);
    }

    public function render(): View
    {
        // load warehouses that the current user manages
        $userId = Auth::id();
        $warehouses = Warehouse::query()->where('user_id', $userId)->orderBy('name')->get();

        // Build grouped items (warehouse -> chemicals summary) similar to WarehousesGrouped
        $query = WarehouseStock::query()
            ->selectRaw("warehouses.id as warehouse_id, warehouses.name as warehouse_name, chemicals.id as chemical_id, chemicals.name as chemical_name, chemicals.state as chemical_state, COALESCE(chemical_types.name, 'Uncategorized') as chemical_type, COALESCE(SUM(warehouse_stocks.quantity_available), 0) as remaining")
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
            ->join('chemicals', 'chemicals.id', '=', 'warehouse_stocks.chemical_id')
            ->leftJoin('chemical_types', 'chemical_types.id', '=', 'chemicals.type_id')
            ->when($warehouses->isNotEmpty(), fn($q) => $q->whereIn('warehouses.id', $warehouses->pluck('id')))
            ->groupBy('warehouses.id', 'warehouses.name', 'chemicals.id', 'chemicals.name', 'chemicals.state', DB::raw("COALESCE(chemical_types.name, 'Uncategorized')"))
            ->orderBy('warehouses.name');

        $rows = $query->get()
            ->groupBy('warehouse_id')
            ->map(fn($group) => [
                'warehouse_id' => $group->first()->warehouse_id,
                'warehouse_name' => $group->first()->warehouse_name,
                'total_remaining' => (int) $group->sum('remaining'),
                'items' => $group->map(fn($r) => [
                    'chemical_id' => $r->chemical_id,
                    'chemical_name' => $r->chemical_name,
                    'chemical_state' => $r->chemical_state,
                    'chemical_type' => $r->chemical_type,
                    'remaining' => (int) $r->remaining,
                ])->values(),
            ])->values();

        return view('livewire.warehouse.list-warehouse', [
            'warehouses' => $warehouses,
            'groups' => $rows,
        ]);
    }
}
