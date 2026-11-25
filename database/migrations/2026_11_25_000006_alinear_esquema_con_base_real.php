<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->modifyIfNeeded('licencias', 'nombre_ips', 'varchar(255)', false, null);
        $this->modifyIfNeeded('licencias', 'tipo_entidad', 'varchar(30)', true, null);
        $this->modifyIfNeeded('licencias', 'representante_legal', 'varchar(150)', true, null);
        $this->modifyIfNeeded('licencias', 'correo_ips', 'varchar(150)', true, null);
        $this->modifyIfNeeded('licencias', 'telefono_ips', 'varchar(20)', true, null);
        $this->modifyIfNeeded('licencias', 'contacto_nombre', 'varchar(255)', true, null);
        $this->modifyIfNeeded('licencias', 'contacto_email', 'varchar(255)', true, null);
        $this->modifyIfNeeded('licencias', 'plan', 'varchar(255)', false, null);
        $this->modifyIfNeeded('licencias', 'fecha_inicio', 'date', false, null);
        $this->modifyIfNeeded('licencias', 'fecha_fin', 'date', false, null);
        $this->modifyIfNeeded('licencias', 'estado', 'varchar(255)', false, "'activa'");

        $this->addForeignKeyIfNeeded('licencias', 'licencias_plan_id_foreign', 'plan_id', 'planes', 'id');
        $this->addForeignKeyIfNeeded('usuarios', 'usuarios_licencia_id_foreign', 'licencia_id', 'licencias', 'id');

        $id = $this->columnInfo('users', 'id');
        if ($id && ($id->DATA_TYPE !== 'bigint' || stripos($id->COLUMN_TYPE, 'unsigned') === false || $id->IS_NULLABLE !== 'NO' || stripos($id->EXTRA, 'auto_increment') !== false)) {
            DB::statement('ALTER TABLE users MODIFY COLUMN id BIGINT UNSIGNED NOT NULL');
        }

        $rol = $this->columnInfo('users', 'rol');
        $password = $this->columnInfo('users', 'password');
        $rolPosition = $rol && $password ? (int) $password->ORDINAL_POSITION + 1 : null;
        if ($rol && ($rol->COLUMN_TYPE !== 'varchar(30)' || $rol->IS_NULLABLE !== 'NO' || $rol->COLUMN_DEFAULT !== "'superadmin'" || ($rolPosition !== null && (int) $rol->ORDINAL_POSITION !== $rolPosition))) {
            DB::statement("ALTER TABLE users MODIFY COLUMN rol VARCHAR(30) NOT NULL DEFAULT 'superadmin' AFTER password");
        }

        $invalid = DB::select('SELECT id FROM planes WHERE NOT JSON_VALID(beneficios) OR NOT JSON_VALID(no_incluye)');
        if (count($invalid) > 0) {
            $ids = implode(', ', array_map(fn ($row) => (string) $row->id, $invalid));
            throw new RuntimeException("Hay filas en planes con JSON inválido: {$ids}");
        }

        $this->makeJsonNullable('planes', 'beneficios');
        $this->makeJsonNullable('planes', 'no_incluye');
    }

    public function down(): void
    {
        // Alineación de esquema: no se revierte
    }

    private function columnInfo(string $table, string $column): ?object
    {
        return DB::selectOne(
            'SELECT COLUMN_TYPE, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT, EXTRA, COLLATION_NAME, ORDINAL_POSITION
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, $column]
        );
    }

    private function modifyIfNeeded(string $table, string $column, string $type, bool $nullable, ?string $default, string $extra = ''): void
    {
        $info = $this->columnInfo($table, $column);
        if (!$info) {
            return;
        }

        $actualNullable = $info->IS_NULLABLE === 'YES';
        if ($info->COLUMN_TYPE === $type && $actualNullable === $nullable && $info->COLUMN_DEFAULT === $default) {
            return;
        }

        $sql = "ALTER TABLE {$table} MODIFY COLUMN {$column} {$type} " . ($nullable ? 'NULL' : 'NOT NULL');
        if ($default !== null) {
            $sql .= " DEFAULT {$default}";
        }
        if ($extra !== '') {
            $sql .= ' ' . $extra;
        }

        DB::statement($sql);
    }

    private function hasConstraint(string $table, string $name, string $type): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$table, $name, $type]
        )->total > 0;
    }

    private function hasIndex(string $table, string $name): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?',
            [$table, $name]
        )->total > 0;
    }

    private function addForeignKeyIfNeeded(string $table, string $name, string $column, string $referencedTable, string $referencedColumn): void
    {
        if ($this->hasConstraint($table, $name, 'FOREIGN KEY')) {
            return;
        }

        if (!$this->hasIndex($table, $name)) {
            DB::statement("ALTER TABLE {$table} ADD INDEX {$name} ({$column})");
        }

        DB::statement("ALTER TABLE {$table} ADD CONSTRAINT {$name} FOREIGN KEY ({$column}) REFERENCES {$referencedTable} ({$referencedColumn}) ON DELETE SET NULL");
    }

    private function makeJsonNullable(string $table, string $column): void
    {
        $info = $this->columnInfo($table, $column);
        $hasCheck = (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.CHECK_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$table, $column]
        )->total > 0;

        $needsModify = $info && ($info->COLUMN_TYPE !== 'longtext' || $info->COLLATION_NAME !== 'utf8mb4_bin' || $info->IS_NULLABLE !== 'YES');
        if (!$needsModify && $hasCheck) {
            return;
        }

        $parts = [];
        if ($needsModify) {
            $parts[] = "MODIFY COLUMN {$column} LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL";
        }
        if (!$hasCheck) {
            $parts[] = "ADD CONSTRAINT {$column} CHECK (json_valid({$column}))";
        }

        if ($parts) {
            DB::statement("ALTER TABLE {$table} " . implode(', ', $parts));
        }
    }
};
