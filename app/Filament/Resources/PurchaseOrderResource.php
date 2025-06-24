<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Filament\Resources\PurchaseOrderResource\RelationManagers;
use App\Models\PurchaseOrder;
use App\Models\Seedling;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Procurement';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Purchase Order';
    protected static ?string $pluralModelLabel = 'Purchase Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\TextInput::make('po_number')
                            ->label('PO Number')
                            ->default('PO-' . date('Ymd') . '-' . strtoupper(uniqid()))
                            ->required()
                            ->maxLength(50)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Select::make('vendor_id')
                            ->relationship('vendor', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $vendor = \App\Models\Vendor::find($state);
                                    if ($vendor) {
                                        $set('payment_terms', $vendor->payment_terms ?? '');
                                    }
                                }
                            }),
                        Forms\Components\DatePicker::make('order_date')
                            ->required()
                            ->default(now()->toDateString()),
                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->required()
                            ->default(now()->addWeek()->toDateString()),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Sent to Vendor',
                                'received' => 'Received',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required()
                            ->disabled(fn ($record) => $record && !$record->canBeEdited())
                            ->dehydrated(fn ($record) => !$record || $record->canBeEdited()),
                        Forms\Components\Textarea::make('delivery_terms')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('payment_terms')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Order Items')
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->relationship()
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
                            ])
                            ->columns(4)
                            ->columnSpanFull()
                            ->reorderable()
                            ->defaultItems(1)
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['seedling_id'] ? Seedling::find($state['seedling_id'])?->name : null),

                        Forms\Components\Placeholder::make('totals')
                            ->label('Order Totals')
                            ->content(function (\Closure $get) {
                                $items = $get('items');
                                $subtotal = collect($items)->sum('total');
                                $tax = $get('tax', 0);
                                $shipping = $get('shipping', 0);
                                $total = $subtotal + $tax + $shipping;

                                return view('filament.components.order-totals', [
                                    'subtotal' => $subtotal,
                                    'tax' => $tax,
                                    'shipping' => $shipping,
                                    'total' => $total,
                                ]);
                            })
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('subtotal')
                                    ->numeric()
                                    ->prefix('UGX')
                                    ->disabled()
                                    ->dehydrated()
                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->items->sum('total'));
                                        } else {
                                            $component->state(0);
                                        }
                                    }),
                                Forms\Components\TextInput::make('tax')
                                    ->numeric()
                                    ->prefix('UGX')
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function (\Closure $get, Forms\Set $set) {
                                        $subtotal = collect($get('items'))->sum('total');
                                        $tax = $get('tax', 0);
                                        $shipping = $get('shipping', 0);
                                        $set('total', $subtotal + $tax + $shipping);
                                    }),
                                Forms\Components\TextInput::make('shipping')
                                    ->numeric()
                                    ->prefix('UGX')
                                    ->default(0)
                                    ->minValue(0)
                                    ->live()
                                    ->afterStateUpdated(function (\Closure $get, Forms\Set $set) {
                                        $subtotal = collect($get('items'))->sum('total');
                                        $tax = $get('tax', 0);
                                        $shipping = $get('shipping', 0);
                                        $set('total', $subtotal + $tax + $shipping);
                                    }),
                                Forms\Components\TextInput::make('total')
                                    ->numeric()
                                    ->prefix('UGX')
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpan(3),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('po_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'received' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->money('UGX')
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'received' => 'Received',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('vendor')
                    ->relationship('vendor', 'name'),
                Tables\Filters\Filter::make('order_date')
                    ->form([
                        Forms\Components\DatePicker::make('ordered_from'),
                        Forms\Components\DatePicker::make('ordered_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['ordered_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '>=', $date),
                            )
                            ->when(
                                $data['ordered_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('order_date', '<=', $date),
                            );
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Model $record): bool => $record->canBeEdited()),
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn (Model $record): string => route('purchase-orders.print', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('send')
                    ->label('Send to Vendor')
                    ->icon('heroicon-o-paper-airplane')
                    ->action(fn (Model $record) => $record->update(['status' => 'sent']))
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => $record->status === 'draft')
                    ->color('warning'),
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->action(function (Model $record) {
                        $record->update([
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => $record->status === 'sent' && !$record->approved_at)
                    ->color('success'),
                Tables\Actions\Action::make('receive')
                    ->label('Receive Items')
                    ->icon('heroicon-o-truck')
                    ->url(fn (Model $record): string => static::getUrl('receive', ['record' => $record]))
                    ->visible(fn (Model $record): bool => in_array($record->status, ['sent', 'received']) && $record->approved_at)
                    ->color('success'),
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->action(fn (Model $record) => $record->update(['status' => 'cancelled']))
                    ->requiresConfirmation()
                    ->visible(fn (Model $record): bool => in_array($record->status, ['draft', 'sent']))
                    ->color('danger'),
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
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\GoodsReceiptsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'view' => Pages\ViewPurchaseOrder::route('/{record}'),
            'edit' => Pages\EditPurchaseOrder::route('/{record}/edit'),
            'receive' => Pages\ReceivePurchaseOrder::route('/{record}/receive'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['vendor', 'items.seedling'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
