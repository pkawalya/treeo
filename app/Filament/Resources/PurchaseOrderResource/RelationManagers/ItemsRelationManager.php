<?php

namespace App\Filament\Resources\PurchaseOrderResource\RelationManagers;

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
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $seedling = Seedling::find($state);
                            if ($seedling) {
                                $set('description', $seedling->description);
                                $set('unit_price', $seedling->latest_price ?? 0);
                            }
                        }
                    })
                    ->columnSpan(2),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Closure $get, Forms\Set $set) {
                        $quantity = $get('quantity');
                        $unitPrice = $get('unit_price');
                        $set('total', $quantity * $unitPrice);
                    }),
                Forms\Components\TextInput::make('unit')
                    ->default('pcs')
                    ->maxLength(10)
                    ->required(),
                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('UGX')
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (\Closure $get, Forms\Set $set) {
                        $quantity = $get('quantity');
                        $unitPrice = $get('unit_price');
                        $set('total', $quantity * $unitPrice);
                    }),
                Forms\Components\TextInput::make('total')
                    ->numeric()
                    ->prefix('UGX')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Textarea::make('description')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('seedling.name')
            ->columns([
                Tables\Columns\TextColumn::make('seedling.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->money('UGX')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->money('UGX')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['total'] = $data['quantity'] * $data['unit_price'];
                        return $data;
                    })
                    ->visible(fn () => $this->getOwnerRecord()->canBeEdited()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->canBeEdited()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->canBeEdited()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => $this->getOwnerRecord()->canBeEdited()),
                ]),
            ]);
    }
}
