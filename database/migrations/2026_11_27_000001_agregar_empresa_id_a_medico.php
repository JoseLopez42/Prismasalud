<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 | REC-03 (A): vincular cada médico con su IPS (empresas.id).
 |
 | El vínculo es con la EMPRESA, no con una licencia concreta: una IPS puede
 | tener varias licencias históricas (compras/renovaciones) y solo una de ellas
 | es la vigente. Se agrega nullable porque REC-01 la hará obligatoria.
 |
 | Backfill conservador: solo se asigna empresa cuando TODAS las citas del
 | médico apuntan a una única empresa no nula. Si no tiene citas, hay citas sin
 | empresa o hay más de una empresa, queda en NULL y se completa en REC-01.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Columna nueva, opcional durante REC-03.
        if (! Schema::hasColumn('medico', 'empresa_id')) {
            Schema::table('medico', function (Blueprint $table) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('cedula')
                      ->comment('IPS/EPS a la que pertenece el médico (REC-03)');
            });
        }

        // 2. Backfill: evidencia única = una sola empresa en todas sus citas.
        //    SUM(empresa_id IS NULL) = 0 evita asignar por defecto cuando
        //    alguna cita no tiene empresa (registros anteriores a DBA-11).
        DB::statement('
            UPDATE medico m
            INNER JOIN (
                SELECT cedula_medico, MIN(empresa_id) AS empresa_id
                FROM cita
                GROUP BY cedula_medico
                HAVING COUNT(*) > 0
                   AND SUM(empresa_id IS NULL) = 0
                   AND COUNT(DISTINCT empresa_id) = 1
            ) evidencia ON evidencia.cedula_medico = m.cedula
            SET m.empresa_id = evidencia.empresa_id
        ');

        // 3. Llave foránea: no se puede borrar una empresa que tenga médicos.
        Schema::table('medico', function (Blueprint $table) {
            $table->foreign('empresa_id', 'medico_empresa_id_foreign')
                  ->references('id')->on('empresas')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        // Primero la FK (depende de la columna), luego la columna.
        Schema::table('medico', function (Blueprint $table) {
            $table->dropForeign('medico_empresa_id_foreign');
            $table->dropColumn('empresa_id');
        });
    }
};
