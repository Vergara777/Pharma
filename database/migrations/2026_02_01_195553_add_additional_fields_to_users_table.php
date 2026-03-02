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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->after('email');
            $table->string('address', 255)->nullable()->after('phone');
            $table->string('position', 100)->nullable()->after('address');
            $table->date('hire_date')->nullable()->after('position');
            $table->string('id_number', 50)->nullable()->after('hire_date');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('id_number');
            $table->string('avatar', 255)->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'position',
                'hire_date',
                'id_number',
                'status',
                'avatar',
            ]);
        });
    }
};
