<?php

namespace App\Livewire;

use App\Models\Chemical;
use App\Models\ChemicalRequest;
use App\Models\Dispatch;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Chemicalstat extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = [];

        // Get all chemicals
        $chemicals = Chemical::all();

        foreach ($chemicals as $chemical) {
            // Total requested for this chemical
            $totalRequested = ChemicalRequest::where('chemical_id', $chemical->id)->sum('quantity');

            // Total approved
            $totalApproved = ChemicalRequest::where('chemical_id', $chemical->id)
                ->where('status', 'approved')
                ->sum('quantity');

            // Total dispatched
            $totalDispatched = Dispatch::whereHas('chemicalRequest', function ($query) use ($chemical) {
                $query->where('chemical_id', $chemical->id);
            })->sum('quantity');

            // Remaining quantity
            $totalRemaining = $totalApproved - $totalDispatched;

            $stats[] = Stat::make(
                $chemical->name,       // Label
                $totalRemaining        // Main value
            )
            ->description("Requested: $totalRequested | Approved: $totalApproved | Dispatched: $totalDispatched")
            ->descriptionIcon($totalRemaining > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($totalRemaining > 0 ? 'success' : 'danger');
        }

        return $stats;
    }
}
