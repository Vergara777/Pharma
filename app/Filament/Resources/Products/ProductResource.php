<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Pages\ViewProduct;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Schemas\ProductInfolist;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use BackedEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $modelLabel = 'Producto';

    protected static ?string $pluralModelLabel = 'Productos';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?bool $globallySearchable = true;

    protected static int $globalSearchResultsLimit = 20;

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->name . ' - ' . $record->sku;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku', 'description'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'SKU' => $record->sku,
            'Categoría' => $record->category?->name,
            'Precio' => 'COP ' . number_format($record->price, 0, ',', '.'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
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
            'index' => ListProducts::route('/'),
        ];
    }

    public static function maxContentWidth(): string
    {
        return 'full';
    }

    public static function canCreate(): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->role === 'admin';
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()->role === 'admin';
    }
}
