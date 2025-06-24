<?php

namespace App\Filament\Resources\SeedlingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InventoryRelationManager extends RelationManager
{
    protected static string $relationship = 'inventory';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->minValue(0.01)
                    ->prefix('$'),
                Forms\Components\TextInput::make('batch_number')
                    ->required()
                    ->maxLength(100),
                Forms\Components\DatePicker::make('procurement_date')
                    ->required()
                    ->default(now()->toDateString()),
                Forms\Components\DatePicker::make('expiry_date'),
                Forms\Components\Select::make('status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'reserved' => 'Reserved',
                        'distributed' => 'Distributed',
                    ])
                    ->default('in_stock')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('batch_number')
            ->columns([
                Tables\Columns\TextColumn::make('batch_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('procurement_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in_stock' => 'success',
                        'reserved' => 'warning',
                        'distributed' => 'info',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'in_stock' => 'In Stock',
                        'reserved' => 'Reserved',
                        'distributed' => 'Distributed',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
