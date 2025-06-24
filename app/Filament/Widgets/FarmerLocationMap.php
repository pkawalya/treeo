<?php

namespace App\Filament\Widgets;

use App\Models\Farmer;
use Filament\Widgets\Widget;
use Filament\Widgets\Concerns\CanPoll;
use Filament\Support\Enums\IconPosition;

class FarmerLocationMap extends Widget
{
    use CanPoll;

    protected static string $view = 'filament.widgets.farmer-location-map';

    protected int | string | array $columnSpan = 'full';

    public function getPollingInterval(): ?string
    {
        return '30s';
    }

    protected function getViewData(): array
    {
        try {
            $farmers = Farmer::with('community')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get()
                ->map(function ($farmer) {
                    return [
                        'id' => $farmer->id,
                        'name' => $farmer->name,
                        'latitude' => (float)$farmer->latitude,
                        'longitude' => (float)$farmer->longitude,
                        'community' => $farmer->community?->name ?? 'No Community',
                        'trees_planted' => $farmer->trees_planted,
                    ];
                });

            $avgLat = $farmers->avg('latitude') ?? 1.3733;
            $avgLng = $farmers->avg('longitude') ?? 32.2903;

            return [
                'farmers' => $farmers,
                'center' => [
                    'lat' => (float)$avgLat,
                    'lng' => (float)$avgLng,
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('Error in FarmerLocationMap widget: ' . $e->getMessage());
            
            // Return default data on error
            return [
                'farmers' => [],
                'center' => [
                    'lat' => 1.3733,
                    'lng' => 32.2903,
                ],
            ];
        }
    }
}
