<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function foreignKeys(): array
    {
        return [
            ['table' => 'cita', 'key' => 'cita_cedula_usuario_foreign', 'column' => 'cedula_usuario', 'references' => 'usuarios'],
            ['table' => 'cita', 'key' => 'cita_cedula_medico_foreign', 'column' => 'cedula_medico', 'references' => 'medico'],
            ['table' => 'historia_clinica', 'key' => 'historia_clinica_cedula_paciente_foreign', 'column' => 'cedula_paciente', 'references' => 'usuarios'],
            ['table' => 'historia_clinica', 'key' => 'historia_clinica_cedula_medico_foreign', 'column' => 'cedula_medico', 'references' => 'medico'],
            ['table' => 'diagnostico', 'key' => 'diagnostico_cedula_paciente_foreign', 'column' => 'cedula_paciente', 'references' => 'usuarios'],
            ['table' => 'diagnostico', 'key' => 'diagnostico_cedula_medico_foreign', 'column' => 'cedula_medico', 'references' => 'medico'],
            ['table' => 'enfermedad', 'key' => 'enfermedad_cedula_paciente_foreign', 'column' => 'cedula_paciente', 'references' => 'usuarios'],
            ['table' => 'enfermedad', 'key' => 'enfermedad_cedula_medico_foreign', 'column' => 'cedula_medico', 'references' => 'medico'],
            ['table' => 'prescripcion', 'key' => 'prescripcion_cedula_paciente_foreign', 'column' => 'cedula_paciente', 'references' => 'usuarios'],
            ['table' => 'prescripcion', 'key' => 'prescripcion_cedula_medico_foreign', 'column' => 'cedula_medico', 'references' => 'medico'],
            ['table' => 'procedimiento', 'key' => 'procedimiento_cedula_paciente_foreign', 'column' => 'cedula_paciente', 'references' => 'usuarios'],
            ['table' => 'procedimiento', 'key' => 'procedimiento_cedula_medico_foreign', 'column' => 'cedula_medico', 'references' => 'medico'],
        ];
    }

    public function up(): void
    {
        foreach ($this->foreignKeys() as $fk) {
            DB::statement("ALTER TABLE {$fk['table']} DROP FOREIGN KEY {$fk['key']}");
            DB::statement("ALTER TABLE {$fk['table']} ADD CONSTRAINT {$fk['key']} FOREIGN KEY ({$fk['column']}) REFERENCES {$fk['references']} (cedula) ON DELETE RESTRICT ON UPDATE CASCADE");
        }
    }

    public function down(): void
    {
        foreach ($this->foreignKeys() as $fk) {
            DB::statement("ALTER TABLE {$fk['table']} DROP FOREIGN KEY {$fk['key']}");
            DB::statement("ALTER TABLE {$fk['table']} ADD CONSTRAINT {$fk['key']} FOREIGN KEY ({$fk['column']}) REFERENCES {$fk['references']} (cedula) ON DELETE CASCADE ON UPDATE CASCADE");
        }
    }
};
