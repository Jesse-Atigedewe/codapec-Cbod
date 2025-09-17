<?php

namespace App\Livewire;

use App\Models\Cooperative;
use App\Models\Farmer;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MembersWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $farmerscount = Farmer::count();
        $farmergroupscount = Farmer::count();
        $cooperativescount = Cooperative::count();
        return [
            Stat::make('Total Farmers',$farmerscount)
                ->description('Total Farmers')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary')
                ->icon('heroicon-o-users'),
            Stat::make('Total Farmer Groups',$farmergroupscount)
                ->description('Total Farmer Groups')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary')
                ->icon('heroicon-o-users'),
            Stat::make('Total Cooperatives',$cooperativescount)
                ->description('Total Cooperatives')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary')
                ->icon('heroicon-o-users'),
        ];
    }
}
