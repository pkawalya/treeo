<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MonitoringResource\Pages;
use App\Models\Monitoring;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MonitoringResource extends Resource
{
    protected static ?string $model = Monitoring::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Monitoring';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Monitoring Details')
                    ->schema([
                        Forms\Components\Select::make('distribution_id')
                            ->relationship('distribution', 'id')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('growth_stage')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('observation_date')
                            ->required()
                            ->default(now()->toDateString()),
                        Forms\Components\Select::make('supervisor_id')
                            ->relationship('supervisor', 'name')
                            ->searchable()
                            ->preload()
                            ->default(auth()->id())
                            ->required(),
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
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('distribution.recipient.name')
                    ->label('Recipient')
                    ->searchable(),
                Tables\Columns\TextColumn::make('distribution.seedling.name')
                    ->label('Seedling')
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMonitorings::route('/'),
            'create' => Pages\CreateMonitoring::route('/create'),
            'view' => Pages\ViewMonitoring::route('/{record}'),
            'edit' => Pages\EditMonitoring::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
