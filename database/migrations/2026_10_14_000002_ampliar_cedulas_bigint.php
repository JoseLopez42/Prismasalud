<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function cedulaForeignKeys(): array
    {
        return [
            ['table' => 'cita', 'key' => 'cita_cedula_usuario_foreign', 'column' => 'cedula_usuario'],
            ['table' => 'historia_clinica', 'key' => 'historia_clinica_cedula_paciente_foreign', 'column' => 'cedula_paciente'],
            ['table' => 'diagnostico', 'key' => 'diagnostico_cedula_paciente_foreign', 'column' => 'cedula_paciente'],
            ['table' => 'enfermedad', 'key' => 'enfermedad_cedula_paciente_foreign', 'column' => 'cedula_paciente'],
            ['table' => 'prescripcion', 'key' => 'prescripcion_cedula_paciente_foreign', 'column' => 'cedula_paciente'],
            ['table' => 'procedimiento', 'key' => 'procedimiento_cedula_paciente_foreign', 'column' => 'cedula_paciente'],
        ];
    }

    public function up(): void
    {
        // Drop foreign keys that depend on the columns we are widening.
        DB::statement('ALTER TABLE afiliacion DROP FOREIGN KEY afiliacion_cc_afiliado_foreign');
        DB::statement('ALTER TABLE afiliacion DROP FOREIGN KEY afiliacion_cc_usuario_foreign');
        DB::statement('ALTER TABLE beneficiarios DROP FOREIGN KEY beneficiarios_cc_cotizante_foreign');
        DB::statement('ALTER TABLE detalle_afiliado_afiliacion DROP FOREIGN KEY detalle_afiliado_afiliacion_cc_afiliado_foreign');
        DB::statement('ALTER TABLE transacciones DROP FOREIGN KEY transacciones_usuario_cedula_foreign');

        foreach ($this->cedulaForeignKeys() as $fk) {
            if (Schema::hasTable($fk['table'])) {
                DB::statement("ALTER TABLE {$fk['table']} DROP FOREIGN KEY {$fk['key']}");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE usuarios MODIFY cedula BIGINT UNSIGNED NOT NULL');

        foreach ($this->cedulaForeignKeys() as $fk) {
            if (Schema::hasTable($fk['table'])) {
                DB::statement("ALTER TABLE {$fk['table']} MODIFY {$fk['column']} BIGINT UNSIGNED NOT NULL");
            }
        }

        DB::statement('ALTER TABLE beneficiarios MODIFY cc_afiliado BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE beneficiarios MODIFY cc_cotizante BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE afiliacion MODIFY cc_usuario BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE afiliacion MODIFY cc_afiliado BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE detalle_afiliado_afiliacion MODIFY cc_afiliado BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE transacciones MODIFY usuario_cedula BIGINT UNSIGNED NULL');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Re-add foreign keys with the new column types.
        DB::statement('ALTER TABLE afiliacion ADD CONSTRAINT afiliacion_cc_afiliado_foreign FOREIGN KEY (cc_afiliado) REFERENCES beneficiarios (cc_afiliado)');
        DB::statement('ALTER TABLE afiliacion ADD CONSTRAINT afiliacion_cc_usuario_foreign FOREIGN KEY (cc_usuario) REFERENCES usuarios (cedula)');
        DB::statement('ALTER TABLE beneficiarios ADD CONSTRAINT beneficiarios_cc_cotizante_foreign FOREIGN KEY (cc_cotizante) REFERENCES beneficiarios (cc_afiliado) ON DELETE SET NULL');
        DB::statement('ALTER TABLE detalle_afiliado_afiliacion ADD CONSTRAINT detalle_afiliado_afiliacion_cc_afiliado_foreign FOREIGN KEY (cc_afiliado) REFERENCES beneficiarios (cc_afiliado)');
        DB::statement('ALTER TABLE transacciones ADD CONSTRAINT transacciones_usuario_cedula_foreign FOREIGN KEY (usuario_cedula) REFERENCES usuarios (cedula) ON DELETE SET NULL');

        foreach ($this->cedulaForeignKeys() as $fk) {
            if (Schema::hasTable($fk['table'])) {
                DB::statement("ALTER TABLE {$fk['table']} ADD CONSTRAINT {$fk['key']} FOREIGN KEY ({$fk['column']}) REFERENCES usuarios (cedula) ON UPDATE CASCADE ON DELETE CASCADE");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->cedulaForeignKeys() as $fk) {
            if (Schema::hasTable($fk['table'])) {
                DB::statement("ALTER TABLE {$fk['table']} DROP FOREIGN KEY {$fk['key']}");
            }
        }

        DB::statement('ALTER TABLE transacciones DROP FOREIGN KEY transacciones_usuario_cedula_foreign');
        DB::statement('ALTER TABLE detalle_afiliado_afiliacion DROP FOREIGN KEY detalle_afiliado_afiliacion_cc_afiliado_foreign');
        DB::statement('ALTER TABLE beneficiarios DROP FOREIGN KEY beneficiarios_cc_cotizante_foreign');
        DB::statement('ALTER TABLE afiliacion DROP FOREIGN KEY afiliacion_cc_usuario_foreign');
        DB::statement('ALTER TABLE afiliacion DROP FOREIGN KEY afiliacion_cc_afiliado_foreign');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE transacciones MODIFY usuario_cedula INT UNSIGNED NULL');
        DB::statement('ALTER TABLE detalle_afiliado_afiliacion MODIFY cc_afiliado INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE afiliacion MODIFY cc_afiliado INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE afiliacion MODIFY cc_usuario INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE beneficiarios MODIFY cc_cotizante INT UNSIGNED NULL');
        DB::statement('ALTER TABLE beneficiarios MODIFY cc_afiliado INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE usuarios MODIFY cedula INT UNSIGNED NOT NULL');

        foreach ($this->cedulaForeignKeys() as $fk) {
            if (Schema::hasTable($fk['table'])) {
                DB::statement("ALTER TABLE {$fk['table']} MODIFY {$fk['column']} INT UNSIGNED NOT NULL");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::statement('ALTER TABLE afiliacion ADD CONSTRAINT afiliacion_cc_afiliado_foreign FOREIGN KEY (cc_afiliado) REFERENCES beneficiarios (cc_afiliado)');
        DB::statement('ALTER TABLE afiliacion ADD CONSTRAINT afiliacion_cc_usuario_foreign FOREIGN KEY (cc_usuario) REFERENCES usuarios (cedula)');
        DB::statement('ALTER TABLE beneficiarios ADD CONSTRAINT beneficiarios_cc_cotizante_foreign FOREIGN KEY (cc_cotizante) REFERENCES beneficiarios (cc_afiliado) ON DELETE SET NULL');
        DB::statement('ALTER TABLE detalle_afiliado_afiliacion ADD CONSTRAINT detalle_afiliado_afiliacion_cc_afiliado_foreign FOREIGN KEY (cc_afiliado) REFERENCES beneficiarios (cc_afiliado)');
        DB::statement('ALTER TABLE transacciones ADD CONSTRAINT transacciones_usuario_cedula_foreign FOREIGN KEY (usuario_cedula) REFERENCES usuarios (cedula) ON DELETE SET NULL');

        foreach ($this->cedulaForeignKeys() as $fk) {
            if (Schema::hasTable($fk['table'])) {
                DB::statement("ALTER TABLE {$fk['table']} ADD CONSTRAINT {$fk['key']} FOREIGN KEY ({$fk['column']}) REFERENCES usuarios (cedula) ON UPDATE CASCADE ON DELETE CASCADE");
            }
        }
    }
};
