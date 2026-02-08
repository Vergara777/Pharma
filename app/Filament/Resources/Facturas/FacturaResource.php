<?php

namespace App\Filament\Resources\Facturas;

use App\Filament\Resources\Facturas\Pages\CreateFactura;
use App\Filament\Resources\Facturas\Pages\EditFactura;
use App\Filament\Resources\Facturas\Pages\ListFacturas;
use App\Filament\Resources\Facturas\Pages\ViewFactura;
use App\Filament\Resources\Facturas\Schemas\FacturaForm;
use App\Filament\Resources\Facturas\Schemas\FacturaInfolist;
use App\Filament\Resources\Facturas\Tables\FacturasTable;
use App\Models\Factura;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class FacturaResource extends Resource
{
    protected static ?string $model = Factura::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'invoice_number';

    protected static ?string $navigationLabel = 'Facturas';

    protected static ?string $modelLabel = 'Factura';

    protected static ?string $pluralModelLabel = 'Facturas';

    protected static UnitEnum|string|null $navigationGroup = 'Ventas';

    protected static ?int $navigationSort = 2;

    protected static ?bool $globallySearchable = true;

    protected static int $globalSearchResultsLimit = 10;

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->invoice_number;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['invoice_number', 'cliente.name'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Cliente' => $record->cliente?->name ?? '-',
            'Total' => '$' . number_format($record->total, 0, ',', '.'),
            'Estado' => match($record->status) {
                'pending' => 'Pendiente',
                'paid' => 'Pagada',
                'cancelled' => 'Cancelada',
            },
        ];
    }

    public static function getModalWidth(): string
    {
        return '7xl'; // Opciones: sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl, screen
    }

    public static function form(Schema $schema): Schema
    {
        return FacturaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return FacturaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FacturasTable::configure($table);
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
            'index' => ListFacturas::route('/'),
        ];
    }

    // Permisos: Trabajadores solo pueden VER, Admins pueden TODO
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

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }
}
