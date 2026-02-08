<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convertir los valores existentes de decimal a entero (multiplicar por 100 para no perder centavos)
        // Pero como en Colombia no usamos centavos, simplemente redondeamos
        DB::statement('ALTER TABLE facturas MODIFY subtotal BIGINT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE facturas MODIFY tax BIGINT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE facturas MODIFY discount BIGINT NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE facturas MODIFY total BIGINT NOT NULL DEFAULT 0');
        
        DB::statement('ALTER TABLE factura_items MODIFY price BIGINT NOT NULL');
        DB::statement('ALTER TABLE factura_items MODIFY subtotal BIGINT NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE facturas MODIFY subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE facturas MODIFY tax DECIMAL(10, 2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE facturas MODIFY discount DECIMAL(10, 2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE facturas MODIFY total DECIMAL(10, 2) NOT NULL DEFAULT 0');
        
        DB::statement('ALTER TABLE factura_items MODIFY price DECIMAL(10, 2) NOT NULL');
        DB::statement('ALTER TABLE factura_items MODIFY subtotal DECIMAL(10, 2) NOT NULL');
    }
};
