<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('departamentos')->insert([
            ['id_departamento' => 1, 'nombre_departamento' => 'Cundinamarca',    'created_at' => now(), 'updated_at' => now()],
            ['id_departamento' => 2, 'nombre_departamento' => 'Antioquia',       'created_at' => now(), 'updated_at' => now()],
            ['id_departamento' => 3, 'nombre_departamento' => 'Tolima',          'created_at' => now(), 'updated_at' => now()],
            ['id_departamento' => 4, 'nombre_departamento' => 'Valle del Cauca', 'created_at' => now(), 'updated_at' => now()],
            ['id_departamento' => 5, 'nombre_departamento' => 'Santander',       'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}