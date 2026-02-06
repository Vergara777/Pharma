<?php

namespace App\Observers;

use App\Models\Lote;
use App\Models\Product;

class LoteObserver
{
    /**
     * Handle the Lote "created" event.
     */
    public function created(Lote $lote): void
    {
        // Aumentar el stock del producto cuando se crea un lote
        $product = $lote->product;
        if ($product) {
            $product->stock += $lote->cantidad_inicial;
            $product->save();
            
            // Registrar el movimiento
            $lote->movimientos()->create([
                'tipo_movimiento' => 'entrada',
                'cantidad' => $lote->cantidad_inicial,
                'cantidad_anterior' => 0,
                'cantidad_nueva' => $lote->cantidad_inicial,
                'motivo' => 'Ingreso inicial del lote ' . $lote->codigo_lote,
                'usuario_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Handle the Lote "updated" event.
     */
    public function updated(Lote $lote): void
    {
        // No hacer nada aquí - el método registrarMovimiento() ya maneja la actualización del stock
        // Si se actualiza directamente cantidad_actual sin usar registrarMovimiento, 
        // se debe hacer manualmente
    }

    /**
     * Handle the Lote "deleted" event.
     */
    public function deleted(Lote $lote): void
    {
        // Restar el stock del producto cuando se elimina un lote
        $product = $lote->product;
        if ($product) {
            $product->stock -= $lote->cantidad_actual;
            $product->save();
        }
    }
}
