<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign keys that depend on the medico columns we are widening.
        $fks = [
            'cita_cedula_medico_foreign',
            'diagnostico_cedula_medico_foreign',
            'enfermedad_cedula_medico_foreign',
            'historia_clinica_cedula_medico_foreign',
            'horario_medico_cedula_medico_foreign',
            'prescripcion_cedula_medico_foreign',
            'procedimiento_cedula_medico_foreign',
        ];
        foreach ($fks as $fk) {
            $table = str_replace('_cedula_medico_foreign', '', $fk);
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$fk}");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE empresas MODIFY usuario_cedula BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE licencias MODIFY usuario_cedula BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE medico MODIFY cedula BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE cita MODIFY cedula_medico BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE diagnostico MODIFY cedula_medico BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE enfermedad MODIFY cedula_medico BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE historia_clinica MODIFY cedula_medico BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE horario_medico MODIFY cedula_medico BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE prescripcion MODIFY cedula_medico BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE procedimiento MODIFY cedula_medico BIGINT UNSIGNED NOT NULL');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Re-add foreign keys with the new column types.
        DB::statement('ALTER TABLE cita ADD CONSTRAINT cita_cedula_medico_foreign FOREIGN KEY (cedula_medico) REFERENCES medico (cedula) ON UPDATE CASCADE ON DELETE CASCADE');
        DB::statement('ALTER TABLE diagnostico ADD CONSTRAINT diagnostico_cedula_medico_foreign FOREIGN KEY (cedula_medico) REFERENCES medico (cedula) ON UPDATE CASCADE ON DELETE CASCADE');
        DB::statement('ALTER TABLE enfermedad ADD CONSTRAINT enfermedad_cedula_medico_foreign FOREIGN KEY (cedula_medico) REFERENCES medico (cedula) ON UPDATE CASCADE ON DELETE CASCADE');
        DB::statement('ALTER TABLE historia_clinica ADD CONSTRAINT historia_clinica_cedula_medico_foreign FOREIGN KEY (cedula_medico) REFERENCES medico (cedula) ON UPDATE CASCADE ON DELETE CASCADE');
        DB::statement('ALTER TABLE horario_medico ADD CONSTRAINT horario_medico_cedula_medico_foreign FOREIGN KEY (cedula_medico) REFERENCES medico (cedula) ON UPDATE CASCADE ON DELETE CASCADE');
        DB::statement('ALTER TABLE prescripcion ADD CONSTRAINT prescripcion_cedula_medico_foreign FOREIGN KEY (cedula_medico) REFERENCES medico (cedula) ON UPDATE CASCADE ON DELETE CASCADE');
        DB::statement('ALTER TABLE procedimiento ADD CONSTRAINT procedimiento_cedula_medico_foreign FOREIGN KEY (cedula_medico) REFERENCES medico (cedula) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    public function down(): void
    {
        $fks = [
            'cita_cedula_medico_foreign',
            'diagnostico_cedula_medico_foreign',
            'enfermedad_cedula_medico_foreign',
            'historia_clinica_cedula_medico_foreign',
            'horario_medico_cedula_medico_foreign',
            'prescripcion_cedula_medico_foreign',
            'procedimiento_cedula_medico_foreign',
        ];
        foreach ($fks as $fk) {
            $table = str_replace('_cedula_medico_foreign', '', $fk);
            DB::statement("ALTER TABLE {$table} DROP FOREIGN KEY {$fk}");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE empresas MODIFY usuario_cedula INT UNSIGNED NULL');
        DB::statement('ALTER TABLE licencias MODIFY usuario_cedula INT UNSIGNED NULL');
        DB::statement('ALTER TABLE medico MODIFY cedula INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE cita MODIFY cedula_medico INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE diagnostico MODIFY cedula_medico INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE enfermedad MODIFY cedula_medico INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE historia_clinica MODIFY cedula_medico INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE horario_medico MODIFY cedula_medico INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE prescripcion MODIFY cedula_medico INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE procedimiento MODIFY cedula_medico INT UNSIGNED NOT NULL');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        foreach ($fks as $fk) {
            $table = str_replace('_cedula_medico_foreign', '', $fk);
            DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$fk} FOREIGN KEY (cedula_medico) REFERENCES medico (cedula) ON UPDATE CASCADE ON DELETE CASCADE");
        }
    }
};