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
        Role::create(['name' => 'Admin Nacional', 'slug' => 'national_admin']);
        Role::create(['name' => 'Admin Regional', 'slug' => 'regional_admin']);
        Role::create(['name' => 'Admin de Unidade', 'slug' => 'unit_admin']);
        Role::create(['name' => 'Docente', 'slug' => 'teacher']);
        Role::create(['name' => 'Aluno', 'slug' => 'student']);
    }
}