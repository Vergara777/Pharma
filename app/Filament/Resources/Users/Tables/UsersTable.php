<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Tables;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                    ImageColumn::make('avatar')
                        ->label('')
                        ->circular()
                        ->size(40)
                        ->defaultImageUrl(fn ($record) => $record->profile_photo_url)
                        ->grow(false),

                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make('name')
                            ->label('Nombre')
                            ->weight('bold')
                            ->searchable()
                            ->sortable(),
                        TextColumn::make('document_number')
                            ->label('Documento')
                            ->icon('heroicon-m-identification')
                            ->color('gray')
                            ->searchable()
                            ->placeholder('Sin documento'),
                    ])->space(1),

                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make('phone')
                            ->label('Teléfono')
                            ->icon('heroicon-m-phone')
                            ->searchable()
                            ->placeholder('Sin teléfono'),
                        TextColumn::make('email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->color('gray')
                            ->searchable()
                            ->placeholder('Sin correo'),
                    ])->space(1),

                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make('role')
                            ->label('Rol')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'admin' => 'warning',
                                'tech' => 'info',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'admin' => 'Administrador',
                                'tech' => 'Trabajador',
                                default => $state,
                            }),
                        TextColumn::make('status')
                            ->label('Estado')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                                default => $state,
                            }),
                    ])->space(1),

                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make('position')
                            ->label('Cargo')
                            ->icon('heroicon-m-briefcase')
                            ->placeholder('Sin cargo'),
                        TextColumn::make('hire_date')
                            ->label('Fecha')
                            ->icon('heroicon-m-calendar')
                            ->date('d/m/Y')
                            ->placeholder('Sin fecha'),
                    ])->space(1),
                ])->from('md'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol')
                    ->options([
                        'admin' => 'Administrador',
                        'tech' => 'Trabajador',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->color('gray'),
                EditAction::make()
                    ->color('warning'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }
}
