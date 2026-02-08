<?php

namespace App\Filament\Resources\Clientes\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClienteInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Cliente')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre'),
                        TextEntry::make('document')
                            ->label('Documento')
                            ->placeholder('-'),
                        TextEntry::make('email')
                            ->label('Email')
                            ->placeholder('-'),
                        TextEntry::make('phone')
                            ->label('Teléfono')
                            ->placeholder('-'),
                        TextEntry::make('address')
                            ->label('Dirección')
                            ->placeholder('-'),
                        IconEntry::make('is_active')
                            ->label('Activo')
                            ->boolean(),
                    ])
                    ->columns(2),
                
                Section::make('Estadísticas')
                    ->schema([
                        TextEntry::make('facturas_count')
                            ->label('Total de Facturas')
                            ->state(fn ($record) => $record->facturas()->count())
                            ->badge()
                            ->color('success'),
                        TextEntry::make('ventas_count')
                            ->label('Total de Ventas')
                            ->state(fn ($record) => $record->ventas()->count())
                            ->badge()
                            ->color('info'),
                        TextEntry::make('total_facturado')
                            ->label('Total Facturado')
                            ->state(fn ($record) => '$' . number_format($record->facturas()->sum('total'), 2))
                            ->badge()
                            ->color('warning'),
                        TextEntry::make('total_ventas')
                            ->label('Total en Ventas')
                            ->state(fn ($record) => '$' . number_format($record->ventas()->sum('grand_total'), 2))
                            ->badge()
                            ->color('primary'),
                    ])
                    ->columns(4),
                
                Section::make('Información del Sistema')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Actualizado')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
