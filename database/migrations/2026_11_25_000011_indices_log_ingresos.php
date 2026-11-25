<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->indexes() as $index) {
            if (!$this->hasIndex($index['name'])) {
                DB::statement("CREATE INDEX {$index['name']} ON log_ingresos ({$index['columns']})");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes() as $index) {
            if ($this->hasIndex($index['name'])) {
                DB::statement("DROP INDEX {$index['name']} ON log_ingresos");
            }
        }
    }

    private function indexes(): array
    {
        return [
            ['name' => 'idx_log_ingresos_cedula_fecha', 'columns' => 'cedula, fecha'],
            ['name' => 'idx_log_ingresos_tipo_fecha', 'columns' => 'tipo_usuario, fecha'],
            ['name' => 'idx_log_ingresos_fecha', 'columns' => 'fecha'],
        ];
    }

    private function hasIndex(string $name): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            ['log_ingresos', $name]
        )->total > 0;
    }
};
