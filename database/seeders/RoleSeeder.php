<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // Importamos o Model 'Role' para poder usá-lo

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Este código cria as 3 linhas na nossa tabela 'roles'
        Role::create(['name' => 'Administrador', 'slug' => 'admin']);
        Role::create(['name' => 'Gente e Cultura', 'slug' => 'gente-e-cultura']);
        Role::create(['name' => 'Colaborador Comum', 'slug' => 'colaborador']);
    }
}