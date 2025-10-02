<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WarehouseStock;
use Illuminate\Support\Facades\DB;

class WarehousesGrouped extends Component
{
    // keep track of which warehouse ids are expanded in the accordion
    public array $open = [];

    public function drillDown(?int $warehouseId): void
    {
        if ($warehouseId !== null && ! in_array($warehouseId, $this->open, true)) {
            $this->open[] = $warehouseId;
        }
    }

    public function toggle(int $warehouseId): void
    {
        if (in_array($warehouseId, $this->open, true)) {
            $this->open = array_values(array_filter($this->open, fn($id) => $id !== $warehouseId));
            return;
        }

        $this->open[] = $warehouseId;
    }

    public function render()
    {
        $query = WarehouseStock::query()
            ->selectRaw("warehouses.id as warehouse_id, warehouses.name as warehouse_name, chemicals.id as chemical_id, chemicals.name as chemical_name, chemicals.state as chemical_state, COALESCE(chemical_types.name, 'Uncategorized') as chemical_type, COALESCE(SUM(warehouse_stocks.quantity_available), 0) as remaining")
            ->join('warehouses', 'warehouses.id', '=', 'warehouse_stocks.warehouse_id')
            ->join('chemicals', 'chemicals.id', '=', 'warehouse_stocks.chemical_id')
            ->leftJoin('chemical_types', 'chemical_types.id', '=', 'chemicals.type_id')
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

        return view('livewire.warehouses-grouped', [
            'groups' => $rows,
        ]);
    }
}
