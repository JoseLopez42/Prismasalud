<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNAS = [
        'id_tipo_documento', 'pais_expedicion', 'genero', 'estado_civil',
        'grupo_etnico', 'tiene_discapacidad', 'tipo_discapacidad',
        'nacionalidad', 'pais_nacimiento', 'zona_residencia', 'telefono_fijo',
        'estrato', 'nivel_educativo', 'ocupacion', 'tipo_sangre',
        'victima_conflicto_armado', 'numero_afiliacion_anterior',
    ];

    public function up(): void
    {
        $this->copiarDirecto('id_tipo_documento', 'id_tipo_documento');
        $this->copiarDirecto('telefono_fijo', 'telefono_fijo');
        $this->copiarDirecto('estrato', 'estrato');
        $this->copiarDirecto('numero_afiliacion_anterior', 'numero_afiliacion_anterior');

        $this->copiarCatalogo('pais_expedicion', 'paises', 'id_pais_expedicion');
        $this->copiarCatalogo('genero', 'generos', 'id_genero');
        $this->copiarCatalogo('estado_civil', 'estados_civiles', 'id_estado_civil');
        $this->copiarCatalogo('nacionalidad', 'paises', 'id_pais_nacionalidad');
        $this->copiarCatalogo('pais_nacimiento', 'paises', 'id_pais_nacimiento');
        $this->copiarCatalogo('zona_residencia', 'zonas_residencia', 'id_zona_residencia');
        $this->copiarCatalogo('nivel_educativo', 'niveles_educativos', 'id_nivel_educativo');
        $this->copiarOcupacion();

        if ($this->hasForeignKey('afiliacion', 'afiliacion_id_tipo_documento_foreign')) {
            DB::statement('ALTER TABLE afiliacion DROP FOREIGN KEY afiliacion_id_tipo_documento_foreign');
        }

        if ($this->hasCheck('afiliacion', 'chk_afiliacion_estrato')) {
            DB::statement('ALTER TABLE afiliacion DROP CONSTRAINT chk_afiliacion_estrato');
        }

        $columnas = array_values(array_filter(
            self::COLUMNAS,
            fn (string $columna) => Schema::hasColumn('afiliacion', $columna)
        ));

        if ($columnas) {
            Schema::table('afiliacion', function (Blueprint $table) use ($columnas) {
                $table->dropColumn($columnas);
            });
        }
    }

    public function down(): void
    {
        $columnas = array_values(array_filter(
            self::COLUMNAS,
            fn (string $columna) => !Schema::hasColumn('afiliacion', $columna)
        ));

        if ($columnas) {
            Schema::table('afiliacion', function (Blueprint $table) use ($columnas) {
                foreach ($columnas as $columna) {
                    match ($columna) {
                        'id_tipo_documento' => $table->unsignedBigInteger($columna)->nullable(),
                        'pais_expedicion', 'genero', 'estado_civil', 'grupo_etnico', 'nacionalidad', 'pais_nacimiento', 'zona_residencia', 'nivel_educativo', 'ocupacion' => $table->string($columna, match ($columna) {
                            'genero', 'zona_residencia' => 20,
                            'estado_civil', 'grupo_etnico', 'nacionalidad', 'pais_nacimiento', 'nivel_educativo' => 50,
                            default => 100,
                        })->nullable(),
                        'tiene_discapacidad', 'victima_conflicto_armado' => $table->boolean($columna)->default(false),
                        'tipo_discapacidad' => $table->string($columna, 100)->nullable(),
                        'telefono_fijo' => $table->string($columna, 20)->nullable(),
                        'estrato' => $table->unsignedTinyInteger($columna)->nullable(),
                        'tipo_sangre' => $table->string($columna, 5)->nullable(),
                        'numero_afiliacion_anterior' => $table->string($columna, 50)->nullable(),
                    };
                }
            });
        }

        if (!$this->hasCheck('afiliacion', 'chk_afiliacion_estrato')) {
            DB::statement('ALTER TABLE afiliacion ADD CONSTRAINT chk_afiliacion_estrato CHECK (estrato IS NULL OR estrato BETWEEN 1 AND 6)');
        }

        if (
            !$this->hasForeignKey('afiliacion', 'afiliacion_id_tipo_documento_foreign')
            && Schema::hasColumn('afiliacion', 'id_tipo_documento')
        ) {
            DB::statement('ALTER TABLE afiliacion ADD CONSTRAINT afiliacion_id_tipo_documento_foreign FOREIGN KEY (id_tipo_documento) REFERENCES tipo_documento (id_tipo_documento) ON DELETE SET NULL');
        }
    }

    private function copiarDirecto(string $from, string $to): void
    {
        if (!Schema::hasColumn('afiliacion', $from) || !Schema::hasColumn('beneficiarios', $to)) {
            return;
        }

        DB::statement("UPDATE beneficiarios b INNER JOIN afiliacion a ON a.cc_afiliado = b.cc_afiliado SET b.{$to} = a.{$from} WHERE b.{$to} IS NULL AND a.{$from} IS NOT NULL");
    }

    private function copiarCatalogo(string $from, string $catalogo, string $to): void
    {
        if (!Schema::hasColumn('afiliacion', $from) || !Schema::hasColumn('beneficiarios', $to)) {
            return;
        }

        $llave = match ($catalogo) {
            'paises' => 'id_pais',
            'generos' => 'id_genero',
            'estados_civiles' => 'id_estado_civil',
            'zonas_residencia' => 'id_zona_residencia',
            'niveles_educativos' => 'id_nivel_educativo',
        };

        DB::statement("UPDATE beneficiarios b INNER JOIN afiliacion a ON a.cc_afiliado = b.cc_afiliado INNER JOIN {$catalogo} c ON a.{$from} = c.nombre SET b.{$to} = c.{$llave} WHERE b.{$to} IS NULL AND a.{$from} IS NOT NULL");
    }

    private function copiarOcupacion(): void
    {
        if (!Schema::hasColumn('afiliacion', 'ocupacion') || !Schema::hasColumn('beneficiarios', 'id_ocupacion')) {
            return;
        }

        $otroId = DB::table('ocupaciones')->where('nombre', 'Otro')->value('id_ocupacion');

        if (!$otroId) {
            throw new RuntimeException('No existe la ocupación Otro en el catálogo.');
        }

        DB::statement(
            "UPDATE beneficiarios b INNER JOIN afiliacion a ON a.cc_afiliado = b.cc_afiliado LEFT JOIN ocupaciones c ON a.ocupacion = c.nombre SET b.id_ocupacion = COALESCE(c.id_ocupacion, ?), b.ocupacion_otra = CASE WHEN c.id_ocupacion IS NULL AND b.ocupacion_otra IS NULL THEN a.ocupacion ELSE b.ocupacion_otra END WHERE a.ocupacion IS NOT NULL AND b.id_ocupacion IS NULL",
            [$otroId]
        );
    }

    private function hasForeignKey(string $table, string $name): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$table, $name, 'FOREIGN KEY']
        )->total > 0;
    }

    private function hasCheck(string $table, string $name): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$table, $name, 'CHECK']
        )->total > 0;
    }
};
