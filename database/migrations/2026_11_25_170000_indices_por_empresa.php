<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->indexes() as $index) {
            if (! $this->hasIndex($index['table'], $index['name'])) {
                $columnas = implode(', ', array_map(fn ($columna) => "`{$columna}`", $index['columns']));
                DB::statement("CREATE INDEX `{$index['name']}` ON `{$index['table']}` ({$columnas})");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes() as $index) {
            if ($this->hasIndex($index['table'], $index['name'])) {
                DB::statement("DROP INDEX `{$index['name']}` ON `{$index['table']}`");
            }
        }
    }

    private function indexes(): array
    {
        return [
            [
                'table' => 'cita',
                'name' => 'idx_cita_empresa_medico_fecha',
                'columns' => ['empresa_id', 'cedula_medico', 'fecha', 'hora_inicio'],
            ],
            [
                'table' => 'cita',
                'name' => 'idx_cita_empresa_usuario_fecha',
                'columns' => ['empresa_id', 'cedula_usuario', 'fecha'],
            ],
            [
                'table' => 'prescripcion',
                'name' => 'idx_prescripcion_empresa_estado_limite',
                'columns' => ['empresa_id', 'estado', 'fecha_limite'],
            ],
            [
                'table' => 'prescripcion',
                'name' => 'idx_prescripcion_empresa_paciente_estado',
                'columns' => ['empresa_id', 'cedula_paciente', 'estado'],
            ],
            [
                'table' => 'licencias',
                'name' => 'idx_licencias_empresa_estado',
                'columns' => ['empresa_id', 'estado'],
            ],
        ];
    }

    private function hasIndex(string $table, string $name): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $name]
        )->total > 0;
    }
};
