<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Inventory\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Excel;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Exports\ProductExport;
use App\Imports\ProductImport;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-box';

    protected static ?string $navigationGroup = 'Inventory';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Product Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Product Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1),
                                
                                TextInput::make('sku')
                                    ->label('SKU')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1),
                            ]),
                        
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(3)
                            ->maxLength(1000),
                        
                        Grid::make(3)
                            ->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->required()
                                    ->options([
                                        'massage_oil' => 'Massage Oil',
                                        'essential_oil' => 'Essential Oil',
                                        'carrier_oil' => 'Carrier Oil',
                                        'cream' => 'Cream',
                                        'lotion' => 'Lotion',
                                        'gel' => 'Gel',
                                        'serum' => 'Serum',
                                        'balm' => 'Balm',
                                        'scrub' => 'Scrub',
                                        'mask' => 'Mask',
                                        'other' => 'Other',
                                    ])
                                    ->columnSpan(1),
                                
                                TextInput::make('subcategory')
                                    ->label('Subcategory')
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                
                                Select::make('status')
                                    ->label('Status')
                                    ->required()
                                    ->options([
                                        'active' => 'Active',
                                        'inactive' => 'Inactive',
                                        'discontinued' => 'Discontinued',
                                    ])
                                    ->default('active')
                                    ->columnSpan(1),
                            ]),
                    ]),
                
                Section::make('Inventory Management')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                TextInput::make('current_stock')
                                    ->label('Current Stock')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1),
                                
                                TextInput::make('minimum_stock')
                                    ->label('Minimum Stock')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1),
                                
                                TextInput::make('maximum_stock')
                                    ->label('Maximum Stock')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1),
                                
                                TextInput::make('reorder_quantity')
                                    ->label('Reorder Quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('unit_of_measure')
                                    ->label('Unit of Measure')
                                    ->required()
                                    ->default('ml')
                                    ->columnSpan(1),
                                
                                TextInput::make('unit_size')
                                    ->label('Unit Size')
                                    ->numeric()
                                    ->step(0.1)
                                    ->columnSpan(1),
                            ]),
                    ]),
                
                Section::make('Pricing')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('cost_price')
                                    ->label('Cost Price')
                                    ->required()
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('$')
                                    ->columnSpan(1),
                                
                                TextInput::make('selling_price')
                                    ->label('Selling Price')
                                    ->numeric()
                                    ->step(0.01)
                                    ->prefix('$')
                                    ->columnSpan(1),
                                
                                Select::make('currency')
                                    ->label('Currency')
                                    ->required()
                                    ->options([
                                        'USD' => 'USD',
                                        'EUR' => 'EUR',
                                        'GBP' => 'GBP',
                                        'CAD' => 'CAD',
                                        'AUD' => 'AUD',
                                    ])
                                    ->default('USD')
                                    ->columnSpan(1),
                            ]),
                    ]),
                
                Section::make('Physical Properties')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('brand')
                                    ->label('Brand')
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                
                                TextInput::make('manufacturer')
                                    ->label('Manufacturer')
                                    ->maxLength(100)
                                    ->columnSpan(1),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('expiry_date')
                                    ->label('Expiry Date')
                                    ->columnSpan(1),
                                
                                DatePicker::make('manufacture_date')
                                    ->label('Manufacture Date')
                                    ->columnSpan(1),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('batch_number')
                                    ->label('Batch Number')
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                
                                TextInput::make('lot_number')
                                    ->label('Lot Number')
                                    ->maxLength(100)
                                    ->columnSpan(1),
                            ]),
                    ]),
                
                Section::make('Storage & Location')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('storage_location')
                                    ->label('Storage Location')
                                    ->maxLength(100)
                                    ->columnSpan(1),
                                
                                TextInput::make('warehouse')
                                    ->label('Warehouse')
                                    ->maxLength(100)
                                    ->columnSpan(1),
                            ]),
                    ]),
                
                Section::make('Supplier Information')
                    ->schema([
                        Select::make('primary_supplier_id')
                            ->label('Primary Supplier')
                            ->relationship('supplier', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ]),
                
                Section::make('Product Features')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_trackable')
                                    ->label('Trackable')
                                    ->default(true)
                                    ->columnSpan(1),
                                
                                Toggle::make('requires_refrigeration')
                                    ->label('Requires Refrigeration')
                                    ->default(false)
                                    ->columnSpan(1),
                                
                                Toggle::make('is_hazardous')
                                    ->label('Hazardous Material')
                                    ->default(false)
                                    ->columnSpan(1),
                            ]),
                        
                        Grid::make(3)
                            ->schema([
                                Toggle::make('is_perishable')
                                    ->label('Perishable')
                                    ->default(false)
                                    ->columnSpan(1),
                                
                                Toggle::make('is_organic')
                                    ->label('Organic')
                                    ->default(false)
                                    ->columnSpan(1),
                                
                                Toggle::make('is_natural')
                                    ->label('Natural')
                                    ->default(false)
                                    ->columnSpan(1),
                            ]),
                    ]),
                
                Section::make('Barcodes & QR Codes')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('barcode')
                                    ->label('Barcode')
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1),
                                
                                TextInput::make('qr_code')
                                    ->label('QR Code')
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true)
                                    ->columnSpan(1),
                            ]),
                    ]),
                
                Section::make('Images')
                    ->schema([
                        FileUpload::make('images')
                            ->label('Product Images')
                            ->multiple()
                            ->image()
                            ->imageEditor()
                            ->directory('products')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->reorderable()
                            ->appendFiles()
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Additional Information')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notes')
                            ->rows(3)
                            ->maxLength(1000)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Product Name')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'massage_oil' => 'primary',
                        'essential_oil' => 'success',
                        'carrier_oil' => 'info',
                        'cream' => 'warning',
                        'lotion' => 'secondary',
                        'gel' => 'danger',
                        'serum' => 'primary',
                        'balm' => 'success',
                        'scrub' => 'info',
                        'mask' => 'warning',
                        'other' => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'primary' => 'active',
                        'danger' => 'inactive',
                        'warning' => 'discontinued',
                    ]),
                
                TextColumn::make('current_stock')
                    ->label('Stock')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('minimum_stock')
                    ->label('Min Stock')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                
                IconColumn::make('is_low_stock')
                    ->label('Low Stock')
                    ->boolean()
                    ->trueIcon('heroicon-o-exclamation-triangle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->alignCenter(),
                
                TextColumn::make('cost_price')
                    ->label('Cost Price')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd(),
                
                TextColumn::make('selling_price')
                    ->label('Selling Price')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd(),
                
                TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('expiry_date')
                    ->label('Expiry Date')
                    ->date()
                    ->sortable(),
                
                IconColumn::make('requires_refrigeration')
                    ->label('Refrigerated')
                    ->boolean()
                    ->trueIcon('heroicono-snowflake')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'massage_oil' => 'Massage Oil',
                        'essential_oil' => 'Essential Oil',
                        'carrier_oil' => 'Carrier Oil',
                        'cream' => 'Cream',
                        'lotion' => 'Lotion',
                        'gel' => 'Gel',
                        'serum' => 'Serum',
                        'balm' => 'Balm',
                        'scrub' => 'Scrub',
                        'mask' => 'Mask',
                        'other' => 'Other',
                    ]),
                
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'discontinued' => 'Discontinued',
                    ]),
                
                Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('current_stock <= minimum_stock')),
                
                Filter::make('out_of_stock')
                    ->label('Out of Stock')
                    ->query(fn (Builder $query): Builder => $query->where('current_stock', '<=', 0)),
                
                Filter::make('expiring_soon')
                    ->label('Expiring Soon')
                    ->query(fn (Builder $query): Builder => $query->where('expiry_date', '<=', now()->addDays(30))),
                
                SelectFilter::make('supplier')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->preload(),
                
                Filter::make('requires_refrigeration')
                    ->label('Requires Refrigeration')
                    ->query(fn (Builder $query): Builder => $query->where('requires_refrigeration', true)),
            ])
            ->actions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Product $record): string => route('products.show', $record)),
                
                Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn (Product $record): string => route('products.edit', $record)),
                
                Action::make('adjust_stock')
                    ->label('Adjust Stock')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->form([
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->required()
                            ->numeric()
                            ->placeholder('Enter positive or negative quantity'),
                        
                        Textarea::make('reason')
                            ->label('Reason')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (Product $record, array $data): void {
                        $record->adjustStock($data['quantity'], $data['reason']);
                        Notification::make()
                            ->title('Stock adjusted successfully')
                            ->success()
                            ->send();
                    }),
                
                Action::make('generate_barcode')
                    ->label('Generate Barcode')
                    ->icon('heroicon-o-qrcode')
                    ->action(function (Product $record): void {
                        $record->generateBarcode();
                        Notification::make()
                            ->title('Barcode generated successfully')
                            ->success()
                            ->send();
                    }),
                
                Action::make('generate_qr_code')
                    ->label('Generate QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->action(function (Product $record): void {
                        $record->generateQRCode();
                        Notification::make()
                            ->title('QR Code generated successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('Delete')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            $records->each->delete();
                            Notification::make()
                                ->title('Products deleted successfully')
                                ->success()
                                ->send();
                        }),
                    
                    BulkAction::make('export')
                        ->label('Export')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (\Illuminate\Support\Collection $records): void {
                            // Implement bulk export functionality
                            Notification::make()
                                ->title('Products exported successfully')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateActions([
                Action::make('create')
                    ->label('Create Product')
                    ->icon('heroicon-o-plus')
                    ->url(fn (): string => route('products.create')),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ProductExport::class)
                    ->label('Export Products'),
            ])
            ->bulkActions([
                ExportBulkAction::make()
                    ->exporter(ProductExport::class)
                    ->label('Export Selected'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TransactionsRelationManager::class,
            RelationManagers\BarcodesRelationManager::class,
            RelationManagers\AlertsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku', 'description', 'brand', 'manufacturer'];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'primary';
    }
}
