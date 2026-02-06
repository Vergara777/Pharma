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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json
            $table->timestamps();
        });
        
        // Insertar configuraciones por defecto
        DB::table('settings')->insert([
            ['key' => 'pharmacy_name', 'value' => 'Pharma', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pharmacy_address', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pharmacy_phone', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'pharmacy_email', 'value' => '', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'low_stock_alert', 'value' => '1', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'default_stock_minimum', 'value' => '20', 'type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'currency', 'value' => 'COP', 'type' => 'string', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'expiration_alert', 'value' => '1', 'type' => 'boolean', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'expiration_alert_days', 'value' => '30', 'type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
