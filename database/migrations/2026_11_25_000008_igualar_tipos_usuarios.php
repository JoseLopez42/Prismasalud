<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $nitOrphans = DB::select('SELECT u.cedula, u.nit_eps FROM usuarios u LEFT JOIN eps e ON CAST(u.nit_eps AS CHAR) = e.nit_eps WHERE u.nit_eps IS NOT NULL AND e.nit_eps IS NULL LIMIT 10');
        if ($nitOrphans) {
            throw new RuntimeException('usuarios.nit_eps huérfanos: ' . json_encode($nitOrphans));
        }

        $ciudadOrphans = DB::select('SELECT u.cedula, u.id_ciudad FROM usuarios u LEFT JOIN ciudades c ON u.id_ciudad = c.id_ciudad WHERE u.id_ciudad IS NOT NULL AND c.id_ciudad IS NULL LIMIT 10');
        if ($ciudadOrphans) {
            throw new RuntimeException('usuarios.id_ciudad huérfanos: ' . json_encode($ciudadOrphans));
        }

        $tipoDocumentoOrphans = DB::select('SELECT u.cedula, u.id_tipo_documento FROM usuarios u LEFT JOIN tipo_documento t ON u.id_tipo_documento = t.id_tipo_documento WHERE u.id_tipo_documento IS NOT NULL AND t.id_tipo_documento IS NULL LIMIT 10');
        if ($tipoDocumentoOrphans) {
            throw new RuntimeException('usuarios.id_tipo_documento huérfanos: ' . json_encode($tipoDocumentoOrphans));
        }

        DB::statement('ALTER TABLE usuarios MODIFY COLUMN nit_eps VARCHAR(20) NULL');
        DB::statement('ALTER TABLE usuarios MODIFY COLUMN id_ciudad BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE usuarios MODIFY COLUMN id_tipo_documento BIGINT UNSIGNED NULL');

        if (!$this->hasForeignKey('usuarios', 'usuarios_nit_eps_foreign')) {
            DB::statement('ALTER TABLE usuarios ADD CONSTRAINT usuarios_nit_eps_foreign FOREIGN KEY (nit_eps) REFERENCES eps (nit_eps) ON DELETE SET NULL');
        }
        if (!$this->hasForeignKey('usuarios', 'usuarios_id_ciudad_foreign')) {
            DB::statement('ALTER TABLE usuarios ADD CONSTRAINT usuarios_id_ciudad_foreign FOREIGN KEY (id_ciudad) REFERENCES ciudades (id_ciudad) ON DELETE SET NULL');
        }
        if (!$this->hasForeignKey('usuarios', 'usuarios_id_tipo_documento_foreign')) {
            DB::statement('ALTER TABLE usuarios ADD CONSTRAINT usuarios_id_tipo_documento_foreign FOREIGN KEY (id_tipo_documento) REFERENCES tipo_documento (id_tipo_documento) ON DELETE SET NULL');
        }
    }

    public function down(): void
    {
        if ($this->hasForeignKey('usuarios', 'usuarios_nit_eps_foreign')) {
            DB::statement('ALTER TABLE usuarios DROP FOREIGN KEY usuarios_nit_eps_foreign');
        }
        if ($this->hasForeignKey('usuarios', 'usuarios_id_ciudad_foreign')) {
            DB::statement('ALTER TABLE usuarios DROP FOREIGN KEY usuarios_id_ciudad_foreign');
        }
        if ($this->hasForeignKey('usuarios', 'usuarios_id_tipo_documento_foreign')) {
            DB::statement('ALTER TABLE usuarios DROP FOREIGN KEY usuarios_id_tipo_documento_foreign');
        }

        DB::statement('ALTER TABLE usuarios MODIFY COLUMN nit_eps INT UNSIGNED NULL');
        DB::statement('ALTER TABLE usuarios MODIFY COLUMN id_ciudad INT UNSIGNED NULL');
        DB::statement('ALTER TABLE usuarios MODIFY COLUMN id_tipo_documento INT UNSIGNED NULL');
    }

    private function hasForeignKey(string $table, string $name): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$table, $name, 'FOREIGN KEY']
        )->total > 0;
    }
};
