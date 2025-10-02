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
        $farmersCount = Farmer::count();
        $farmerGroupsCount = FarmerGroup::count();
        $cooperativesCount = Cooperative::count();

        // district level
        $farmersDistrictCount = Farmer::where('district_id', $user->district_id)->count();
        $farmerGroupsDistrictCount = FarmerGroup::where('district_id', $user->district_id)->count();
        $cooperativesDistrictCount = Cooperative::where('district_id', $user->district_id)->count();


        $totalAvailable = DB::table('warehouse_stocks')
            ->when(
                $user->hasRole('codapecrep'),
                fn($query) => $query->where('user_id', $user->id)
                                                                ->whereIn('warehouse_id', $user->warehouses->pluck('id'))
            )->sum('quantity_available');

        return [
            Stat::make('Farmers', $farmersCount)
                ->visible(fn() => Auth::user()->hasRole('admin') )
                ->description('Total registered farmers')
                ->icon('heroicon-o-user-group')
                ->color('primary'),


            Stat::make('Farmer Groups', $farmerGroupsCount)
                ->visible(fn() => Auth::user()->hasRole('admin') )
                ->description('Total farmer groups')
                ->icon('heroicon-o-user-group')
                ->color('primary'),

           

            Stat::make('Cooperatives', $cooperativesCount)
                ->visible(fn() => Auth::user()->hasRole('admin') )
                ->description('Total cooperatives')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Available Quantity', number_format((int)$totalAvailable))
                ->visible(fn() => Auth::user()->hasRole('admin'))
                ->description('Total available across warehouses')
                ->icon('heroicon-o-cube')
                ->color('success'),


          //district level
             Stat::make('Farmers', $farmersDistrictCount)
                ->visible(fn() => Auth::user()->hasRole('dco') )
                ->description('Total registered farmers in your district')
                ->icon('heroicon-o-user-group')
                ->color('primary'),
             
                Stat::make('Farmer Groups', $farmerGroupsDistrictCount)
                    ->visible(fn() => Auth::user()->hasRole('dco') )
                    ->description('Total farmer groups in your district')
                    ->icon('heroicon-o-user-group')
                    ->color('primary'),
                Stat::make('Cooperatives', $cooperativesDistrictCount)
                    ->visible(fn() => Auth::user()->hasRole('dco') )
                    ->description('Total cooperatives in your district')
                    ->icon('heroicon-o-users')
                    ->color('primary'),
            //district level end
        ];
    }
}
