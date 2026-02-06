<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Lote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'codigo_lote',
        'fecha_vencimiento',
        'costo_unitario',
        'cantidad_inicial',
        'cantidad_actual',
        'estado',
        'proveedor_id',
        'documento_compra',
        'documento_archivo',
        'notas',
        'registro_sanitario',
        'lote_fabricante',
        'fecha_fabricacion',
        'temperatura_almacenamiento',
        'requiere_cadena_frio',
        'condiciones_especiales',
        'ubicacion_fisica',
        'usuario_registro_id',
        'fecha_ingreso',
        'motivo_bloqueo',
        'fecha_bloqueo',
        'precio_venta_sugerido',
        'descuento_proveedor',
        'iva_porcentaje',
        'dias_alerta_vencimiento',
        'cantidad_minima_alerta',
        'alerta_enviada',
        'fecha_alerta_enviada',
        'cantidad_vendida',
        'cantidad_devuelta',
        'cantidad_ajustada',
        'ultimo_movimiento',
        'observaciones_calidad',
        'es_muestra_medica',
        'requiere_receta',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_fabricacion' => 'date',
        'fecha_ingreso' => 'datetime',
        'fecha_bloqueo' => 'datetime',
        'fecha_alerta_enviada' => 'datetime',
        'ultimo_movimiento' => 'datetime',
        'precio_venta_sugerido' => 'integer',
        'descuento_proveedor' => 'decimal:2',
        'iva_porcentaje' => 'decimal:2',
        'requiere_cadena_frio' => 'boolean',
        'alerta_enviada' => 'boolean',
        'es_muestra_medica' => 'boolean',
        'requiere_receta' => 'boolean',
    ];

    // Relaciones
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'proveedor_id');
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoLote::class);
    }

    // Métodos útiles
    public function diasParaVencer(): int
    {
        return Carbon::now()->diffInDays($this->fecha_vencimiento, false);
    }

    public function estaProximoAVencer(): bool
    {
        return $this->diasParaVencer() <= $this->dias_alerta_vencimiento && $this->diasParaVencer() > 0;
    }

    public function estaVencido(): bool
    {
        return $this->fecha_vencimiento < Carbon::now();
    }

    public function tieneStockBajo(): bool
    {
        return $this->cantidad_actual <= $this->cantidad_minima_alerta;
    }

    public function registrarMovimiento(string $tipo, int $cantidad, ?int $ventaId = null, ?string $motivo = null): void
    {
        $cantidadAnterior = $this->cantidad_actual;
        $diferenciaStock = 0;
        
        // Actualizar cantidad según el tipo de movimiento
        switch ($tipo) {
            case 'entrada':
            case 'devolucion':
                $this->cantidad_actual += $cantidad;
                $diferenciaStock = $cantidad;
                break;
            case 'salida':
            case 'venta':
            case 'merma':
            case 'vencimiento':
                $this->cantidad_actual -= $cantidad;
                $diferenciaStock = -$cantidad;
                $this->cantidad_vendida += ($tipo === 'venta' ? $cantidad : 0);
                break;
            case 'ajuste':
                $diferenciaStock = $cantidad - $cantidadAnterior;
                $this->cantidad_actual = $cantidad;
                $this->cantidad_ajustada += abs($diferenciaStock);
                break;
        }

        // Actualizar estado si se agotó
        if ($this->cantidad_actual <= 0) {
            $this->estado = 'agotado';
        } elseif ($this->estado === 'agotado' && $this->cantidad_actual > 0) {
            $this->estado = 'activo';
        }

        $this->ultimo_movimiento = now();
        $this->save();
        
        // Actualizar stock del producto
        if ($diferenciaStock != 0) {
            $product = $this->product;
            if ($product) {
                $product->stock += $diferenciaStock;
                $product->save();
            }
        }

        // Registrar el movimiento
        $this->movimientos()->create([
            'tipo_movimiento' => $tipo,
            'cantidad' => $cantidad,
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $this->cantidad_actual,
            'motivo' => $motivo,
            'venta_id' => $ventaId,
            'usuario_id' => auth()->id(),
        ]);
    }

    public function calcularValorInventario(): float
    {
        return $this->cantidad_actual * $this->costo_unitario;
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeProximosAVencer($query, int $dias = 90)
    {
        return $query->where('fecha_vencimiento', '<=', Carbon::now()->addDays($dias))
                    ->where('fecha_vencimiento', '>', Carbon::now())
                    ->where('estado', 'activo');
    }

    public function scopeVencidos($query)
    {
        return $query->where('fecha_vencimiento', '<', Carbon::now());
    }

    public function scopeConStock($query)
    {
        return $query->where('cantidad_actual', '>', 0);
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('cantidad_actual', '<=', 'cantidad_minima_alerta');
    }
}
