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
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Chave Estrangeira para a tabela 'roles'
            $table->foreignId('role_id')->constrained('roles');

            $table->string('name', 100);
            $table->string('email')->unique();
            $table->string('cpf', 14)->unique();
            $table->string('password')->nullable(); // Nulável porque o colaborador não terá senha ao se cadastrar
            $table->string('phone_number', 20)->nullable();
            $table->string('postal_code', 9)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('neighborhood', 40)->nullable();
            $table->string('city', 30)->nullable();
            $table->string('state', 2)->nullable();

            $table->rememberToken(); // Coluna para a funcionalidade "Lembrar-me"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
