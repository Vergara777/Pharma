<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Support\Colors\Color;
use Filament\Actions\ViewAction;
use Filament\Schemas\Schema;

class TopProfitableProductsWidget extends BaseWidget
{
    protected static ?int $sort = 6;
    
    // Lo ponemos a la mitad para que quepa al lado del gráfico
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('📈 Top Productos con Mayor Ganancia')
            ->description('Análisis de rendimiento y margen de utilidad real')
            ->query(
                Product::query()
                    ->join('venta_items', 'products.id', '=', 'venta_items.product_id')
                    ->join('ventas', 'ventas.id', '=', 'venta_items.venta_id')
                    ->where('ventas.status', 'active')
                    ->select('products.*')
                    ->selectRaw('SUM(venta_items.qty) as total_qty')
                    ->selectRaw('SUM(venta_items.subtotal) as total_revenue')
                    ->selectRaw('SUM(venta_items.qty * products.cost) as total_cost')
                    ->selectRaw('SUM(venta_items.subtotal - (venta_items.qty * products.cost)) as total_profit')
                    ->groupBy('products.id')
                    ->orderByDesc('total_profit')
                    ->limit(5)
            )
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->circular()
                    ->size(32),

                TextColumn::make('name')
                    ->label('Producto')
                    ->weight('bold')
                    ->wrap()
                    ->description(fn ($record) => $record->category->name ?? '', position: 'below'),

                TextColumn::make('total_qty')
                    ->label('Vol.')
                    ->tooltip('Volumen Vendido')
                    ->numeric()
                    ->alignment('center'),

                TextColumn::make('total_revenue')
                    ->label('Ing.')
                    ->tooltip('Ingresos Totales')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->color('gray')
                    ->alignment('right'),

                TextColumn::make('total_profit')
                    ->label('Profit')
                    ->tooltip('Ganancia Neta')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->weight('black')
                    ->color('success')
                    ->alignment('right'),

                TextColumn::make('margin')
                    ->label('ROI')
                    ->state(function ($record) {
                        $cost = (float) ($record->total_cost ?? 0);
                        $profit = (float) ($record->total_profit ?? 0);
                        $margin = $cost > 0 ? ($profit / $cost) * 100 : 0;
                        return number_format($margin, 1) . '%';
                    })
                    ->badge()
                    ->color(function ($state) {
                        $val = (float) str_replace('%', '', $state);
                        return $val > 30 ? 'success' : ($val > 15 ? 'warning' : 'danger');
                    })
                    ->alignment('center'),
            ])
            ->actions([
                ViewAction::make()
                    ->label('')
                    ->tooltip('Ver Detalles')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->infolist(function (Schema $schema) {
                        $infoComponents = \App\Filament\Resources\Products\Schemas\ProductInfolist::configure(new \Filament\Schemas\Schema())->getComponents();
                        
                        return $schema->components([
                            \Filament\Schemas\Components\Section::make('🚀 Análisis de Rendimiento (Trading)')
                                ->schema([
                                    \Filament\Infolists\Components\TextEntry::make('total_qty')
                                        ->label('Volumen Vendido')
                                        ->badge()
                                        ->color('gray')
                                        ->suffix(' unidades'),
                                    
                                    \Filament\Infolists\Components\TextEntry::make('total_revenue')
                                        ->label('Ingresos Brutos')
                                        ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                                        ->weight('bold')
                                        ->color('info'),

                                    \Filament\Infolists\Components\TextEntry::make('total_profit')
                                        ->label('Ganancia Neta (Profit)')
                                        ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                                        ->weight('black')
                                        ->color('success')
                                        ->icon('heroicon-m-arrow-trending-up'),

                                    \Filament\Infolists\Components\TextEntry::make('margin')
                                        ->label('Retorno (ROI)')
                                        ->state(function ($record) {
                                            $cost = (float) ($record->total_cost ?? 0);
                                            $profit = (float) ($record->total_profit ?? 0);
                                            $margin = $cost > 0 ? ($profit / $cost) * 100 : 0;
                                            return number_format($margin, 1) . '%';
                                        })
                                        ->badge()
                                        ->color(function ($state) {
                                            $val = (float) str_replace('%', '', $state);
                                            return $val > 30 ? 'success' : ($val > 15 ? 'warning' : 'danger');
                                        }),
                                ])->columns(4),

                            \Filament\Schemas\Components\Section::make('📦 Información del Producto')
                                ->schema($infoComponents)
                                ->collapsible(),
                        ]);
                    })
                    ->modalHeading('Análisis Estratégico')
                    ->modalWidth('5xl'),
            ])
            ->paginated(false);
    }
}
