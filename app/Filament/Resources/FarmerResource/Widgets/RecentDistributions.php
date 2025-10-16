<?php

namespace App\Filament\Resources\FarmerResource\Widgets;

use App\Models\Distribution;
use App\Models\Farmer;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class RecentDistributions extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Recent Distributions';
    protected static ?string $pollingInterval = null;
    
    public ?Model $record = null;
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Distribution::query()
                    ->where('recipient_type', Farmer::class)
                    ->where('recipient_id', $this->record?->id)
                    ->latest('distribution_date')
                    ->limit(5)
            )
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                Tables\Columns\TextColumn::make('distribution_date')
                    ->date('M d, Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('seedling.name')
                    ->label('Seedling')
                    ->formatStateUsing(function ($state, Distribution $record) {
                        $seedlingType = $record->seedling->type ?? 'Unknown';
                        $seedlingName = $state ?? 'Unknown';
                        
                        return new HtmlString(
                            '<div class="flex items-center space-x-2">' .
                            '<div class="w-3 h-3 rounded-full bg-' . $this->getSeedlingTypeColor($seedlingType) . '-500"></div>' .
                            '<span>' . e($seedlingName) . '</span>' .
                            '</div>'
                        );
                    })
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-o-cube')
                    ->color('success')
                    ->alignCenter()
                    ->size('lg')
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('cost')
                    ->money('UGX')
                    ->sortable()
                    ->icon('heroicon-o-currency-dollar')
                    ->color('warning'),
                    
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                    
                Tables\Columns\TextColumn::make('distributor.name')
                    ->label('Distributed By')
                    ->formatStateUsing(fn ($state) => $state ?? 'System')
                    ->icon('heroicon-o-user')
                    ->color('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(fn (Distribution $record): string => route('filament.admin.resources.distributions.view', $record)),
            ])
            ->emptyStateIcon('heroicon-o-truck')
            ->emptyStateHeading('No recent distributions')
            ->emptyStateDescription('This farmer has not received any seedling distributions yet.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Create Distribution')
                    ->icon('heroicon-o-plus')
                    ->url(route('filament.admin.resources.distributions.create'))
            ]);
    }
    
    protected function getSeedlingTypeColor(string $type): string
    {
        return match (strtolower($type)) {
            'fruit' => 'orange',
            'timber' => 'emerald',
            'medicinal' => 'purple',
            'ornamental' => 'pink',
            'indigenous' => 'blue',
            'exotic' => 'amber',
            default => 'gray',
        };
    }
}
