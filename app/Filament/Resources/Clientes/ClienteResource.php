<?php

namespace App\Filament\Resources\Clientes;

use App\Filament\Resources\Clientes\Pages\CreateCliente;
use App\Filament\Resources\Clientes\Pages\EditCliente;
use App\Filament\Resources\Clientes\Pages\ListClientes;
use App\Filament\Resources\Clientes\Pages\ViewCliente;
use App\Filament\Resources\Clientes\Schemas\ClienteForm;
use App\Filament\Resources\Clientes\Schemas\ClienteInfolist;
use App\Filament\Resources\Clientes\Tables\ClientesTable;
use App\Models\Cliente;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ClienteResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Clientes';

    protected static ?string $modelLabel = 'Cliente';

    protected static ?string $pluralModelLabel = 'Clientes';

    // protected static UnitEnum|string|null $navigationGroup = 'Ventas';

    protected static ?int $navigationSort = 1;

    protected static ?bool $globallySearchable = true;

    protected static int $globalSearchResultsLimit = 10;

    public static function getGlobalSearchResultTitle($record): string
    {
        return $record->name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'document', 'email', 'phone'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Documento' => $record->document ?? '-',
            'Teléfono' => $record->phone ?? '-',
            'Email' => $record->email ?? '-',
        ];
    }

    public static function getModalWidth(): string
    {
        return '3xl'; // Modal más ancho para editar clientes
    }

    public static function form(Schema $schema): Schema
    {
        return ClienteForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClienteInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Clientes\RelationManagers\FacturasRelationManager::class,
            \App\Filament\Resources\Clientes\RelationManagers\VentasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientes::route('/'),
            'view' => ViewCliente::route('/{record}'),
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
