<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_lote', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('lotes')->onDelete('cascade');
            $table->enum('tipo_movimiento', [
                'entrada',
                'venta', 
                'devolucion',
                'ajuste',
                'merma',
                'vencimiento',
                'transferencia'
            ])->comment('Tipo de movimiento del lote');
            $table->integer('cantidad')->comment('Cantidad del movimiento (+ o -)');
            $table->integer('cantidad_anterior')->comment('Stock antes del movimiento');
            $table->integer('cantidad_nueva')->comment('Stock después del movimiento');
            $table->text('motivo')->nullable();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('set null')->comment('Si es por venta');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            // Índices
            $table->index('lote_id', 'idx_lote');
            $table->index('tipo_movimiento', 'idx_tipo');
            $table->index('created_at', 'idx_fecha');
            $table->index('venta_id', 'idx_venta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_lote');
    }
};
