<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_table_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('table_name');
            $table->json('column_toggles')->nullable();
            $table->integer('per_page')->default(25);
            $table->timestamps();
            
            $table->unique(['user_id', 'table_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_table_preferences');
    }
};
