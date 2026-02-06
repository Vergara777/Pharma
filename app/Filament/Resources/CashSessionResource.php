<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CashSessionResource\Pages\ListCashSessions;
use App\Filament\Resources\CashSessionResource\Pages\ManageCashSession;
use App\Models\CashSession;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action as RecordAction;
use Filament\Notifications\Notification;
use BackedEnum;

class CashSessionResource extends Resource
{
    protected static ?string $model = CashSession::class;

    protected static ?string $modelLabel = 'Caja';

    protected static ?string $pluralModelLabel = 'Cajas';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 5;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCashSessions::route('/'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return true; // Visible para todos los usuarios
    }

    public static function canCreate(): bool
    {
        return false; // Se crea desde la página de lista
    }
    
    public static function canViewAny(): bool
    {
        return true; // Todos pueden ver las cajas
    }
}
