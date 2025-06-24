<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardShortcuts;
use App\Filament\Widgets\FarmerLocationMap;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    

    public function getTitle(): string|Htmlable
    {
        return __('Dashboard');
    }
    
    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardShortcuts::class,
            FarmerLocationMap::class,
        ];
    }
}