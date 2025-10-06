<?php

namespace App\Livewire;

use App\Models\Farmer;
use App\Models\FarmerGroup;
use App\Models\Cooperative;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CountsWidget extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = Auth::user();

        // 🔑 Determine the scope based on role
        $scope = null;
        if ($user->hasRole('dco')) {
            $scope = ['district_id' => $user->district_id];
        } elseif ($user->hasRole(['regional_manager', 'auditor'])) {
            $scope = ['region_id' => $user->region_id];
        }

        // Farmers
        $farmersCount = Farmer::when($scope, fn($q) => $q->where($scope))->count();
        $farmerGroupsCount = FarmerGroup::when($scope, fn($q) => $q->where($scope))->count();
        $cooperativesCount = Cooperative::when($scope, fn($q) => $q->where($scope))->count();

        // Warehouse stock (only relevant for admin + codapecrep)
        $totalAvailable = null;
        if ($user->hasRole('admin')) {
            $totalAvailable = DB::table('warehouse_stocks')->sum('quantity_available');
        } elseif ($user->hasRole('codapecrep')) {
            $totalAvailable = DB::table('warehouse_stocks')
                ->where('user_id', $user->id)
                ->whereIn('warehouse_id', $user->warehouses->pluck('id'))
                ->sum('quantity_available');
        }

        // 🧩 Build stats dynamically
        $stats = [];

        // Global view for Admin
        if ($user->hasRole('admin')) {
            $stats[] = Stat::make('Farmers', $farmersCount)
                ->description('Total registered farmers')
                ->icon('heroicon-o-user-group')
                ->color('primary');

            $stats[] = Stat::make('Farmer Groups', $farmerGroupsCount)
                ->description('Total farmer groups')
                ->icon('heroicon-o-user-group')
                ->color('primary');

            $stats[] = Stat::make('Cooperatives', $cooperativesCount)
                ->description('Total cooperatives')
                ->icon('heroicon-o-users')
                ->color('primary');

            $stats[] = Stat::make('Available Quantity', number_format((int)$totalAvailable))
                ->description('Total available across warehouses')
                ->icon('heroicon-o-cube')
                ->color('success');
        }

        // District / Region level (dco, regional_manager, auditor)
        if ($user->hasRole(['dco', 'regional_manager', 'auditor'])) {
            $scopeText = $user->hasRole('dco') 
                ? 'in your district' 
                : 'in your region';

            $stats[] = Stat::make('Farmers', $farmersCount)
                ->description("Total registered farmers $scopeText")
                ->icon('heroicon-o-user-group')
                ->color('primary');

            $stats[] = Stat::make('Farmer Groups', $farmerGroupsCount)
                ->description("Total farmer groups $scopeText")
                ->icon('heroicon-o-user-group')
                ->color('primary');

            $stats[] = Stat::make('Cooperatives', $cooperativesCount)
                ->description("Total cooperatives $scopeText")
                ->icon('heroicon-o-users')
                ->color('primary');
        }

        return $stats;
    }
}
