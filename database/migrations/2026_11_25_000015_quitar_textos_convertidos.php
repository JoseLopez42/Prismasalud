<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNAS_BENEFICIARIO = [
        'genero', 'estado_civil', 'nivel_educativo', 'ocupacion',
        'nacionalidad', 'pais_nacimiento', 'pais_expedicion',
        'zona_residencia', 'parentesco',
    ];

    public function up(): void
    {
        $beneficiarios = array_values(array_filter(
            self::COLUMNAS_BENEFICIARIO,
            fn (string $columna) => Schema::hasColumn('beneficiarios', $columna)
        ));

        if ($beneficiarios) {
            Schema::table('beneficiarios', function (Blueprint $table) use ($beneficiarios) {
                $table->dropColumn($beneficiarios);
            });
        }

        if (Schema::hasColumn('afiliacion', 'parentesco')) {
            Schema::table('afiliacion', function (Blueprint $table) {
                $table->dropColumn('parentesco');
            });
        }
    }

    public function down(): void
    {
        $beneficiarios = array_values(array_filter(
            self::COLUMNAS_BENEFICIARIO,
            fn (string $columna) => !Schema::hasColumn('beneficiarios', $columna)
        ));

        if ($beneficiarios) {
            Schema::table('beneficiarios', function (Blueprint $table) use ($beneficiarios) {
                foreach ($beneficiarios as $columna) {
                    match ($columna) {
                        'genero', 'zona_residencia' => $table->string($columna, 20)->nullable(),
                        'estado_civil', 'nivel_educativo', 'parentesco' => $table->string($columna, match ($columna) {
                            'parentesco' => 40,
                            default => 50,
                        })->nullable(),
                        default => $table->string($columna, 100)->nullable(),
                    };
                }
            });

            $this->restaurarTextosBeneficiario();
        }

        if (!Schema::hasColumn('afiliacion', 'parentesco')) {
            Schema::table('afiliacion', function (Blueprint $table) {
                $table->string('parentesco', 50)->nullable();
            });
        }

        $this->restaurarParentescoAfiliacion();
        DB::statement('ALTER TABLE afiliacion MODIFY parentesco VARCHAR(50) NOT NULL');
    }

    private function restaurarTextosBeneficiario(): void
    {
        $catalogos = [
            'genero' => ['generos', 'id_genero', 'id_genero'],
            'estado_civil' => ['estados_civiles', 'id_estado_civil', 'id_estado_civil'],
            'nivel_educativo' => ['niveles_educativos', 'id_nivel_educativo', 'id_nivel_educativo'],
            'ocupacion' => ['ocupaciones', 'id_ocupacion', 'id_ocupacion'],
            'nacionalidad' => ['paises', 'id_pais_nacionalidad', 'id_pais'],
            'pais_nacimiento' => ['paises', 'id_pais_nacimiento', 'id_pais'],
            'pais_expedicion' => ['paises', 'id_pais_expedicion', 'id_pais'],
            'zona_residencia' => ['zonas_residencia', 'id_zona_residencia', 'id_zona_residencia'],
            'parentesco' => ['parentescos', 'id_parentesco', 'id_parentesco'],
        ];

        foreach ($catalogos as $columna => [$catalogo, $columnaId, $llave]) {
            if (!Schema::hasColumn('beneficiarios', $columna) || !Schema::hasColumn('beneficiarios', $columnaId)) {
                continue;
            }

            DB::statement("UPDATE beneficiarios b INNER JOIN {$catalogo} c ON b.{$columnaId} = c.{$llave} SET b.{$columna} = c.nombre WHERE b.{$columna} IS NULL");
        }

        if (Schema::hasColumn('beneficiarios', 'ocupacion_otra')) {
            DB::statement("UPDATE beneficiarios SET ocupacion = ocupacion_otra WHERE ocupacion IS NULL AND ocupacion_otra IS NOT NULL");
        }
    }

    private function restaurarParentescoAfiliacion(): void
    {
        if (!Schema::hasColumn('afiliacion', 'id_parentesco')) {
            DB::statement("UPDATE afiliacion SET parentesco = 'Titular' WHERE parentesco IS NULL");

            return;
        }

        DB::statement("UPDATE afiliacion a LEFT JOIN parentescos p ON a.id_parentesco = p.id_parentesco SET a.parentesco = COALESCE(p.nombre, 'Titular')");
    }
};
