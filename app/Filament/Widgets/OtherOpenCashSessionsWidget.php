<?php

namespace App\Filament\Widgets;

use App\Models\CashSession;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;

class OtherOpenCashSessionsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CashSession::query()
                    ->where('status', 'open')
                    ->where('user_id', '!=', auth()->id())
                    ->with(['user', 'ventas' => function ($query) {
                        $query->where('status', 'active');
                    }])
                    ->orderBy('opened_at', 'desc')
            )
            ->heading('👥 Otras Cajas Abiertas')
            ->columns([
                TextColumn::make('id')
                    ->label('Caja #')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('opened_at')
                    ->label('Abierta Hace')
                    ->since()
                    ->sortable(),
                TextColumn::make('initial_amount')
                    ->label('Monto Inicial')
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.')),
                TextColumn::make('ventas_sum')
                    ->label('Ventas')
                    ->getStateUsing(fn ($record) => $record->ventas->sum('grand_total'))
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.')),
                TextColumn::make('total_teorico')
                    ->label('Total Teórico')
                    ->getStateUsing(fn ($record) => $record->calculateTheoreticalAmount())
                    ->formatStateUsing(fn ($state) => '$' . number_format($state, 0, ',', '.'))
                    ->weight('bold')
                    ->color('success'),
            ])
            ->actions([
                Action::make('close')
                    ->label('Cerrar Caja')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn ($record) => 'Cerrar Caja #' . $record->id . ' de ' . $record->user->name)
                    ->modalDescription('Como administrador, puedes cerrar la caja de otro usuario. Ingresa el monto contado.')
                    ->modalWidth('2xl')
                    ->form(function ($record) {
                        return [
                            Grid::make(3)
                                ->schema([
                                    Placeholder::make('initial_amount')
                                        ->label('Monto Inicial')
                                        ->content('$' . number_format($record->initial_amount, 0, ',', '.')),
                                    Placeholder::make('sales')
                                        ->label('Ventas del Día')
                                        ->content(function () use ($record) {
                                            $theoretical = $record->calculateTheoreticalAmount();
                                            return '$' . number_format($theoretical - $record->initial_amount, 0, ',', '.');
                                        }),
                                    Placeholder::make('theoretical')
                                        ->label('Monto Teórico')
                                        ->content('$' . number_format($record->calculateTheoreticalAmount(), 0, ',', '.')),
                                ]),
                            TextInput::make('counted_amount')
                                ->label('Monto Contado')
                                ->required()
                                ->prefix('$')
                                ->placeholder('150.000')
                                ->helperText('Dinero físico contado en la caja')
                                ->extraInputAttributes(['data-money-format' => true])
                                ->live(onBlur: true)
                                ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace(['.', ','], '', $state) : 0),
                            Placeholder::make('difference_display')
                                ->label('Diferencia')
                                ->content(function ($get) use ($record) {
                                    $countedAmount = $get('counted_amount');
                                    if (!$countedAmount) {
                                        return 'Ingresa el monto contado';
                                    }
                                    
                                    $cleanValue = (int) str_replace(['.', ','], '', $countedAmount);
                                    $theoretical = $record->calculateTheoreticalAmount();
                                    $difference = $cleanValue - $theoretical;
                                    
                                    if ($difference > 0) {
                                        return '✅ Sobran $' . number_format($difference, 0, ',', '.');
                                    } elseif ($difference < 0) {
                                        return '❌ Faltan $' . number_format(abs($difference), 0, ',', '.');
                                    } else {
                                        return '✓ Cuadra exacto';
                                    }
                                })
                                ->extraAttributes(function ($get) use ($record) {
                                    $countedAmount = $get('counted_amount');
                                    if (!$countedAmount) {
                                        return ['style' => 'color: #6b7280;'];
                                    }
                                    
                                    $cleanValue = (int) str_replace(['.', ','], '', $countedAmount);
                                    $theoretical = $record->calculateTheoreticalAmount();
                                    $difference = $cleanValue - $theoretical;
                                    
                                    $color = $difference >= 0 ? '#10b981' : '#ef4444';
                                    return ['style' => "color: {$color}; font-weight: bold; font-size: 1.125rem;"];
                                }),
                            Textarea::make('notes')
                                ->label('Notas')
                                ->rows(3)
                                ->placeholder('Motivo del cierre por admin...')
                                ->columnSpanFull(),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        $theoretical = $record->calculateTheoreticalAmount();
                        $countedAmount = (int) str_replace(['.', ','], '', $data['counted_amount']);
                        
                        $record->update([
                            'closed_at' => now(),
                            'counted_amount' => $countedAmount,
                            'theoretical_amount' => $theoretical,
                            'difference' => $countedAmount - $theoretical,
                            'notes' => ($data['notes'] ?? '') . "\n\n[Cerrada por admin: " . auth()->user()->name . "]",
                            'status' => 'closed',
                            'closed_by_admin' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Caja Cerrada')
                            ->body("La caja #{$record->id} de {$record->user->name} ha sido cerrada por el administrador")
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No hay otras cajas abiertas')
            ->emptyStateDescription('Todos los demás usuarios tienen sus cajas cerradas')
            ->emptyStateIcon('heroicon-o-lock-closed');
    }
}
