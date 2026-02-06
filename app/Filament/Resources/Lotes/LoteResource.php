<?php

namespace App\Filament\Resources\Lotes;

use App\Filament\Resources\Lotes\Pages\ListLotes;
use App\Filament\Resources\Lotes\Pages\CreateLote;
use App\Filament\Resources\Lotes\Pages\EditLote;
use App\Filament\Resources\Lotes\Pages\ViewLote;
use App\Filament\Resources\Lotes\Pages;
use App\Filament\Resources\Lotes\Schemas\LoteForm;
use App\Filament\Resources\Lotes\Tables\LotesTable;
use App\Models\Lote;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Schemas\Schema;

class LoteResource extends Resource
{
    protected static ?string $model = Lote::class;

    protected static ?string $modelLabel = 'Lote';

    protected static ?string $pluralModelLabel = 'Lotes';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-square-3-stack-3d';
    }
    
    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function schema(Schema $schema): Schema
    {
        return $schema->schema(LoteForm::getSchema());
    }

    public static function table(Table $table): Table
    {
        return LotesTable::make($table);
    }

    public static function infolist(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\Section::make('Información del Lote')
                    ->icon('heroicon-o-cube')
                    ->columns(3) // Divide in 3 columns for better spacing
                    ->schema([
                        // Column 1: Identification & Status
                        \Filament\Schemas\Components\Group::make([
                            \Filament\Infolists\Components\TextEntry::make('codigo_lote')
                                ->label('Código')
                                ->weight('bold')
                                ->size('lg')
                                ->copyable(),
                                
                            \Filament\Infolists\Components\TextEntry::make('estado')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'activo' => 'success',
                                    'agotado' => 'danger',
                                    'bloqueado' => 'warning',
                                    'vencido' => 'danger',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                            \Filament\Infolists\Components\TextEntry::make('product.name')
                                ->label('Producto')
                                ->icon('heroicon-o-beaker')
                                ->weight('bold'),
                        ]),

                        // Column 2: Financial & Supplier
                        \Filament\Schemas\Components\Group::make([
                            \Filament\Infolists\Components\TextEntry::make('costo_unitario')
                                ->label('Costo Unitario')
                                ->money('COP')
                                ->size('lg')
                                ->weight('bold'),
                                
                            \Filament\Infolists\Components\TextEntry::make('proveedor.name')
                                ->label('Proveedor')
                                ->icon('heroicon-o-truck'),
                                
                            \Filament\Infolists\Components\TextEntry::make('usuarioRegistro.name')
                                ->label('Registrado por')
                                ->icon('heroicon-o-user'),
                        ]),

                        // Column 3: Stock & Dates
                        \Filament\Schemas\Components\Group::make([
                            \Filament\Schemas\Components\Grid::make(2)
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('cantidad_inicial')
                                        ->label('Inicial')
                                        ->numeric(),
                                    \Filament\Infolists\Components\TextEntry::make('cantidad_actual')
                                        ->label('Actual')
                                        ->numeric()
                                        ->weight('bold')
                                        ->color(fn ($state) => $state <= 0 ? 'danger' : 'success'),
                                ]),
                            
                            \Filament\Infolists\Components\TextEntry::make('fecha_vencimiento')
                                ->label('Vencimiento')
                                ->date('d/m/Y')
                                ->icon('heroicon-o-calendar-days')
                                ->color(fn ($record) => $record->fecha_vencimiento < now() ? 'danger' : 'primary'),
                                
                            \Filament\Infolists\Components\TextEntry::make('fecha_ingreso')
                                ->label('Ingreso')
                                ->date('d/m/Y')
                                ->icon('heroicon-o-calendar'),
                        ]),
                            
                        \Filament\Infolists\Components\TextEntry::make('notas')
                            ->columnSpanFull()
                            ->markdown()
                            ->visible(fn ($record) => !empty($record->notas)),
                    ]),

                \Filament\Schemas\Components\Section::make('Historial de Movimientos')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->collapsible()
                    ->schema([
                        \Filament\Infolists\Components\RepeatableEntry::make('movimientos')
                            ->schema([
                                \Filament\Schemas\Components\Grid::make(4)
                                    ->schema([
                                        \Filament\Infolists\Components\TextEntry::make('tipo_movimiento')
                                            ->label('Tipo')
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'entrada' => 'success',
                                                'salida' => 'warning',
                                                'ajuste' => 'info',
                                                'merma' => 'danger',
                                                default => 'gray',
                                            })
                                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                                        \Filament\Infolists\Components\TextEntry::make('created_at')
                                            ->label('Fecha')
                                            ->dateTime('d/m/Y h:i A'),

                                        \Filament\Schemas\Components\Group::make([
                                            \Filament\Infolists\Components\TextEntry::make('cantidad')
                                                ->label('Movido')
                                                ->weight('bold'),
                                            \Filament\Infolists\Components\TextEntry::make('motivo')
                                                ->label('Motivo')
                                                ->limit(30)
                                                ->tooltip(fn ($state) => $state),
                                        ]),

                                        \Filament\Infolists\Components\TextEntry::make('usuario.name')
                                            ->label('Usuario')
                                            ->icon('heroicon-o-user-circle'),
                                    ]),
                            ])
                            ->grid(1) // List view for movements
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1); // Main layout: Single column to stack sections vertically
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLotes::route('/'),
        ];
    }
    
    public static function canViewAny(): bool
    {
        // Solo admin puede ver lotes
        return auth()->user()->role === 'admin';
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

    public static function getNavigationBadge(): ?string
    {
        return Lote::proximosAVencer(30)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = Lote::proximosAVencer(30)->count();
        return $count > 0 ? 'warning' : null;
    }
}
