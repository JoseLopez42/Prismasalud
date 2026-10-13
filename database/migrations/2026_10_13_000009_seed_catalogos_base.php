<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Datos base para poder agendar citas y formular medicamentos
     * ("estamos iniciando"). Solo inserta lo que falte, así que se
     * puede correr sobre una base que ya tenga datos.
     */
    public function up(): void
    {
        $ahora = now();

        // Estados de una cita
        DB::table('estado_cita')->insertOrIgnore([
            ['nombre' => 'Pendiente',  'descripcion' => 'La cita está agendada y aún no ha sido atendida.', 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Confirmada', 'descripcion' => 'La cita fue confirmada por la IPS.',                 'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Atendida',   'descripcion' => 'El paciente ya fue atendido por el médico.',        'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Cancelada',  'descripcion' => 'La cita fue cancelada.',                            'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);

        // Tipos de cita
        DB::table('tipo_cita')->insertOrIgnore([
            ['nombre' => 'Consulta general',       'descripcion' => 'Consulta médica general.',                         'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Consulta de control',    'descripcion' => 'Seguimiento de un tratamiento o diagnóstico previo.',  'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Urgencia',               'descripcion' => 'Atención prioritaria no programada.',                'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Toma de laboratorio',    'descripcion' => 'Toma de muestras para exámenes de laboratorio.',    'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);

        // Catálogo de medicamentos de apoyo
        DB::table('medicamento')->insertOrIgnore([
            ['nombre' => 'Acetaminofén', 'presentacion' => 'Tableta 500 mg',  'descripcion' => 'Analgésico y antipirético.',            'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Ibuprofeno',   'presentacion' => 'Tableta 400 mg',  'descripcion' => 'Antiinflamatorio no esteroideo.',       'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Amoxicilina',  'presentacion' => 'Cápsula 500 mg',  'descripcion' => 'Antibiótico de amplio espectro.',       'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Loratadina',   'presentacion' => 'Tableta 10 mg',   'descripcion' => 'Antihistamínico.',                      'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Omeprazol',    'presentacion' => 'Cápsula 20 mg',   'descripcion' => 'Inhibidor de la bomba de protones.',    'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Diclofenaco',  'presentacion' => 'Tableta 50 mg',   'descripcion' => 'Antiinflamatorio y analgésico.',        'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Metformina',   'presentacion' => 'Tableta 850 mg',  'descripcion' => 'Antidiabético oral.',                   'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
            ['nombre' => 'Losartán',     'presentacion' => 'Tableta 50 mg',   'descripcion' => 'Antihipertensivo.',                     'activo' => 1, 'created_at' => $ahora, 'updated_at' => $ahora],
        ]);
    }

    public function down(): void
    {
        // No se eliminan los datos base para no borrar información que
        // el administrador pudo haber creado encima.
    }
};
