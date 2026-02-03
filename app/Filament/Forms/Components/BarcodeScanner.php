<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\TextInput;

class BarcodeScanner extends TextInput
{
    protected string $view = 'filament.forms.components.barcode-scanner';

    protected function setUp(): void
    {
        parent::setUp();

        $this->suffixIcon('heroicon-o-qr-code');
        
        $this->extraInputAttributes([
            'autocomplete' => 'off',
            'autofocus' => false,
        ]);
    }
}
