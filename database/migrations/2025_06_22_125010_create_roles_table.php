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
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // Cria uma coluna 'id' BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('name', 50)->unique(); // VARCHAR(50) e não permite valores duplicados
            $table->string('slug', 50)->unique(); // VARCHAR(50) único, para usar no código
            $table->timestamps(); // Cria as colunas 'created_at' e 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
