<?php

namespace App\Filament\Widgets;

use App\Models\Farmer;
use App\Models\Community;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        $totalFarmers = Farmer::count();
        $totalTreesPlanted = Farmer::sum('trees_planted');
        $totalCommunities = Community::count();
        $averageTreesPerFarmer = $totalFarmers > 0 ? round($totalTreesPlanted / $totalFarmers, 2) : 0;

        return [
            Stat::make('Total Farmers', $totalFarmers)
                ->description('All registered farmers')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),
            Stat::make('Total Trees Planted', number_format($totalTreesPlanted))
                ->description('Across all communities')
                ->descriptionIcon('heroicon-o-sparkles')
                ->color('success'),
            Stat::make('Total Communities', $totalCommunities)
                ->description('Farmer communities')
                ->descriptionIcon('heroicon-o-map-pin')
                ->color('info'),
            Stat::make('Average Trees per Farmer', $averageTreesPerFarmer)
                ->description('An average measure')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('warning'),
        ];
    }
}
