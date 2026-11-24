<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Carga los 221 tipos de cita oficiales de Colombia.
 *
 * Fuente: Ministerio de Salud y Protección Social - Resumen Digital de
 * Atención en Salud (RDA) v1.0.3 / IHCEcol.
 *   - ValueSet CUPSConsultationCodes: 210 consultas.
 *   - CodeSystem CUPS, capítulo 8907: 11 consultas de urgencias.
 *
 * El CSV con los datos normalizados vive en database/seeders/data/.
 * El seeder es idempotente: se puede ejecutar varias veces sin duplicar.
 *
 * NOTA: por defecto NO se ejecuta en DatabaseSeeder, porque el catálogo
 * se muestra solo de forma visual. Ejecutar manualmente solo si se quiere
 * persistir los 221 tipos en la base:
 *   php artisan db:seed --class=TipoCitaCupsSeeder
 */
class TipoCitaCupsSeeder extends Seeder
{
    public function run(): void
    {
        $csv = __DIR__.'/data/tipos_cita_cups_colombia.csv';

        if (! file_exists($csv)) {
            $this->command?->error("No se encontró el archivo: {$csv}");

            return;
        }

        $handle = fopen($csv, 'r');
        $headers = fgetcsv($handle);

        // Por si el CSV fue exportado con BOM (UTF-8 con firma): el BOM hace
        // que fgetcsv no reconozca las comillas del primer encabezado.
        $headers = array_map(fn ($h) => trim(preg_replace('/^\xEF\xBB\xBF/', '', $h), "\" \t\n\r\0\x0B"), $headers);

        $registros = [];
        while (($fila = fgetcsv($handle)) !== false) {
            if (count($fila) === count($headers)) {
                $registros[] = array_combine($headers, $fila);
            }
        }
        fclose($handle);

        // Especialidades que ya existen en la base del proyecto.
        $especialidades = DB::table('especialidad')
            ->pluck('id', 'nombre')
            ->mapWithKeys(fn ($id, $nombre) => [$this->normalizar($nombre) => $id])
            ->all();

        /*
         * Los tres tipos genéricos que ya tenían citas asociadas pasan a
         * ser sus equivalentes oficiales, conservando su ID y sus citas.
         * "Toma de laboratorio" no es una consulta CUPS y se deja intacto.
         */
        $equivalencias = [
            3 => '890201', // Consulta general       -> primera vez, medicina general
            4 => '890301', // Consulta de control    -> control/seguimiento, medicina general
            5 => '890701', // Urgencia               -> urgencias, medicina general
        ];

        foreach ($equivalencias as $id => $codigo) {
            DB::table('tipo_cita')->where('id', $id)->update(['codigo_cups' => $codigo]);
        }

        $insertados = 0;
        $actualizados = 0;

        foreach ($registros as $registro) {
            $codigo = trim($registro['codigo_cups']);

            if ($codigo === '') {
                continue;
            }

            $nombreEspecialidad = trim($registro['especialidad'] ?? '');
            $payload = [
                'codigo_cups'      => $codigo,
                'nombre'           => trim($registro['nombre']),
                'descripcion'      => trim($registro['nombre_oficial']) ?: trim($registro['nombre']),
                'categoria'        => trim($registro['categoria']) ?: null,
                'fuente'           => trim($registro['fuente']) ?: 'MinSalud_RDA_1.0.3',
                'duracion_minutos' => (int) ($registro['duracion_minutos'] ?: 30),
                'id_especialidad'  => $especialidades[$this->normalizar($nombreEspecialidad)] ?? null,
                'activo'           => filter_var($registro['activo'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            ];

            $existe = DB::table('tipo_cita')->where('codigo_cups', $codigo)->exists();

            if ($existe) {
                DB::table('tipo_cita')
                    ->where('codigo_cups', $codigo)
                    ->update($payload + ['updated_at' => now()]);
                $actualizados++;
            } else {
                DB::table('tipo_cita')->insert($payload + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $insertados++;
            }
        }

        $this->command?->info("Tipos CUPS: {$insertados} insertados, {$actualizados} actualizados.");
    }

    /** Normaliza texto para comparar especialidades sin tildes ni mayúsculas. */
    private function normalizar(string $valor): string
    {
        return trim(preg_replace('/\s+/', ' ', Str::ascii(Str::lower($valor))));
    }
}
