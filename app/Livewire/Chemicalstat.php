<?php

namespace App\Livewire;

use App\Models\Chemical;
use App\Models\ChemicalRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ChemicalStat extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // Get all chemicals upfront
        $chemicals = Chemical::all();

        // Aggregate requests by chemical_id
        $requests = ChemicalRequest::select(
                'chemical_id',
                DB::raw('SUM(quantity) as total_requested'),
                DB::raw("SUM(CASE WHEN status = 'approved' THEN quantity ELSE 0 END) as total_approved")
            )
            ->groupBy('chemical_id')
            ->get()
            ->keyBy('chemical_id');

        return $chemicals->map(function (Chemical $chemical) use ($requests) {
            $data = $requests->get($chemical->id);

            $totalRequested = $data->total_requested ?? 0;
            $totalApproved  = $data->total_approved ?? 0;
            $totalRemaining = $totalRequested - $totalApproved;

            return Stat::make($chemical->name, number_format($totalRemaining))
                ->description(
                    "Requested: " . number_format($totalRequested) .
                    " | Approved: " . number_format($totalApproved)
                )
                ->descriptionIcon(
                    $totalRemaining > 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($totalRemaining > 0 ? 'success' : 'danger');
        })->toArray();
    }
}
