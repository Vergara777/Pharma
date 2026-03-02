<?php

namespace App\Filament\Resources\Categories\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\CreateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->toggleable(), 
                TextColumn::make('description')
                    ->searchable()
                    ->toggleable()
                    ->placeholder('Sin descripción'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => $state === 'active' ? 'success' : 'danger')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->visible(fn () => auth()->user()->role === 'admin'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])
                    ->visible(fn () => auth()->user()->role === 'admin'),
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
