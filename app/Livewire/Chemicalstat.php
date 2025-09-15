<?php

namespace App\Livewire;

use App\Models\Chemical;
use App\Models\ChemicalRequest;
use App\Models\Dispatch;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ChemicalStat extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $stats = [];

        // Fetch all chemicals
        $chemicals = Chemical::all();

        foreach ($chemicals as $chemical) {
            // Compute stats for each chemical
            $totalRequested   = $this->getTotalRequested($chemical->id);
            $totalApproved    = $this->getTotalApproved($chemical->id);
            $totalDispatched  = $this->getTotalDispatched($chemical->id);
            $totalRemaining   = $totalApproved - $totalDispatched;

            // Build Stat card
            $stats[] = Stat::make(
                $chemical->name,
                number_format($totalRemaining) // Format for readability
            )
            ->description(
                "Requested: " . number_format($totalRequested) .
                " | Approved: " . number_format($totalApproved) .
                " | Dispatched: " . number_format($totalDispatched)
            )
            ->descriptionIcon(
                $totalRemaining > 0
                    ? 'heroicon-m-arrow-trending-up'
                    : 'heroicon-m-arrow-trending-down'
            )
            ->color(
                $totalRemaining > 0 ? 'success' : 'danger'
            );
        }

        return $stats;
    }

    /**
     * Get total requested quantity for a chemical.
     */
    protected function getTotalRequested(int $chemicalId): int
    {
        return ChemicalRequest::where('chemical_id', $chemicalId)->sum('quantity');
    }

    /**
     * Get total approved quantity for a chemical.
     */
    protected function getTotalApproved(int $chemicalId): int
    {
        return ChemicalRequest::where('chemical_id', $chemicalId)
            ->where('status', 'approved')
            ->sum('quantity');
    }

    /**
     * Get total dispatched quantity for a chemical.
     */
    protected function getTotalDispatched(int $chemicalId): int
    {
        return Dispatch::whereHas('chemicalRequest', function ($query) use ($chemicalId) {
            $query->where('chemical_id', $chemicalId);
        })->sum('quantity');
    }
}
