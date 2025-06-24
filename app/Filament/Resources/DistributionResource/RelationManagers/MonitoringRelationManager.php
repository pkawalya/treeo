<?php

namespace App\Filament\Resources\DistributionResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MonitoringRelationManager extends RelationManager
{
    protected static string $relationship = 'monitoring';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('growth_stage')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('observation_date')
                    ->required()
                    ->default(now()->toDateString()),
                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image_urls')
                    ->multiple()
                    ->image()
                    ->directory('monitoring')
                    ->imageEditor()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('latitude')
                    ->numeric()
                    ->step('0.000001')
                    ->maxLength(20),
                Forms\Components\TextInput::make('longitude')
                    ->numeric()
                    ->step('0.000001')
                    ->maxLength(20),
                Forms\Components\KeyValue::make('environmental_conditions')
                    ->keyLabel('Condition')
                    ->valueLabel('Value')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('growth_stage')
            ->columns([
                Tables\Columns\TextColumn::make('growth_stage')
                    ->searchable(),
                Tables\Columns\TextColumn::make('observation_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['supervisor_id'] = auth()->id();
                        return $data;
                    }),
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
