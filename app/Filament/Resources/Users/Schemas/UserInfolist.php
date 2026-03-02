<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->schema([
                        ImageEntry::make('avatar')
                            ->label('Foto de Perfil')
                            ->circular()
                            ->disk('public')
                            ->size(120)
                            ->columnSpan(2),
                        
                        TextEntry::make('name')
                            ->label('Nombre Completo')
                            ->size('lg')
                            ->weight('bold')
                            ->columnSpan(2),
                        
                        TextEntry::make('email')
                            ->label('Correo Electrónico')
                            ->icon('heroicon-o-envelope')
                            ->copyable(),
                        
                        TextEntry::make('phone')
                            ->label('Teléfono')
                            ->icon('heroicon-o-phone')
                            ->placeholder('Sin teléfono')
                            ->copyable(),
                        
                        TextEntry::make('id_number')
                            ->label('Número de Identificación')
                            ->icon('heroicon-o-identification')
                            ->placeholder('Sin identificación')
                            ->copyable(),
                        
                        TextEntry::make('birth_date')
                            ->label('Fecha de Nacimiento')
                            ->date('d/m/Y')
                            ->placeholder('Sin fecha'),
                        
                        TextEntry::make('address')
                            ->label('Dirección')
                            ->icon('heroicon-o-map-pin')
                            ->placeholder('Sin dirección')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Información Laboral')
                    ->schema([
                        TextEntry::make('position')
                            ->label('Cargo/Posición')
                            ->placeholder('Sin cargo')
                            ->badge()
                            ->color('info'),
                        
                        TextEntry::make('hire_date')
                            ->label('Fecha de Contratación')
                            ->date('d/m/Y')
                            ->placeholder('Sin fecha'),
                        
                        TextEntry::make('role')
                            ->label('Rol del Sistema')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'admin' => 'warning',
                                'tech' => 'info',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'admin' => 'Administrador',
                                'tech' => 'Trabajador',
                            }),
                        
                        TextEntry::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                            }),
                    ])
                    ->columns(2),

                Section::make('Información Adicional')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Fecha de Registro')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                        
                        TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
