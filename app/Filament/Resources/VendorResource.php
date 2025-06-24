<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendorResource\Pages;
use App\Filament\Resources\VendorResource\RelationManagers;
use App\Models\Vendor;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class VendorResource extends Resource
{
    protected static ?string $model = Vendor::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Procurement';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Supplier';
    protected static ?string $pluralModelLabel = 'Suppliers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Vendor Information')
                    ->tabs([
                        // Basic Information Tab
                        Forms\Components\Tabs\Tab::make('Basic Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Company Details')
                                    ->description('Basic information about the vendor')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('company_registration')
                                            ->label('Registration #')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('tax_identification_number')
                                            ->label('TIN')
                                            ->maxLength(50),
                                        Forms\Components\Select::make('vendor_type')
                                            ->options([
                                                'nursery' => 'Nursery',
                                                'supplier' => 'Supplier',
                                                'contractor' => 'Contractor',
                                                'service' => 'Service Provider',
                                                'other' => 'Other',
                                            ])
                                            ->required()
                                            ->default('supplier'),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Active Vendor')
                                            ->default(true)
                                            ->required(),
                                    ])->columns(3),

                                Forms\Components\Section::make('Contact Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('contact_person')
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('contact_position')
                                            ->maxLength(255)
                                            ->columnSpan(2),
                                        Forms\Components\TextInput::make('email')
                                            ->email()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('phone')
                                            ->tel()
                                            ->required()
                                            ->maxLength(20),
                                        Forms\Components\TextInput::make('alternative_phone')
                                            ->tel()
                                            ->maxLength(20),
                                    ])->columns(3),

                                Forms\Components\Section::make('Address')
                                    ->schema([
                                        Forms\Components\Textarea::make('address')
                                            ->maxLength(65535)
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('city')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('state')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('country')
                                            ->default('Uganda')
                                            ->maxLength(100),
                                        Forms\Components\TextInput::make('postal_code')
                                            ->maxLength(20),
                                    ])->columns(3),
                            ]),

                        // Financial Details Tab
                        Forms\Components\Tabs\Tab::make('Financial Details')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Forms\Components\Section::make('Banking Information')
                                    ->schema([
                                        Forms\Components\TextInput::make('bank_name')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('bank_branch')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('bank_account_number')
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('bank_account_name')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('swift_code')
                                            ->label('SWIFT/BIC Code')
                                            ->maxLength(50),
                                    ])->columns(2),

                                Forms\Components\Section::make('Payment Terms')
                                    ->schema([
                                        Forms\Components\Select::make('payment_terms')
                                            ->options([
                                                'prepaid' => 'Prepaid',
                                                'on_delivery' => 'On Delivery',
                                                'net7' => 'Net 7 Days',
                                                'net15' => 'Net 15 Days',
                                                'net30' => 'Net 30 Days',
                                                'net60' => 'Net 60 Days',
                                                'end_of_month' => 'End of Month',
                                            ])
                                            ->default('net30')
                                            ->required(),
                                        Forms\Components\TextInput::make('credit_limit')
                                            ->numeric()
                                            ->prefix('UGX'),
                                        Forms\Components\TextInput::make('currency')
                                            ->default('UGX')
                                            ->maxLength(3),
                                    ])->columns(3),
                            ]),

                        // Additional Info Tab
                        Forms\Components\Tabs\Tab::make('Additional Info')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Classification')
                                    ->schema([
                                        Forms\Components\Select::make('preferred_supplier')
                                            ->options([
                                                'yes' => 'Yes',
                                                'no' => 'No',
                                            ])
                                            ->default('no')
                                            ->required(),
                                        Forms\Components\Select::make('vendor_rating')
                                            ->options([
                                                'A' => 'A - Excellent',
                                                'B' => 'B - Good',
                                                'C' => 'C - Average',
                                                'D' => 'D - Poor',
                                                'F' => 'F - Unreliable',
                                            ]),
                                        Forms\Components\TagsInput::make('categories')
                                            ->suggestions([
                                                'Seedlings', 'Fertilizers', 'Pesticides', 'Equipment', 'Services', 'Consultancy', 'Logistics'
                                            ])
                                            ->placeholder('Add categories')
                                            ->columnSpanFull(),
                                    ])->columns(2),

                                Forms\Components\Section::make('Notes & Documents')
                                    ->schema([
                                        Forms\Components\FileUpload::make('documents')
                                            ->multiple()
                                            ->directory('vendor-documents')
                                            ->downloadable()
                                            ->openable()
                                            ->previewable()
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('notes')
                                            ->maxLength(65535)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Vendor $record): string => $record->vendor_type ? ucfirst($record->vendor_type) : '')
                    ->wrap()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('contact_person')
                    ->searchable()
                    ->description(fn (Vendor $record): string => $record->contact_position ?? '')
                    ->wrap(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('primary'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->icon('heroicon-o-phone')
                    ->iconColor('gray'),
                Tables\Columns\TextColumn::make('payment_terms')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'prepaid' => 'success',
                        'on_delivery' => 'success',
                        'net7' => 'info',
                        'net15' => 'info',
                        'net30' => 'warning',
                        'net60' => 'danger',
                        'end_of_month' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper(str_replace(['net', '_'], '', $state)))
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('vendor_type')
                    ->options([
                        'nursery' => 'Nursery',
                        'supplier' => 'Supplier',
                        'contractor' => 'Contractor',
                        'service' => 'Service Provider',
                        'other' => 'Other',
                    ])
                    ->label('Vendor Type'),
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->label('Status'),
                Tables\Filters\SelectFilter::make('payment_terms')
                    ->options([
                        'prepaid' => 'Prepaid',
                        'on_delivery' => 'On Delivery',
                        'net7' => 'Net 7 Days',
                        'net15' => 'Net 15 Days',
                        'net30' => 'Net 30 Days',
                        'net60' => 'Net 60 Days',
                        'end_of_month' => 'End of Month',
                    ])
                    ->label('Payment Terms'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->placeholder('From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->placeholder('Until Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Created from ' . \Carbon\Carbon::parse($data['created_from'])->toFormattedDateString();
                        }
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Created until ' . \Carbon\Carbon::parse($data['created_until'])->toFormattedDateString();
                        }
                        return $indicators;
                    }),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->iconButton(),
                Tables\Actions\Action::make('email')
                    ->icon('heroicon-o-envelope')
                    ->url(fn (Vendor $record): string => 'mailto:' . $record->email)
                    ->openUrlInNewTab()
                    ->iconButton(),
                Tables\Actions\Action::make('call')
                    ->icon('heroicon-o-phone')
                    ->url(fn (Vendor $record): string => 'tel:' . $record->phone)
                    ->iconButton(),
                Tables\Actions\EditAction::make()
                    ->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->icon('heroicon-o-check-circle')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false])),
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn (Vendor $record): bool => $record->is_active === false),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('No vendors found')
            ->emptyStateDescription('Create your first vendor to get started.');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PurchaseOrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendors::route('/'),
            'create' => Pages\CreateVendor::route('/create'),
            'view' => Pages\ViewVendor::route('/{record}'),
            'edit' => Pages\EditVendor::route('/{record}/edit'),
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
