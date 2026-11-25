<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| DBA-11 (D): aislamiento por IPS en las tablas clínicas.
|
| Cada registro clínico guarda a qué empresa (IPS/EPS) pertenece, para que
| una IPS nunca vea los pacientes, citas o historias de otra.
|
| Por ahora empresa_id es OPCIONAL (nullable): la app sigue funcionando.
| En la fase de código los controladores enviarán empresa_id y entonces
| se volverá obligatoria con otra migración.
|
| Los registros que ya existían se asignan a JoSalud IPS (NIT 900076521-2),
| la IPS de la cuenta de pruebas "Admin Prueba". Se busca por NIT y no por id,
| para que funcione igual en la base de cualquiera del equipo.
|
| Ejecutar SOLO esta migración, con la conexión de migraciones:
|   php artisan migrate --database=migraciones --path=database/migrations/NOMBRE_DE_ESTE_ARCHIVO.php
*/
return new class extends Migration
{
    // Tablas clínicas que llevan empresa_id
    private array $tablas = [
        'cita',
        'historia_clinica',
        'prescripcion',
        'diagnostico',
        'enfermedad',
        'procedimiento',
    ];

    // NIT de la IPS a la que se asignan los registros que ya existían
    private string $nitIpsDatosExistentes = '900076521-2';

    public function up(): void
    {
        // 1. Columna + llave foránea en cada tabla.
        //    RESTRICT: no se puede borrar una empresa que tiene registros clínicos
        //    (la historia clínica se conserva mínimo 15 años, Res. 839 de 2017).
        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('id')
                      ->comment('IPS/EPS dueña del registro (DBA-11)');

                $table->foreign('empresa_id', "{$tabla}_empresa_id_foreign")
                      ->references('id')->on('empresas')
                      ->restrictOnDelete()
                      ->cascadeOnUpdate();
            });
        }

        // 2. Asignar los registros que ya existían a JoSalud IPS (si existe en esta base).
        $empresaId = DB::table('empresas')->where('nit', $this->nitIpsDatosExistentes)->value('id');

        if ($empresaId) {
            foreach ($this->tablas as $tabla) {
                DB::table($tabla)->whereNull('empresa_id')->update(['empresa_id' => $empresaId]);
            }
        }
    }

    public function down(): void
    {
        // Primero la llave foránea (depende de la columna), luego la columna.
        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                $table->dropForeign("{$tabla}_empresa_id_foreign");
                $table->dropColumn('empresa_id');
            });
        }
    }
};