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
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->disk('public')
                    ->size(40)
                    ->toggleable(),

                TextColumn::make('document_type')
                    ->label('Tipo')
                    ->required()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('document_number')
                    ->label('Documento')
                    ->required()
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->email)
                    ->weight('medium')
                    ->toggleable(),
                
                TextColumn::make('position')
                    ->label('Cargo')
                    ->searchable()
                    ->placeholder('Sin cargo')
                    ->toggleable(),
                
                TextColumn::make('role')
                    ->label('Rol')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'warning',
                        'tech' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'admin' => '👑 Administrador',
                        'tech' => '👨‍💼 Trabajador',
                    })
                    ->toggleable(),
                
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activo',
                        'inactive' => 'Inactivo',
                    })
                    ->toggleable(),
                
                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable()
                    ->placeholder('Sin teléfono')
                    ->toggleable(),
                
                TextColumn::make('hire_date')
                    ->label('Fecha de Contratación')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('Sin fecha')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                ViewAction::make(),
                EditAction::make(),
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
