<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| DBA-09 (A): TIMESTAMP -> DATETIME.
|
| TIMESTAMP solo admite fechas hasta el 19/01/2038 (problema del año 2038):
| una licencia de 2 años comprada en 2036 ya no se podría guardar.
| DATETIME llega hasta el año 9999 y guarda la fecha tal cual, sin convertir
| de zona horaria.
|
| La conversión usa la zona horaria de la conexión (SYSTEM = Colombia, igual
| que config('app.timezone') = America/Bogota), así que las fechas quedan
| exactamente como se ven hoy en la app.
|
| Se respeta lo que cada columna ya tenía: si acepta NULL y su valor por defecto.
|
| Ejecutar SOLO esta migración, con la conexión de migraciones:
|   php artisan migrate --database=migraciones --path=database/migrations/NOMBRE_DE_ESTE_ARCHIVO.php
*/
return new class extends Migration
{
    // Tablas con created_at y updated_at
    private array $tablasConTimestamps = [
        'afiliacion', 'beneficiarios', 'catalogo_procedimiento', 'cita', 'ciudades',
        'consultorio', 'departamentos', 'detalle_afiliado_afiliacion', 'detalle_procedimiento',
        'diagnostico', 'empresas', 'empresas_respaldo_dba11', 'enfermedad', 'eps',
        'especialidad', 'estado', 'estados_civiles', 'estado_cita', 'estratos', 'generos',
        'grupos_etnicos', 'historia_clinica', 'horario_medico', 'licencias', 'medicamento',
        'medico', 'niveles_educativos', 'ocupaciones', 'paises', 'parentescos', 'planes',
        'prescripcion', 'procedimiento', 'regimen', 'sedes', 'sub_tipo_paciente',
        'tipos_sangre', 'tipos_usuario', 'tipo_cita', 'tipo_procedimiento',
        'transacciones', 'zonas_residencia',
    ];

    // Tablas con otras columnas de fecha
    private array $otrasColumnas = [
        'failed_jobs'           => ['failed_at'],
        'password_reset_tokens' => ['created_at'],
        'users'                 => ['email_verified_at', 'created_at', 'updated_at'],
        'usuarios'              => ['codigo_expira_en', 'created_at', 'updated_at'],
    ];

    public function up(): void
    {
        $this->convertir('timestamp', 'DATETIME');
    }

    public function down(): void
    {
        $this->convertir('datetime', 'TIMESTAMP');
    }

    /**
     * Cambia el tipo de todas las columnas de la lista, de $tipoActual a $tipoNuevo,
     * conservando si aceptan NULL y su valor por defecto.
     * Solo toca las columnas que de verdad tienen $tipoActual (si ya fueron
     * convertidas, las salta: se puede volver a ejecutar sin problema).
     */
    private function convertir(string $tipoActual, string $tipoNuevo): void
    {
        $columnasPorTabla = $this->otrasColumnas;
        foreach ($this->tablasConTimestamps as $tabla) {
            $columnasPorTabla[$tabla] = ['created_at', 'updated_at'];
        }

        foreach ($columnasPorTabla as $tabla => $columnas) {
            if (! Schema::hasTable($tabla)) {
                continue;
            }

            $cambios = [];

            foreach ($columnas as $columna) {
                // Cómo está la columna hoy (tipo, si acepta NULL, valor por defecto)
                $info = DB::selectOne(
                    'SELECT DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
                       FROM information_schema.COLUMNS
                      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$tabla, $columna]
                );

                if (! $info || strtolower($info->DATA_TYPE) !== $tipoActual) {
                    continue;
                }

                $definicion = "`{$columna}` {$tipoNuevo} " . ($info->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL');

                // Conservar el valor por defecto (MariaDB muestra 'NULL' como texto cuando no hay)
                if ($info->COLUMN_DEFAULT !== null && strtoupper($info->COLUMN_DEFAULT) !== 'NULL') {
                    $definicion .= stripos($info->COLUMN_DEFAULT, 'current_timestamp') !== false
                        ? ' DEFAULT CURRENT_TIMESTAMP'
                        : ' DEFAULT ' . DB::getPdo()->quote($info->COLUMN_DEFAULT);
                }

                $cambios[] = "MODIFY {$definicion}";
            }

            // Un solo ALTER por tabla (más rápido que uno por columna)
            if ($cambios) {
                DB::statement("ALTER TABLE `{$tabla}` " . implode(', ', $cambios));
            }
        }
    }
};