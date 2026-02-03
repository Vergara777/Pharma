<?php

namespace App\Filament\Traits;

use App\Models\UserTablePreference;
use Filament\Tables\Table;

trait HasTablePreferences
{
    protected function getTablePreferenceName(): string
    {
        return static::class;
    }

    protected function applyTablePreferences(Table $table): Table
    {
        if (!auth()->check()) {
            return $table;
        }

        $preference = UserTablePreference::where('user_id', auth()->id())
            ->where('table_name', $this->getTablePreferenceName())
            ->first();

        if ($preference && $preference->per_page) {
            $table->defaultPaginationPageOption($preference->per_page);
        }

        return $table;
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
