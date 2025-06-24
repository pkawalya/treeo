<?php

namespace App\Filament\Resources\GoodsReceiptResource\RelationManagers;

use App\Models\Seedling;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('seedling_id')
                    ->label('Seedling')
                    ->options(Seedling::pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->disabled(fn ($record) => $record?->exists)
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $seedling = Seedling::find($state);
                            if ($seedling) {
                                $set('description', $seedling->description);
                            }
                        }
                    })
                    ->columnSpan(2),
                Forms\Components\TextInput::make('quantity_ordered')
                    ->label('Ordered Qty')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('quantity_received')
                    ->label('Received Qty')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Closure $get, Forms\Set $set) {
                        $qty = (float) $get('quantity_received');
                        $unitPrice = (float) $get('unit_price');
                        $set('total', number_format($qty * $unitPrice, 2, '.', ''));
                    }),
                Forms\Components\TextInput::make('unit_price')
                    ->label('Unit Price')
                    ->numeric()
                    ->prefix('UGX')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Closure $get, Forms\Set $set) {
                        $qty = (float) $get('quantity_received');
                        $unitPrice = (float) $get('unit_price');
                        $set('total', number_format($qty * $unitPrice, 2, '.', ''));
                    }),
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->prefix('UGX')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('batch_number')
                    ->label('Batch #')
                    ->required()
                    ->maxLength(50),
                Forms\Components\DatePicker::make('expiry_date')
                    ->label('Expiry Date')
                    ->required()
                    ->minDate(now()),
                Forms\Components\Select::make('condition')
                    ->options([
                        'good' => 'Good',
                        'damaged' => 'Damaged',
                        'expired' => 'Expired',
                        'partial' => 'Partially Damaged',
                    ])
                    ->required()
                    ->default('good'),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('notes')
                    ->label('Item Notes')
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('seedling.name')
            ->columns([
                Tables\Columns\TextColumn::make('seedling.name')
                    ->label('Seedling')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity_ordered')
                    ->label('Ordered')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_received')
                    ->label('Received')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('UGX')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('UGX')
                    ->sortable(),
                Tables\Columns\TextColumn::make('batch_number')
                    ->label('Batch #')
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'good' => 'success',
                        'damaged' => 'danger',
                        'expired' => 'danger',
                        'partial' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['total'] = $data['quantity_received'] * $data['unit_price'];
                        return $data;
                    })
                    ->visible(fn () => $this->getOwnerRecord()->status === 'draft'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->status === 'draft'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->status === 'draft'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => $this->getOwnerRecord()->status === 'draft'),
                ]),
            ]);
    }
}
