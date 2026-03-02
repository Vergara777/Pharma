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
        Schema::table('facturas', function (Blueprint $table) {
            $table->bigInteger('subtotal')->default(0)->change();
            $table->bigInteger('tax')->default(0)->change();
            $table->bigInteger('discount')->default(0)->change();
            $table->bigInteger('total')->default(0)->change();
        });

        Schema::table('factura_items', function (Blueprint $table) {
            $table->bigInteger('price')->change();
            $table->bigInteger('subtotal')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->change();
            $table->decimal('tax', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->default(0)->change();
        });

        Schema::table('factura_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('subtotal', 10, 2)->change();
        });
    }
};
