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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->integer('qty')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->decimal('total', 10, 2);
            $table->decimal('subtotal', 10, 2)->default(0.00);
            $table->decimal('discount_percent', 5, 2)->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->decimal('tax_rate', 5, 2)->default(19.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('grand_total', 10, 2)->default(0.00);
            $table->enum('status', ['active', 'cancelled', 'returned'])->default('active');
            $table->text('cancel_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('payment_method_id')->nullable()->constrained('payment_methods')->nullOnDelete();
            $table->string('payment_reference', 100)->nullable();
            $table->decimal('amount_received', 12, 2)->default(0.00);
            $table->decimal('change_amount', 12, 2)->default(0.00);
            $table->string('invoice_number', 50)->nullable();
            $table->string('invoice_name', 150)->nullable();
            $table->string('invoice_document', 20)->nullable();
            $table->text('invoice_address')->nullable();
            $table->string('invoice_phone', 20)->nullable();
            $table->string('invoice_email', 100)->nullable();
            $table->string('customer_name', 150)->nullable();
            $table->string('customer_phone', 30)->nullable();
            $table->string('customer_email', 191)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_role', 32)->nullable();
            $table->string('user_name', 150)->nullable();
            $table->unsignedBigInteger('cash_session_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            // Índices
            $table->index('product_id');
            $table->index('status');
            $table->index('cancelled_by');
            $table->index('payment_method_id');
            $table->index('user_id');
            $table->index('cash_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
