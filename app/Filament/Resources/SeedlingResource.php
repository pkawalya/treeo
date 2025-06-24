<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeedlingResource\Pages;
use App\Filament\Resources\SeedlingResource\RelationManagers;
use App\Models\Seedling;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeedlingResource extends Resource
{
    protected static ?string $model = Seedling::class;

    protected static ?string $navigationIcon = 'heroicon-s-sparkles';
    protected static ?string $navigationGroup = 'Inventory Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Seedling Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('type')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Textarea::make('description')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('image_url')
                            ->image()
                            ->directory('seedlings')
                            ->imageEditor()
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Growth Stages')
                    ->schema([
                        Forms\Components\Repeater::make('growth_stages')
                            ->schema([
                                Forms\Components\TextInput::make('stage')
                                    ->required()
                                    ->placeholder('e.g., Germination'),
                                Forms\Components\TextInput::make('duration')
                                    ->required()
                                    ->placeholder('e.g., 2-3 weeks'),
                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->placeholder('Description of this stage'),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&color=7F9CF5&background=EBF4FF'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('inventories_sum_quantity')
                    ->label('Total Inventory')
                    ->sum('inventories', 'quantity')
                    ->sortable(),
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
            RelationManagers\InventoryRelationManager::class,
            RelationManagers\DistributionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeedlings::route('/'),
            'create' => Pages\CreateSeedling::route('/create'),
            'view' => Pages\ViewSeedling::route('/{record}'),
            'edit' => Pages\EditSeedling::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withSum('inventories', 'quantity')
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
