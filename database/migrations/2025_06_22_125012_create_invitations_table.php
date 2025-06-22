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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('token', 64)->unique(); // Token seguro e único

            // Usando enum para o status, com um valor padrão
            $table->enum('status', ['pending', 'completed', 'expired'])->default('pending');

            $table->timestamp('expires_at'); // Data de expiração do token

            // Chave estrangeira para 'users', pode ser nula
            $table->foreignId('registered_user_id')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
