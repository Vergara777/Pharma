<?php

namespace App\Filament\Resources\CashSessionResource\Pages;

use App\Filament\Resources\CashSessionResource;
use App\Models\CashSession;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Placeholder;
use Filament\Schemas\Components\Grid;
use Filament\Notifications\Notification;

class ListCashSessions extends ListRecords
{
    protected static string $resource = CashSessionResource::class;
    
    public ?CashSession $openSession = null;

    public function mount(): void
    {
        $this->openSession = CashSession::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();
    }
    
    protected function getHeaderWidgets(): array
    {
        $widgets = [
            \App\Filament\Widgets\CurrentCashSessionWidget::class,
        ];
        
        // Si es admin, agregar widget de otras cajas abiertas
        if (auth()->user()->role === 'admin') {
            $widgets[] = \App\Filament\Widgets\OtherOpenCashSessionsWidget::class;
        }
        
        return $widgets;
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Widgets\CashSessionHistoryWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        if ($this->openSession) {
            return [
                Action::make('close_session')
                    ->label('Cerrar Caja')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->modalHeading('Cerrar Caja #' . $this->openSession->id)
                    ->modalDescription('Ingresa el monto contado físicamente para cerrar la caja')
                    ->modalWidth('2xl')
                    ->form([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('initial_amount')
                                    ->label('Monto Inicial')
                                    ->content('$' . number_format($this->openSession->initial_amount, 0, ',', '.')),
                                Placeholder::make('sales')
                                    ->label('Ventas del Día')
                                    ->content(function () {
                                        $theoretical = $this->openSession->calculateTheoreticalAmount();
                                        return '$' . number_format($theoretical - $this->openSession->initial_amount, 0, ',', '.');
                                    }),
                                Placeholder::make('theoretical')
                                    ->label('Monto Teórico')
                                    ->content('$' . number_format($this->openSession->calculateTheoreticalAmount(), 0, ',', '.')),
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
                            ->content(function ($get) {
                                $countedAmount = $get('counted_amount');
                                if (!$countedAmount) {
                                    return 'Ingresa el monto contado';
                                }
                                
                                $cleanValue = (int) str_replace(['.', ','], '', $countedAmount);
                                $theoretical = $this->openSession->calculateTheoreticalAmount();
                                $difference = $cleanValue - $theoretical;
                                
                                if ($difference > 0) {
                                    return '✅ Sobran $' . number_format($difference, 0, ',', '.');
                                } elseif ($difference < 0) {
                                    return '❌ Faltan $' . number_format(abs($difference), 0, ',', '.');
                                } else {
                                    return '✓ Cuadra exacto';
                                }
                            })
                            ->extraAttributes(function ($get) {
                                $countedAmount = $get('counted_amount');
                                if (!$countedAmount) {
                                    return ['style' => 'color: #6b7280;'];
                                }
                                
                                $cleanValue = (int) str_replace(['.', ','], '', $countedAmount);
                                $theoretical = $this->openSession->calculateTheoreticalAmount();
                                $difference = $cleanValue - $theoretical;
                                
                                $color = $difference >= 0 ? '#10b981' : '#ef4444';
                                return ['style' => "color: {$color}; font-weight: bold; font-size: 1.125rem;"];
                            }),
                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Observaciones del cierre de caja...')
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data) {
                        $theoretical = $this->openSession->calculateTheoreticalAmount();
                        
                        // Limpiar el valor de puntos y comas
                        $countedAmount = (int) str_replace(['.', ','], '', $data['counted_amount']);
                        
                        $this->openSession->update([
                            'closed_at' => now(),
                            'counted_amount' => $countedAmount,
                            'theoretical_amount' => $theoretical,
                            'difference' => $countedAmount - $theoretical,
                            'notes' => $data['notes'] ?? null,
                            'status' => 'closed',
                        ]);

                        Notification::make()
                            ->title('Caja Cerrada')
                            ->body('La caja ha sido cerrada exitosamente')
                            ->success()
                            ->send();

                        $this->openSession = null;
                        $this->redirect(route('filament.admin.resources.cash-sessions.index'));
                    }),
            ];
        }

        return [
            Action::make('open_session')
                ->label('Abrir Caja')
                ->icon('heroicon-o-lock-open')
                ->color('success')
                ->form([
                    TextInput::make('initial_amount')
                        ->label('Monto Inicial')
                        ->required()
                        ->prefix('$')
                        ->placeholder('50.000')
                        ->helperText('Dinero en efectivo con el que inicia la caja')
                        ->extraInputAttributes(['data-money-format' => true])
                        ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace(['.', ','], '', $state) : 0),
                ])
                ->action(function (array $data) {
                    // Limpiar el valor de puntos y comas
                    $initialAmount = (int) str_replace(['.', ','], '', $data['initial_amount']);
                    
                    CashSession::create([
                        'user_id' => auth()->id(),
                        'opened_at' => now(),
                        'initial_amount' => $initialAmount,
                        'status' => 'open',
                    ]);

                    Notification::make()
                        ->title('Caja Abierta')
                        ->body('La caja ha sido abierta exitosamente')
                        ->success()
                        ->send();

                    return redirect()->route('filament.admin.resources.cash-sessions.index');
                }),
        ];
    }
}
