<?php

namespace App\Livewire\Concerns;

use App\Models\WarehouseStock;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\DB;

trait HasWarehouseSummaries
{
    /**
     * Summarize warehouse stocks grouped by chemical type for a numeric column
     */
    protected function summarizeByType(string $metric): HtmlString|string
    {
        $rows = WarehouseStock::query()
            ->join('chemicals', 'warehouse_stocks.chemical_id', '=', 'chemicals.id')
            ->leftJoin('chemical_types', 'chemical_types.id', '=', 'chemicals.type_id')
            ->select(DB::raw(sprintf("COALESCE(chemical_types.name, 'Uncategorized') as type, SUM(warehouse_stocks.%s) as total", $metric)))
            ->groupBy(DB::raw("COALESCE(chemical_types.name, 'Uncategorized')"))
            ->get();

        if ($rows->isEmpty()) {
            return '—';
        }

        $list = $rows
            ->map(fn ($r) => ucfirst($r->type) . ': ' . (int) $r->total)
            ->join('<br/>');

        return new HtmlString($list);
    }

    /**
     * Summarize warehouse stocks grouped by chemical state for a numeric column
     */
    protected function summarizeByState(string $metric): HtmlString|string
    {
        $rows = WarehouseStock::query()
            ->join('chemicals', 'warehouse_stocks.chemical_id', '=', 'chemicals.id')
            ->select('chemicals.state', DB::raw(sprintf('SUM(warehouse_stocks.%s) as total', $metric)))
            ->groupBy('chemicals.state')
            ->get();

        if ($rows->isEmpty()) {
            return '—';
        }

        $list = $rows
            ->map(fn ($r) => ucfirst($r->state) . ': ' . (int) $r->total)
            ->join('<br/>');

        return new HtmlString($list);
    }
}
