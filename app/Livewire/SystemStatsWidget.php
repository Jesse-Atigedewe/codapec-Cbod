<?php

namespace App\Livewire;

use App\Models\Chemical;
use App\Models\Dispatch;
use App\Models\Farmer;
use App\Models\FarmerGroup;
use App\Models\Cooperative;
use App\Models\Warehouse;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SystemStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalUsers = 0; // placeholder if there's a User model, keep 0 if not available
        try {
            $totalUsers = \App\Models\User::count();
        } catch (\Throwable $e) {
            $totalUsers = 0;
        }

        $chemicalsCount = Chemical::count();
        $warehousesCount = Warehouse::count();
        $farmersCount = Farmer::count();
        $farmerGroupsCount = FarmerGroup::count();
        $cooperativesCount = Cooperative::count();
        $dispatchesCount = Dispatch::count();

        // total available quantity across warehouse stocks
    $totalAvailable = DB::table('warehouse_stocks')->sum('quantity_available');

        return [
            Stat::make('Chemicals', $chemicalsCount)
                ->description('Total different chemicals')
                ->icon('heroicon-o-beaker')
                ->color('primary'),

            Stat::make('Available Quantity', number_format((int)$totalAvailable))
                ->description('Total available across warehouses')
                ->icon('heroicon-o-cube')
                ->color('success'),

            Stat::make('Farmers', $farmersCount)
                ->description('Total Farmers')
                ->icon('heroicon-o-user')
                ->color('primary'),

            Stat::make('Dispatches', $dispatchesCount)
                ->description('Total Dispatch records')
                ->icon('heroicon-o-truck')
                ->color('danger'),
        ];
    }
}
