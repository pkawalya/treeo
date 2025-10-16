<?php

namespace App\Filament\Resources\FarmerResource\Widgets;

use App\Models\Distribution;
use App\Models\Farmer;
use App\Models\Seedling;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FarmerDistributionStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected ?string $heading = 'Distribution Overview';
    protected int|string|array $columnSpan = 'full';

    public ?Model $record = null;

    protected function getStats(): array
    {
        if (!$this->record instanceof Farmer) {
            return [];
        }

        $totalDistributions = $this->record->distributions()->count();
        $totalSeedlings = $this->record->distributions()->sum('quantity');
        $totalCost = $this->record->distributions()->sum('cost');
        $lastDistribution = $this->record->distributions()->latest('distribution_date')->first();
        
        // Get most common seedling type
        $mostCommonSeedling = null;
        $mostCommonSeedlingCount = 0;
        
        if ($totalDistributions > 0) {
            $seedlingStats = DB::table('distributions')
                ->where('recipient_type', Farmer::class)
                ->where('recipient_id', $this->record->id)
                ->join('seedlings', 'distributions.seedling_id', '=', 'seedlings.id')
                ->select('seedlings.name', DB::raw('SUM(distributions.quantity) as total'))
                ->groupBy('seedlings.name')
                ->orderByDesc('total')
                ->first();
                
            if ($seedlingStats) {
                $mostCommonSeedling = $seedlingStats->name;
                $mostCommonSeedlingCount = $seedlingStats->total;
            }
        }

        // Calculate growth rate (percentage increase in last 3 months)
        $threeMonthsAgo = now()->subMonths(3);
        $recentSeedlings = $this->record->distributions()
            ->where('distribution_date', '>=', $threeMonthsAgo)
            ->sum('quantity');
        
        $previousSeedlings = $this->record->distributions()
            ->where('distribution_date', '<', $threeMonthsAgo)
            ->sum('quantity');
        
        $growthRate = 0;
        if ($previousSeedlings > 0) {
            $growthRate = (($recentSeedlings / $previousSeedlings) - 1) * 100;
        }

        return [
            Stat::make('Total Distributions', $totalDistributions)
                ->description('Distribution events')
                ->descriptionIcon('heroicon-o-truck')
                ->chart([$this->getMonthlyDistributionCounts()])
                ->color('primary'),
            
            Stat::make('Total Seedlings', number_format($totalSeedlings))
                ->description('Seedlings received')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            
            Stat::make('Most Common', $mostCommonSeedling ?? 'None')
                ->description($mostCommonSeedling ? number_format($mostCommonSeedlingCount) . ' seedlings' : 'No data')
                ->descriptionIcon('heroicon-o-star')
                ->color('warning'),
            
            Stat::make('Total Value', 'UGX ' . number_format($totalCost, 0))
                ->description('Value of distributions')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('danger'),
            
            Stat::make('Last Distribution', $lastDistribution ? $lastDistribution->distribution_date->format('M d, Y') : 'None')
                ->description($lastDistribution ? $lastDistribution->seedling->name ?? 'Unknown' : 'No distributions yet')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('info'),
                
            Stat::make('3-Month Growth', $growthRate > 0 ? '+' . number_format($growthRate, 1) . '%' : number_format($growthRate, 1) . '%')
                ->description('Compared to previous period')
                ->descriptionIcon($growthRate >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($growthRate >= 0 ? 'success' : 'danger'),
        ];
    }
    
    protected function getMonthlyDistributionCounts(): array
    {
        if (!$this->record instanceof Farmer) {
            return [0, 0, 0, 0, 0, 0];
        }
        
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();
        $monthlyCounts = [];
        
        for ($i = 0; $i < 6; $i++) {
            $month = now()->subMonths(5 - $i)->startOfMonth();
            $nextMonth = now()->subMonths(4 - $i)->startOfMonth();
            
            $count = $this->record->distributions()
                ->where('distribution_date', '>=', $month)
                ->where('distribution_date', '<', $nextMonth)
                ->count();
                
            $monthlyCounts[] = $count;
        }
        
        return $monthlyCounts;
    }
}
