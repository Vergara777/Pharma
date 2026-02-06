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
        Schema::create('cash_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Usuario (cajero) que abre la caja');
            $table->dateTime('opened_at')->useCurrent()->comment('Fecha y hora de apertura');
            $table->decimal('initial_amount', 12, 2)->default(0.00)->comment('Monto inicial en caja');
            $table->dateTime('closed_at')->nullable()->comment('Fecha y hora de cierre');
            $table->decimal('theoretical_amount', 12, 2)->nullable()->comment('Monto teórico calculado (ventas)');
            $table->decimal('counted_amount', 12, 2)->nullable()->comment('Monto contado físicamente');
            $table->decimal('difference', 12, 2)->nullable()->comment('Diferencia (contado - teórico)');
            $table->enum('status', ['open', 'closed'])->default('open')->comment('Estado de la caja');
            $table->text('notes')->nullable()->comment('Notas o comentarios del cierre');
            $table->timestamps();
            
            $table->index('user_id', 'idx_cash_sessions_user_id');
            $table->index('status');
            $table->index('opened_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_sessions');
    }
};
