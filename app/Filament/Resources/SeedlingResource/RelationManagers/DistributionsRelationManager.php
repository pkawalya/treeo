<?php

namespace App\Filament\Resources\SeedlingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class DistributionsRelationManager extends RelationManager
{
    protected static string $relationship = 'distributions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('recipient_type')
                    ->label('Recipient Type')
                    ->options([
                        'farmer' => 'Farmer',
                        'community' => 'Community',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Select::make('recipient_id')
                    ->label('Recipient')
                    ->searchable()
                    ->options(function ($get) {
                        $type = $get('recipient_type');
                        if (!$type) return [];
                        
                        $model = $type === 'farmer' ? '\App\Models\Farmer' : '\App\Models\Community';
                        return $model::pluck('name', 'id');
                    })
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\DatePicker::make('distribution_date')
                    ->required()
                    ->default(now()->toDateString()),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('recipient.name')
                    ->label('Recipient')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('distribution_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
