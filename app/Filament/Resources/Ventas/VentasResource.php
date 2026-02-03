<?php

namespace App\Filament\Resources\Ventas;

use App\Filament\Resources\Ventas\Pages\CreateVenta;
use App\Filament\Resources\Ventas\Pages\ListVentas;
use App\Filament\Resources\Ventas\Pages\ViewVenta;
use App\Filament\Resources\Ventas\Schemas\VentaForm;
use App\Filament\Resources\Ventas\Schemas\VentaInfolist;
use App\Filament\Resources\Ventas\Tables\VentasTable;
use App\Models\Ventas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class VentasResource extends Resource
{
    protected static ?string $model = Ventas::class;

    protected static ?string $modelLabel = 'Venta';

    protected static ?string $pluralModelLabel = 'Ventas';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'invoice_number';

    public static function form(Schema $schema): Schema
    {
        return VentaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return VentaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VentasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVentas::route('/'),
            // 'create' => CreateVenta::route('/create'), // Deshabilitado - usar carrito
        ];
    }

    public static function canCreate(): bool
    {
        // Deshabilitado - usar el carrito para crear ventas
        return false;
    }

    public static function canEdit($record): bool
    {
        // No se pueden editar ventas, solo ver y cancelar
        return false;
    }

    public static function canDelete($record): bool
    {
        // No se pueden eliminar ventas
        return false;
    }
}
