<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $orphanChecks = [
            'empresas.usuario_cedula' => 'SELECT e.id FROM empresas e LEFT JOIN usuarios u ON e.usuario_cedula = u.cedula WHERE e.usuario_cedula IS NOT NULL AND u.cedula IS NULL LIMIT 10',
            'empresas.licencia_id' => 'SELECT e.id FROM empresas e LEFT JOIN licencias l ON e.licencia_id = l.id WHERE e.licencia_id IS NOT NULL AND l.id IS NULL LIMIT 10',
            'licencias.usuario_cedula' => 'SELECT l.id FROM licencias l LEFT JOIN usuarios u ON l.usuario_cedula = u.cedula WHERE l.usuario_cedula IS NOT NULL AND u.cedula IS NULL LIMIT 10',
            'afiliacion.id_tipo_documento' => 'SELECT a.id_afiliacion FROM afiliacion a LEFT JOIN tipo_documento t ON a.id_tipo_documento = t.id_tipo_documento WHERE a.id_tipo_documento IS NOT NULL AND t.id_tipo_documento IS NULL LIMIT 10',
            'beneficiarios.id_tipo_documento' => 'SELECT b.cc_afiliado FROM beneficiarios b LEFT JOIN tipo_documento t ON b.id_tipo_documento = t.id_tipo_documento WHERE b.id_tipo_documento IS NOT NULL AND t.id_tipo_documento IS NULL LIMIT 10',
        ];

        foreach ($orphanChecks as $label => $sql) {
            $orphans = DB::select($sql);
            if ($orphans) {
                throw new RuntimeException("Huérfanos en {$label}: " . json_encode($orphans));
            }
        }

        foreach ($this->foreignKeys() as $fk) {
            if (!$this->hasForeignKey($fk['table'], $fk['key'])) {
                DB::statement("ALTER TABLE {$fk['table']} ADD CONSTRAINT {$fk['key']} FOREIGN KEY ({$fk['column']}) REFERENCES {$fk['references']} ({$fk['referenced_column']}) ON DELETE SET NULL");
            }
        }
    }

    public function down(): void
    {
        foreach ($this->foreignKeys() as $fk) {
            if ($this->hasForeignKey($fk['table'], $fk['key'])) {
                DB::statement("ALTER TABLE {$fk['table']} DROP FOREIGN KEY {$fk['key']}");
            }
        }
    }

    private function foreignKeys(): array
    {
        return [
            ['table' => 'empresas', 'key' => 'empresas_usuario_cedula_foreign', 'column' => 'usuario_cedula', 'references' => 'usuarios', 'referenced_column' => 'cedula'],
            ['table' => 'empresas', 'key' => 'empresas_licencia_id_foreign', 'column' => 'licencia_id', 'references' => 'licencias', 'referenced_column' => 'id'],
            ['table' => 'licencias', 'key' => 'licencias_usuario_cedula_foreign', 'column' => 'usuario_cedula', 'references' => 'usuarios', 'referenced_column' => 'cedula'],
            ['table' => 'afiliacion', 'key' => 'afiliacion_id_tipo_documento_foreign', 'column' => 'id_tipo_documento', 'references' => 'tipo_documento', 'referenced_column' => 'id_tipo_documento'],
            ['table' => 'beneficiarios', 'key' => 'beneficiarios_id_tipo_documento_foreign', 'column' => 'id_tipo_documento', 'references' => 'tipo_documento', 'referenced_column' => 'id_tipo_documento'],
        ];
    }

    private function hasForeignKey(string $table, string $name): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$table, $name, 'FOREIGN KEY']
        )->total > 0;
    }
};
