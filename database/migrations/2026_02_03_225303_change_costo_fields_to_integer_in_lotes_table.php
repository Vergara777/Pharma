<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->integer('costo_unitario')->default(0)->change();
            $table->integer('precio_venta_sugerido')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->decimal('costo_unitario', 12, 2)->default(0)->change();
            $table->decimal('precio_venta_sugerido', 12, 2)->nullable()->change();
        });
    }
};
