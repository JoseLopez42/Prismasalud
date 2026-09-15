<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminPruebaSeeder extends Seeder
{
    /**
     * Crea un usuario Administrador de prueba para poder entrar
     * al sistema mientras se termina el formulario de registro.
     * Cédula y contraseña de prueba: cámbialas si quieres.
     */
    public function run(): void
    {
        DB::table('usuarios')->insert([
            'cedula'          => 1000000001,       // cédula de prueba
            'nombres'         => 'Admin',
            'apellidos'       => 'Prueba',
            'correo'          => 'admin@prismasaludips.com',
            'contrasena'      => bcrypt('admin123'), // contraseña de prueba: admin123
            'tipo_usuario_id' => 1,                 // 1 = Administrador (según el seeder de tipos)
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }
}