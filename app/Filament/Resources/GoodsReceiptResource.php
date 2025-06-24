<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GoodsReceiptResource\Pages;
use App\Filament\Resources\GoodsReceiptResource\RelationManagers;
use App\Models\GoodsReceipt;
use App\Models\PurchaseOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GoodsReceiptResource extends Resource
{
    protected static ?string $model = GoodsReceipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Procurement';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Goods Receipt';
    protected static ?string $pluralModelLabel = 'Goods Receipts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Receipt Information')
                    ->schema([
                        Forms\Components\TextInput::make('grn_number')
                            ->label('GRN Number')
                            ->default('GRN-' . date('Ymd') . '-' . strtoupper(uniqid()))
                            ->required()
                            ->maxLength(50)
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Select::make('purchase_order_id')
                            ->relationship('purchaseOrder', 'po_number')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $po = PurchaseOrder::find($state);
                                    if ($po) {
                                        $set('vendor_id', $po->vendor_id);
                                        $set('vendor_name', $po->vendor->name);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('vendor_name')
                            ->label('Vendor')
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\DatePicker::make('receipt_date')
                            ->required()
                            ->default(now()->toDateString()),
                        Forms\Components\TextInput::make('received_by')
                            ->required()
                            ->maxLength(255)
                            ->default(auth()->user()->name),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'received' => 'Received',
                                'inspected' => 'Inspected',
                                'rejected' => 'Rejected',
                                'cancelled' => 'Cancelled',
                            ])
                            ->default('draft')
                            ->required(),
                        Forms\Components\Textarea::make('notes')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('grn_number')
                    ->label('GRN #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchaseOrder.po_number')
                    ->label('PO #')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('purchaseOrder.vendor.name')
                    ->label('Vendor')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_date')
                    ->label('Receipt Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_quantity')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('UGX')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'received',
                        'info' => 'inspected',
                        'success' => 'completed',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'received' => 'Received',
                        'inspected' => 'Inspected',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('vendor_id')
                    ->relationship('purchaseOrder.vendor', 'name')
                    ->label('Vendor'),
                Tables\Filters\Filter::make('receipt_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('receipt_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('receipt_date', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Receipt from ' . \Carbon\Carbon::parse($data['from'])->toFormattedDateString();
                        }
                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Receipt until ' . \Carbon\Carbon::parse($data['until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (\App\Models\GoodsReceipt $record): bool => $record->status === 'draft'),
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (\App\Models\GoodsReceipt $record): string => goods_receipt_print_url($record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('receive')
                    ->label('Mark as Received')
                    ->icon('heroicon-o-check-circle')
                    ->action(fn (\App\Models\GoodsReceipt $record) => $record->update(['status' => 'received']))
                    ->requiresConfirmation()
                    ->visible(fn (\App\Models\GoodsReceipt $record): bool => $record->status === 'draft')
                    ->color('success'),
                Tables\Actions\Action::make('inspect')
                    ->label('Mark as Inspected')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->action(function (\App\Models\GoodsReceipt $record) {
                        $record->update(['status' => 'inspected']);
                        $record->updateInventory();
                    })
                    ->requiresConfirmation()
                    ->visible(fn (\App\Models\GoodsReceipt $record): bool => $record->status === 'received')
                    ->color('success'),
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required(),
                    ])
                    ->action(function (array $data, \App\Models\GoodsReceipt $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'notes' => ($record->notes ? $record->notes . "\n\n" : '') . 'Rejection Reason: ' . $data['rejection_reason'],
                        ]);
                    })
                    ->requiresConfirmation()
                    ->visible(fn (\App\Models\GoodsReceipt $record): bool => in_array($record->status, ['draft', 'received']))
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGoodsReceipts::route('/'),
            'create' => Pages\CreateGoodsReceipt::route('/create'),
            'view' => Pages\ViewGoodsReceipt::route('/{record}'),
            'edit' => Pages\EditGoodsReceipt::route('/{record}/edit'),
            'print' => Pages\PrintGoodsReceipt::route('/{record}/print'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['purchaseOrder', 'purchaseOrder.vendor'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
