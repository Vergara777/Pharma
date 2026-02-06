<?php

namespace App\Filament\Resources\CashSessionResource\Pages;

use App\Filament\Resources\CashSessionResource;
use App\Models\CashSession;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;

class ManageCashSession extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = CashSessionResource::class;

    protected string $view = 'filament.resources.cash-session-resource.pages.manage-cash-session';

    public CashSession $record;
    
    public ?array $data = [];

    public function mount(CashSession $record): void
    {
        $this->record = $record;
        
        // Calcular monto teórico
        $this->record->theoretical_amount = $this->record->calculateTheoreticalAmount();
        $this->record->save();
        
        $this->form->fill([
            'counted_amount' => $this->record->counted_amount,
            'notes' => $this->record->notes,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TextInput::make('counted_amount')
                    ->label('Monto Contado')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->placeholder('150.000')
                    ->helperText('Dinero físico contado en la caja')
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->calculateDifference($state);
                    }),
                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->placeholder('Observaciones del cierre de caja...'),
            ])
            ->statePath('data');
    }

    protected function calculateDifference($countedAmount)
    {
        if ($countedAmount && $this->record->theoretical_amount) {
            $this->record->difference = $countedAmount - $this->record->theoretical_amount;
        }
    }

    public function closeSession()
    {
        $data = $this->form->getState();
        
        $this->record->update([
            'closed_at' => now(),
            'counted_amount' => $data['counted_amount'],
            'difference' => $data['counted_amount'] - $this->record->theoretical_amount,
            'notes' => $data['notes'],
            'status' => 'closed',
        ]);

        Notification::make()
            ->title('Caja Cerrada')
            ->body('La caja ha sido cerrada exitosamente')
            ->success()
            ->send();

        return redirect()->route('filament.admin.resources.cash-sessions.index');
    }

    public function getTitle(): string
    {
        return 'Cerrar Caja #' . $this->record->id;
    }
}
