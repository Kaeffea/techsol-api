<?php

namespace Database\Seeders;

// A linha abaixo pode ser removida ou deixada, não faz diferença.
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

// Não precisamos mais do "use App\Models\User;" porque não estamos mais criando usuários aqui.

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // A única coisa que faremos aqui é chamar o seeder que cria os perfis (Roles).
        // O método 'call' é a forma correta de executar outros seeders a partir deste arquivo principal.
        $this->call([
            RoleSeeder::class,
        ]);
    }
}