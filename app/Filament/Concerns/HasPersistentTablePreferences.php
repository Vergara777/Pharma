<?php

namespace App\Filament\Concerns;

use App\Models\UserTablePreference;
use Filament\Tables\Table;

trait HasPersistentTablePreferences
{
    protected function getTablePreferenceName(): string
    {
        return static::class;
    }

    public function bootedHasPersistentTablePreferences(): void
    {
        // Cargar preferencias guardadas
        if (auth()->check()) {
            $this->loadTablePreferences();
        }
    }

    protected function loadTablePreferences(): void
    {
        $preference = UserTablePreference::where('user_id', auth()->id())
            ->where('table_name', $this->getTablePreferenceName())
            ->first();

        if ($preference) {
            // Aplicar preferencias de paginación
            if ($preference->per_page) {
                session()->put("tables.{$this->getTablePreferenceName()}.perPage", $preference->per_page);
            }

            // Aplicar preferencias de columnas
            if ($preference->column_toggles) {
                session()->put("tables.{$this->getTablePreferenceName()}.toggledColumns", $preference->column_toggles);
            }
        }
    }

    public function updatedPaginationPageOption($value): void
    {
        $this->saveTablePreference('per_page', $value);
    }

    protected function saveTablePreference(string $key, mixed $value): void
    {
        if (!auth()->check()) {
            return;
        }

        UserTablePreference::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'table_name' => $this->getTablePreferenceName(),
            ],
            [
                $key => $value,
            ]
        );
    }
}
