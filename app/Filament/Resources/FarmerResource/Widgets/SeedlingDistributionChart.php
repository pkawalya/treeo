<?php

namespace App\Filament\Resources\FarmerResource\Widgets;

use App\Models\Distribution;
use App\Models\Farmer;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SeedlingDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Seedling Distribution Analysis';
    protected static ?string $pollingInterval = null;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '300px';
    
    public ?Model $record = null;
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'callbacks' => [
                        'label' => "function(context) {\n"
                            . "    return context.dataset.label + ': ' + context.raw + ' seedlings';\n"
                            . "}",
                    ],
                ],
                'datalabels' => [
                    'color' => '#ffffff',
                    'font' => [
                        'weight' => 'bold',
                    ],
                    'formatter' => "function(value, context) {\n"
                        . "    return value > 0 ? value : '';\n"
                        . "}",
                ],
            ],
            'cutout' => '60%',
            'elements' => [
                'arc' => [
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ],
            ],
        ];
    }
    
    protected function getData(): array
    {
        if (!$this->record instanceof Farmer) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        // Get seedling distribution by type
        $byType = Distribution::query()
            ->where('recipient_type', Farmer::class)
            ->where('recipient_id', $this->record->id)
            ->join('seedlings', 'distributions.seedling_id', '=', 'seedlings.id')
            ->select('seedlings.type', DB::raw('SUM(distributions.quantity) as total_quantity'))
            ->groupBy('seedlings.type')
            ->get();
            
        // Get seedling distribution by name (for top 5)
        $byName = Distribution::query()
            ->where('recipient_type', Farmer::class)
            ->where('recipient_id', $this->record->id)
            ->join('seedlings', 'distributions.seedling_id', '=', 'seedlings.id')
            ->select('seedlings.name', DB::raw('SUM(distributions.quantity) as total_quantity'))
            ->groupBy('seedlings.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Determine which chart to show based on data availability
        if ($byType->isEmpty() && $byName->isEmpty()) {
            return [
                'datasets' => [
                    [
                        'label' => 'No Data',
                        'data' => [1],
                        'backgroundColor' => ['#e2e8f0'],
                    ],
                ],
                'labels' => ['No seedling data available'],
            ];
        }
        
        // Use type data if available, otherwise use name data
        $data = $byType->isNotEmpty() ? $byType : $byName;
        $labelField = $byType->isNotEmpty() ? 'type' : 'name';
        
        // Generate vibrant colors with transparency
        $baseColors = [
            '#10B981', // emerald
            '#3B82F6', // blue
            '#F59E0B', // amber
            '#EF4444', // red
            '#8B5CF6', // violet
            '#EC4899', // pink
            '#06B6D4', // cyan
            '#84CC16', // lime
            '#F97316', // orange
            '#6366F1', // indigo
        ];
        
        $colors = [];
        foreach ($data as $index => $item) {
            $colorIndex = $index % count($baseColors);
            $colors[] = $baseColors[$colorIndex];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Seedlings Received',
                    'data' => $data->pluck('total_quantity')->toArray(),
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                    'hoverOffset' => 10,
                ],
            ],
            'labels' => $data->pluck($labelField)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
