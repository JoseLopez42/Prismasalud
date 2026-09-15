<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoUsuarioSeeder extends Seeder
{
    /**
     * Inserta los 4 tipos de usuario que maneja el sistema:
     * Administrador, Paciente, Médico y Farmaceuta.
     * Se ejecuta una sola vez (o cada vez que se corra
     * "php artisan db:seed") para tener estos datos base
     * ya cargados en la tabla "tipos_usuario".
     */
    public function run(): void
    {
        DB::table('tipos_usuario')->insert([
            ['nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Paciente',      'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Medico',        'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Farmaceuta',    'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}