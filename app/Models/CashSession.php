<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'opened_at',
        'initial_amount',
        'closed_at',
        'theoretical_amount',
        'counted_amount',
        'difference',
        'status',
        'notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'initial_amount' => 'decimal:2',
        'theoretical_amount' => 'decimal:2',
        'counted_amount' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    /**
     * Relación con el usuario (cajero)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con las ventas de esta sesión
     */
    public function ventas(): HasMany
    {
        return $this->hasMany(Ventas::class, 'cash_session_id');
    }

    /**
     * Scope para sesiones abiertas
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope para sesiones cerradas
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Calcular el monto teórico basado en las ventas
     */
    public function calculateTheoreticalAmount(): float
    {
        return $this->initial_amount + 
               $this->ventas()
                    ->where('status', 'active')
                    ->sum('grand_total');
    }

    /**
     * Calcular la diferencia entre contado y teórico
     */
    public function calculateDifference(): float
    {
        if ($this->counted_amount === null || $this->theoretical_amount === null) {
            return 0;
        }
        
        return $this->counted_amount - $this->theoretical_amount;
    }
}
