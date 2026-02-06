<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Número de display para ordenamiento
            $table->integer('display_no')->after('id')->default(0);
            
            // Ubicación física en bodega/farmacia
            $table->string('shelf', 10)->nullable()->after('stock')->comment('Estante donde se ubica el producto');
            $table->string('row', 10)->nullable()->after('shelf')->comment('Fila dentro del estante');
            $table->string('position', 10)->nullable()->after('row')->comment('Posición dentro de la fila');
            
            // Fecha de vencimiento (ya existe expires_at, pero la renombramos si es necesario)
            // Si ya tienes expiration_date, puedes comentar esta línea
            if (!Schema::hasColumn('products', 'expires_at')) {
                $table->date('expires_at')->nullable()->after('position');
            }
            
            // Estado del producto
            $table->enum('status', ['active', 'retired'])->default('active')->after('expires_at');
            
            // Costo del producto
            $table->decimal('cost', 10, 2)->default(0.00)->after('price')->comment('Costo de compra');
            
            // Stock mínimo y máximo
            $table->integer('min_stock')->default(5)->after('stock')->comment('Stock mínimo recomendado');
            $table->integer('max_stock')->default(100)->after('min_stock')->comment('Stock máximo recomendado');
            
            // Unidades y presentaciones
            $table->string('unit_name', 50)->default('unidad')->after('max_stock')->comment('Nombre de la unidad suelta (tableta, cápsula, ml, etc.)');
            $table->string('package_name', 100)->nullable()->after('unit_name')->comment('Nombre de la presentación (caja x 10, blíster x 8, etc.)');
            $table->unsignedInteger('units_per_package')->default(1)->after('package_name')->comment('Cantidad de unidades sueltas por presentación');
            
            // Precios por unidad y presentación
            $table->bigInteger('price_unit')->nullable()->after('units_per_package')->comment('Precio por unidad suelta');
            $table->bigInteger('price_package')->nullable()->after('price_unit')->comment('Precio por presentación completa');
            
            // Índices para optimización
            $table->index('display_no', 'idx_products_display_no');
            $table->index('status', 'idx_products_status');
            $table->index(['shelf', 'row', 'position'], 'idx_location');
            $table->index('units_per_package', 'idx_products_unit_package');
            $table->index('expires_at');
        });
        
        // Renombrar columnas existentes si es necesario
        Schema::table('products', function (Blueprint $table) {
            // Renombrar stock_minimum a min_stock si existe
            if (Schema::hasColumn('products', 'stock_minimum')) {
                $table->dropColumn('stock_minimum');
            }
            
            // Renombrar stock_maximum a max_stock si existe
            if (Schema::hasColumn('products', 'stock_maximum')) {
                $table->dropColumn('stock_maximum');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_display_no');
            $table->dropIndex('idx_products_status');
            $table->dropIndex('idx_location');
            $table->dropIndex('idx_products_unit_package');
            
            $table->dropColumn([
                'display_no',
                'shelf',
                'row',
                'position',
                'status',
                'cost',
                'min_stock',
                'max_stock',
                'unit_name',
                'package_name',
                'units_per_package',
                'price_unit',
                'price_package',
            ]);
        });
    }
};
