<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 | REC-03 (B): vincular Pacientes y Farmacéuticos con su IPS (empresas.id).
 |
 | Se guarda en usuarios.empresa_id (no en usuarios.licencia_id) porque la
 | licencia cambia en cada renovación. REC-01 usará esta misma columna y la
 | volverá obligatoria.
 |
 | Backfill conservador: la empresa debe ser única en TODA la evidencia
 | disponible para ese usuario:
 |   - Paciente (tipo 2): citas por cedula_usuario + prescripciones por
 |     cedula_paciente.
 |   - Farmacéutico (tipo 4): prescripciones por cedula_farmaceuta.
 | Si no hay evidencia, hay empresas nulas o hay más de una empresa, queda NULL.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Columna nueva, opcional durante REC-03.
        if (! Schema::hasColumn('usuarios', 'empresa_id')) {
            Schema::table('usuarios', function (Blueprint $table) {
                $table->unsignedBigInteger('empresa_id')->nullable()->after('licencia_id')
                      ->comment('IPS/EPS a la que pertenece el usuario (REC-03)');
            });
        }

        // 2. Backfill. Se usa una tabla temporal porque MySQL/MariaDB no permite
        //    UPDATE sobre "usuarios" leyendo de "usuarios" en el mismo subquery.
        DB::statement('DROP TEMPORARY TABLE IF EXISTS tmp_rec03_usuarios_empresa');

        DB::statement('
            CREATE TEMPORARY TABLE tmp_rec03_usuarios_empresa AS
            SELECT usuario, MIN(empresa_id) AS empresa_id
            FROM (
                SELECT u.cedula AS usuario, c.empresa_id AS empresa_id
                FROM usuarios u
                INNER JOIN cita c ON c.cedula_usuario = u.cedula
                WHERE u.tipo_usuario_id = 2

                UNION ALL

                SELECT u.cedula AS usuario, p.empresa_id AS empresa_id
                FROM usuarios u
                INNER JOIN prescripcion p ON p.cedula_paciente = u.cedula
                WHERE u.tipo_usuario_id = 2

                UNION ALL

                SELECT u.cedula AS usuario, p.empresa_id AS empresa_id
                FROM usuarios u
                INNER JOIN prescripcion p ON p.cedula_farmaceuta = u.cedula
                WHERE u.tipo_usuario_id = 4
            ) evidencia
            GROUP BY usuario
            HAVING COUNT(*) > 0
               AND SUM(empresa_id IS NULL) = 0
               AND COUNT(DISTINCT empresa_id) = 1
        ');

        DB::statement('
            UPDATE usuarios u
            INNER JOIN tmp_rec03_usuarios_empresa ev ON ev.usuario = u.cedula
            SET u.empresa_id = ev.empresa_id
            WHERE u.tipo_usuario_id IN (2, 4)
        ');

        DB::statement('DROP TEMPORARY TABLE IF EXISTS tmp_rec03_usuarios_empresa');

        // 3. Llave foránea: no se puede borrar una empresa que tenga usuarios.
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreign('empresa_id', 'usuarios_empresa_id_foreign')
                  ->references('id')->on('empresas')
                  ->restrictOnDelete()
                  ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        // Primero la FK (depende de la columna), luego la columna.
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign('usuarios_empresa_id_foreign');
            $table->dropColumn('empresa_id');
        });
    }
};
