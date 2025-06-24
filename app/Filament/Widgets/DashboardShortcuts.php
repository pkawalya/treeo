<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Route;

class DashboardShortcuts extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-shortcuts';
    protected int | string | array $columnSpan = 'full';

    protected function getShortcuts(): array
    {
        $shortcuts = [];

        // Farmers Section
        if (Route::has('filament.admin.resources.farmers.index')) {
            $shortcuts[] = [
                'label' => 'View Farmers',
                'icon' => 'heroicon-o-user-group',
                'url' => route('filament.admin.resources.farmers.index'),
                'color' => 'primary',
            ];
        }
        if (Route::has('filament.admin.resources.farmers.create')) {
            $shortcuts[] = [
                'label' => 'Add Farmer',
                'icon' => 'heroicon-o-user-plus',
                'url' => route('filament.admin.resources.farmers.create'),
                'color' => 'primary',
                'variant' => 'outline',
            ];
        }

        // Communities
        if (Route::has('filament.admin.resources.communities.index')) {
            $shortcuts[] = [
                'label' => 'Communities',
                'icon' => 'heroicon-o-map',
                'url' => route('filament.admin.resources.communities.index'),
                'color' => 'emerald',
            ];
        }

        // Seedlings
        if (Route::has('filament.admin.resources.seedlings.index')) {
            $shortcuts[] = [
                'label' => 'Seedlings',
                'icon' => 'heroicon-o-sparkles', // Using sparkles as an alternative to leaf
                'url' => route('filament.admin.resources.seedlings.index'),
                'color' => 'green',
            ];
        }

        // Inventory
        if (Route::has('filament.admin.resources.inventories.index')) {
            $shortcuts[] = [
                'label' => 'Inventory',
                'icon' => 'heroicon-o-cube',
                'url' => route('filament.admin.resources.inventories.index'),
                'color' => 'amber',
            ];
        }

        // Purchase Orders
        if (Route::has('filament.admin.resources.purchase-orders.index')) {
            $shortcuts[] = [
                'label' => 'Purchase Orders',
                'icon' => 'heroicon-o-document-text',
                'url' => route('filament.admin.resources.purchase-orders.index'),
                'color' => 'blue',
            ];
        }

        // Goods Receipts
        if (Route::has('filament.admin.resources.goods-receipts.index')) {
            $shortcuts[] = [
                'label' => 'Goods Receipts',
                'icon' => 'heroicon-o-clipboard-document-check',
                'url' => route('filament.admin.resources.goods-receipts.index'),
                'color' => 'indigo',
            ];
        }

        // Distributions
        if (Route::has('filament.admin.resources.distributions.index')) {
            $shortcuts[] = [
                'label' => 'Distributions',
                'icon' => 'heroicon-o-truck',
                'url' => route('filament.admin.resources.distributions.index'),
                'color' => 'violet',
            ];
        }

        // Vendors
        if (Route::has('filament.admin.resources.vendors.index')) {
            $shortcuts[] = [
                'label' => 'Vendors',
                'icon' => 'heroicon-o-building-storefront',
                'url' => route('filament.admin.resources.vendors.index'),
                'color' => 'fuchsia',
            ];
        }

        // Monitoring
        if (Route::has('filament.admin.resources.monitorings.index')) {
            $shortcuts[] = [
                'label' => 'Monitoring',
                'icon' => 'heroicon-o-chart-bar',
                'url' => route('filament.admin.resources.monitorings.index'),
                'color' => 'pink',
            ];
        }

        // Add a default dashboard shortcut if no other shortcuts are available
        if (empty($shortcuts)) {
            $shortcuts[] = [
                'label' => 'Dashboard',
                'icon' => 'heroicon-o-home',
                'url' => route('filament.admin.pages.dashboard'),
                'color' => 'primary',
            ];
        }

        return $shortcuts;
    }

    public function getViewData(): array
    {
        return [
            'shortcuts' => $this->getShortcuts(),
        ];
    }
}
