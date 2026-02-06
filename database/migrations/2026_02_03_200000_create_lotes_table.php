<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('codigo_lote', 100)->comment('Código único del lote');
            $table->date('fecha_vencimiento');
            $table->decimal('costo_unitario', 12, 2)->default(0)->comment('Costo de compra del lote');
            $table->integer('cantidad_inicial')->default(0)->comment('Cantidad recibida inicialmente');
            $table->integer('cantidad_actual')->default(0)->comment('Stock actual del lote');
            $table->enum('estado', ['activo', 'agotado', 'vencido', 'bloqueado'])->default('activo');
            $table->foreignId('proveedor_id')->nullable()->constrained('suppliers')->onDelete('set null')->comment('Proveedor del lote');
            $table->string('documento_compra', 100)->nullable()->comment('Factura o documento de compra');
            $table->string('documento_archivo', 255)->nullable();
            $table->text('notas')->nullable();
            
            // Información regulatoria
            $table->string('registro_sanitario', 100)->nullable()->comment('Registro INVIMA o sanitario');
            $table->string('lote_fabricante', 100)->nullable()->comment('Lote del fabricante');
            $table->date('fecha_fabricacion')->nullable()->comment('Fecha de fabricación del lote');
            
            // Control de calidad
            $table->string('temperatura_almacenamiento', 50)->nullable()->comment('Ej: 2-8°C, Temperatura ambiente');
            $table->boolean('requiere_cadena_frio')->default(false)->comment('Si necesita refrigeración');
            $table->text('condiciones_especiales')->nullable()->comment('Condiciones especiales de almacenamiento');
            
            // Trazabilidad y auditoría
            $table->string('ubicacion_fisica', 100)->nullable()->comment('Estante, pasillo, bodega');
            $table->foreignId('usuario_registro_id')->nullable()->constrained('users')->onDelete('set null')->comment('Usuario que registró el lote');
            $table->dateTime('fecha_ingreso')->nullable()->comment('Fecha y hora de ingreso al inventario');
            $table->text('motivo_bloqueo')->nullable()->comment('Razón si está bloqueado');
            $table->dateTime('fecha_bloqueo')->nullable()->comment('Cuándo se bloqueó');
            
            // Información comercial
            $table->decimal('precio_venta_sugerido', 12, 2)->nullable()->comment('PVP sugerido por el proveedor');
            $table->decimal('descuento_proveedor', 5, 2)->default(0)->comment('Descuento % del proveedor');
            $table->decimal('iva_porcentaje', 5, 2)->default(0)->comment('IVA aplicable');
            
            // Alertas y notificaciones
            $table->integer('dias_alerta_vencimiento')->default(90)->comment('Días antes para alertar');
            $table->integer('cantidad_minima_alerta')->default(10)->comment('Stock mínimo para alertar');
            $table->boolean('alerta_enviada')->default(false)->comment('Si ya se envió alerta de vencimiento');
            $table->dateTime('fecha_alerta_enviada')->nullable()->comment('Cuándo se envió la alerta');
            
            // Control de movimientos
            $table->integer('cantidad_vendida')->default(0)->comment('Total vendido de este lote');
            $table->integer('cantidad_devuelta')->default(0)->comment('Cantidad devuelta por clientes');
            $table->integer('cantidad_ajustada')->default(0)->comment('Ajustes de inventario (+ o -)');
            $table->dateTime('ultimo_movimiento')->nullable()->comment('Última vez que se movió stock');
            
            // Información adicional
            $table->text('observaciones_calidad')->nullable()->comment('Observaciones de control de calidad');
            $table->boolean('es_muestra_medica')->default(false)->comment('Si es muestra gratis');
            $table->boolean('requiere_receta')->default(false)->comment('Si necesita receta médica');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->unique(['product_id', 'codigo_lote'], 'uk_producto_lote');
            $table->index('fecha_vencimiento', 'idx_fecha_vencimiento');
            $table->index('estado', 'idx_estado');
            $table->index('fecha_ingreso', 'idx_fecha_ingreso');
            $table->index('ubicacion_fisica', 'idx_ubicacion');
            $table->index('usuario_registro_id', 'idx_usuario_registro');
            $table->index(['estado', 'fecha_vencimiento'], 'idx_estado_vencimiento');
            $table->index('requiere_cadena_frio', 'idx_cadena_frio');
            $table->index(['alerta_enviada', 'fecha_vencimiento'], 'idx_alerta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes');
    }
};
