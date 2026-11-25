<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ($this->specs() as $spec) {
            if (!Schema::hasColumn($spec['table'], $spec['column'])) {
                Schema::table($spec['table'], function (Blueprint $table) use ($spec) {
                    $table->unsignedBigInteger($spec['column'])->nullable();
                });
            }
        }

        if (!Schema::hasColumn('beneficiarios', 'ocupacion_otra')) {
            Schema::table('beneficiarios', function (Blueprint $table) {
                $table->string('ocupacion_otra', 100)->nullable();
            });
        }

        foreach ($this->specs() as $spec) {
            if (!$this->hasForeignKey($spec['table'], $spec['fk'])) {
                DB::statement("ALTER TABLE {$spec['table']} ADD CONSTRAINT {$spec['fk']} FOREIGN KEY ({$spec['column']}) REFERENCES {$spec['catalog']} ({$spec['catalog_key']}) ON DELETE SET NULL");
            }
        }

        $this->backfill();
        $this->assertBackfill();
    }

    public function down(): void
    {
        foreach (array_reverse($this->specs()) as $spec) {
            if ($this->hasForeignKey($spec['table'], $spec['fk'])) {
                DB::statement("ALTER TABLE {$spec['table']} DROP FOREIGN KEY {$spec['fk']}");
            }
        }

        $columnasBeneficiario = array_values(array_filter(
            array_column($this->specs(), 'column'),
            fn (string $columna) => Schema::hasColumn('beneficiarios', $columna)
        ));

        if ($columnasBeneficiario) {
            Schema::table('beneficiarios', function (Blueprint $table) use ($columnasBeneficiario) {
                $table->dropColumn($columnasBeneficiario);
            });
        }

        if (Schema::hasColumn('afiliacion', 'id_parentesco')) {
            Schema::table('afiliacion', function (Blueprint $table) {
                $table->dropColumn('id_parentesco');
            });
        }

        if (Schema::hasColumn('beneficiarios', 'ocupacion_otra')) {
            Schema::table('beneficiarios', function (Blueprint $table) {
                $table->dropColumn('ocupacion_otra');
            });
        }
    }

    private function specs(): array
    {
        return [
            ['table' => 'beneficiarios', 'pk' => 'cc_afiliado', 'column' => 'id_genero', 'fk' => 'beneficiarios_id_genero_foreign', 'catalog' => 'generos', 'catalog_key' => 'id_genero', 'text' => 'genero'],
            ['table' => 'beneficiarios', 'pk' => 'cc_afiliado', 'column' => 'id_estado_civil', 'fk' => 'beneficiarios_id_estado_civil_foreign', 'catalog' => 'estados_civiles', 'catalog_key' => 'id_estado_civil', 'text' => 'estado_civil'],
            ['table' => 'beneficiarios', 'pk' => 'cc_afiliado', 'column' => 'id_nivel_educativo', 'fk' => 'beneficiarios_id_nivel_educativo_foreign', 'catalog' => 'niveles_educativos', 'catalog_key' => 'id_nivel_educativo', 'text' => 'nivel_educativo'],
            ['table' => 'beneficiarios', 'pk' => 'cc_afiliado', 'column' => 'id_ocupacion', 'fk' => 'beneficiarios_id_ocupacion_foreign', 'catalog' => 'ocupaciones', 'catalog_key' => 'id_ocupacion', 'text' => 'ocupacion'],
            ['table' => 'beneficiarios', 'pk' => 'cc_afiliado', 'column' => 'id_pais_nacionalidad', 'fk' => 'beneficiarios_id_pais_nacionalidad_foreign', 'catalog' => 'paises', 'catalog_key' => 'id_pais', 'text' => 'nacionalidad'],
            ['table' => 'beneficiarios', 'pk' => 'cc_afiliado', 'column' => 'id_pais_nacimiento', 'fk' => 'beneficiarios_id_pais_nacimiento_foreign', 'catalog' => 'paises', 'catalog_key' => 'id_pais', 'text' => 'pais_nacimiento'],
            ['table' => 'beneficiarios', 'pk' => 'cc_afiliado', 'column' => 'id_pais_expedicion', 'fk' => 'beneficiarios_id_pais_expedicion_foreign', 'catalog' => 'paises', 'catalog_key' => 'id_pais', 'text' => 'pais_expedicion'],
            ['table' => 'beneficiarios', 'pk' => 'cc_afiliado', 'column' => 'id_zona_residencia', 'fk' => 'beneficiarios_id_zona_residencia_foreign', 'catalog' => 'zonas_residencia', 'catalog_key' => 'id_zona_residencia', 'text' => 'zona_residencia'],
            ['table' => 'beneficiarios', 'pk' => 'cc_afiliado', 'column' => 'id_parentesco', 'fk' => 'beneficiarios_id_parentesco_foreign', 'catalog' => 'parentescos', 'catalog_key' => 'id_parentesco', 'text' => 'parentesco'],
            ['table' => 'afiliacion', 'pk' => 'id_afiliacion', 'column' => 'id_parentesco', 'fk' => 'afiliacion_id_parentesco_foreign', 'catalog' => 'parentescos', 'catalog_key' => 'id_parentesco', 'text' => 'parentesco'],
        ];
    }

    private function backfill(): void
    {
        foreach ($this->specs() as $spec) {
            if ($spec['column'] === 'id_ocupacion' || !Schema::hasColumn($spec['table'], $spec['text'])) {
                continue;
            }

            DB::statement("UPDATE {$spec['table']} t INNER JOIN {$spec['catalog']} c ON t.{$spec['text']} = c.nombre SET t.{$spec['column']} = c.{$spec['catalog_key']} WHERE t.{$spec['text']} IS NOT NULL AND t.{$spec['column']} IS NULL");
        }

        $otroId = DB::table('ocupaciones')->where('nombre', 'Otro')->value('id_ocupacion');

        if (!$otroId || !Schema::hasColumn('beneficiarios', 'ocupacion')) {
            return;
        }

        DB::statement(
            "UPDATE beneficiarios b LEFT JOIN ocupaciones c ON b.ocupacion = c.nombre SET b.id_ocupacion = COALESCE(c.id_ocupacion, ?), b.ocupacion_otra = CASE WHEN c.id_ocupacion IS NULL THEN b.ocupacion ELSE NULL END WHERE b.ocupacion IS NOT NULL AND b.id_ocupacion IS NULL",
            [$otroId]
        );
    }

    private function assertBackfill(): void
    {
        foreach ($this->specs() as $spec) {
            if ($spec['column'] === 'id_ocupacion' || !Schema::hasColumn($spec['table'], $spec['text'])) {
                continue;
            }

            $pendientes = DB::table($spec['table'])
                ->select($spec['pk'], $spec['text'])
                ->whereNotNull($spec['text'])
                ->whereNull($spec['column'])
                ->limit(5)
                ->get();

            if ($pendientes->isNotEmpty()) {
                throw new RuntimeException("Backfill pendiente en {$spec['table']}.{$spec['column']}: " . json_encode($pendientes));
            }
        }

        $pendientes = DB::table('beneficiarios')
            ->select('cc_afiliado', 'ocupacion')
            ->whereNotNull('ocupacion')
            ->whereNull('id_ocupacion')
            ->limit(5)
            ->get();

        if ($pendientes->isNotEmpty()) {
            throw new RuntimeException('Backfill pendiente en beneficiarios.id_ocupacion: ' . json_encode($pendientes));
        }
    }

    private function hasForeignKey(string $table, string $name): bool
    {
        return (int) DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = ?',
            [$table, $name, 'FOREIGN KEY']
        )->total > 0;
    }
};
