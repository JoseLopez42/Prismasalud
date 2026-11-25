<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE beneficiarios SET fecha_nacimiento = NULL, edad = NULL WHERE fecha_nacimiento = '1900-01-01'");

        foreach ($this->checks() as $check) {
            if ($this->hasCheck($check['table'], $check['name'])) {
                continue;
            }

            $this->assertNoViolations($check['table'], $check['name'], $check['condition']);
            DB::statement("ALTER TABLE {$check['table']} ADD CONSTRAINT {$check['name']} CHECK ({$check['condition']})");
        }
    }

    public function down(): void
    {
        // La limpieza de fechas no se revierte
        foreach (array_reverse($this->checks()) as $check) {
            if ($this->hasCheck($check['table'], $check['name'])) {
                DB::statement("ALTER TABLE {$check['table']} DROP CONSTRAINT {$check['name']}");
            }
        }
    }

    private function checks(): array
    {
        return [
            ['table' => 'cita', 'name' => 'chk_cita_hora', 'condition' => 'hora_fin > hora_inicio'],
            ['table' => 'horario_medico', 'name' => 'chk_horario_medico_hora', 'condition' => 'hora_fin > hora_inicio'],
            ['table' => 'prescripcion', 'name' => 'chk_prescripcion_fecha_limite', 'condition' => 'fecha_limite IS NULL OR fecha_limite >= fecha'],
            ['table' => 'prescripcion', 'name' => 'chk_prescripcion_estado', 'condition' => "estado IN ('pendiente','entregada')"],
            ['table' => 'beneficiarios', 'name' => 'chk_beneficiarios_tipo_persona', 'condition' => "tipo_persona IN ('cotizante','beneficiario')"],
            ['table' => 'beneficiarios', 'name' => 'chk_beneficiarios_cotizante_cc', 'condition' => "(tipo_persona = 'cotizante' AND cc_cotizante IS NULL) OR (tipo_persona = 'beneficiario' AND cc_cotizante IS NOT NULL)"],
            ['table' => 'afiliacion', 'name' => 'chk_afiliacion_estrato', 'condition' => 'estrato IS NULL OR estrato BETWEEN 1 AND 6'],
            ['table' => 'beneficiarios', 'name' => 'chk_beneficiarios_estrato', 'condition' => 'estrato IS NULL OR estrato BETWEEN 1 AND 6'],
            ['table' => 'medico', 'name' => 'chk_medico_estado', 'condition' => "estado IN ('activo','inactivo')"],
            ['table' => 'empresas', 'name' => 'chk_empresas_estado', 'condition' => "estado IN ('activa','prueba','suspendida','inactiva','vencida')"],
            ['table' => 'licencias', 'name' => 'chk_licencias_estado', 'condition' => "estado IN ('activa','prueba','suspendida','inactiva','vencida')"],
            ['table' => 'transacciones', 'name' => 'chk_transacciones_tipo', 'condition' => "tipo IN ('compra','renovacion')"],
            ['table' => 'detalle_procedimiento', 'name' => 'chk_detalle_procedimiento_cantidad', 'condition' => 'cantidad > 0'],
        ];
    }

    private function assertNoViolations(string $table, string $name, string $condition): void
    {
        $rows = DB::select("SELECT * FROM {$table} WHERE NOT ({$condition}) LIMIT 10");
        if ($rows) {
            throw new RuntimeException("CHECK {$name} violado en {$table}: " . json_encode($rows));
        }
    }

    private function hasCheck(string $table, string $name): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$table, $name, 'CHECK']
        )->total > 0;
    }
};
