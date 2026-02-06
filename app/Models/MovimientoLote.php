<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoLote extends Model
{
    protected $table = 'movimientos_lote';

    protected $fillable = [
        'lote_id',
        'tipo_movimiento',
        'cantidad',
        'cantidad_anterior',
        'cantidad_nueva',
        'motivo',
        'venta_id',
        'usuario_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Ventas::class, 'venta_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Métodos útiles
    public function getTipoMovimientoLabelAttribute(): string
    {
        return match($this->tipo_movimiento) {
            'entrada' => 'Entrada',
            'venta' => 'Venta',
            'devolucion' => 'Devolución',
            'ajuste' => 'Ajuste',
            'merma' => 'Merma',
            'vencimiento' => 'Vencimiento',
            'transferencia' => 'Transferencia',
            default => $this->tipo_movimiento,
        };
    }

    public function getTipoMovimientoColorAttribute(): string
    {
        return match($this->tipo_movimiento) {
            'entrada' => 'success',
            'venta' => 'primary',
            'devolucion' => 'warning',
            'ajuste' => 'info',
            'merma' => 'danger',
            'vencimiento' => 'danger',
            'transferencia' => 'secondary',
            default => 'gray',
        };
    }
}
