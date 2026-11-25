<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| DBA-03 (parte 2): auditoría de cambios en la historia clínica.
| Ley 2015 de 2020: toda modificación de la historia clínica debe quedar registrada.
|
| Cada UPDATE o DELETE sobre historia_clinica guarda en auditoria_clinica
| cómo estaba la fila ANTES del cambio, quién lo hizo y cuándo.
| Los triggers lo hacen solos: no depende de que el código PHP se acuerde.
|
| auditoria_clinica es de SOLO AGREGAR: otros dos triggers rechazan cualquier
| UPDATE o DELETE sobre ella, para que nadie pueda borrar el rastro.
|
| Ejecutar SOLO esta migración, con la conexión de migraciones:
|   php artisan migrate --database=migraciones --path=database/migrations/2026_11_25_104404_create_auditoria_clinica_table.php
*/
return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabla donde se guarda el historial de cambios.
        //    No lleva created_at/updated_at: un registro de auditoría nunca se modifica.
        Schema::create('auditoria_clinica', function (Blueprint $table) {
            $table->id();
            $table->string('tabla', 64);                           // tabla que se cambió
            $table->unsignedBigInteger('id_registro');             // id de la fila cambiada
            $table->enum('accion', ['UPDATE', 'DELETE']);          // qué se hizo
            $table->json('valores_antes');                         // la fila completa ANTES del cambio
            $table->string('usuario_bd', 100);                     // usuario de MariaDB (ej. prisma_app@localhost)
            $table->unsignedBigInteger('usuario_app')->nullable(); // cédula de la persona en la app (la envía Laravel)
            $table->dateTime('fecha')->useCurrent();               // cuándo pasó

            // Para buscar rápido "todo lo que le pasó a la historia X"
            $table->index(['tabla', 'id_registro']);
        });

        // 2. Trigger: en cada UPDATE copia la versión vieja de la fila.
        //    OLD.columna = el valor que tenía la fila antes del cambio.
        //    USER() = el usuario de MariaDB que hizo el cambio.
        //    @usuario_app = variable que Laravel llena con la cédula de la sesión (NULL si nadie la llenó).
        DB::unprepared("
            CREATE TRIGGER trg_auditoria_hc_update
            AFTER UPDATE ON historia_clinica
            FOR EACH ROW
            INSERT INTO auditoria_clinica
                (tabla, id_registro, accion, valores_antes, usuario_bd, usuario_app, fecha)
            VALUES (
                'historia_clinica', OLD.id, 'UPDATE',
                JSON_OBJECT(
                    'empresa_id',      OLD.empresa_id,
                    'cedula_paciente', OLD.cedula_paciente,
                    'cedula_medico',   OLD.cedula_medico,
                    'id_cita',         OLD.id_cita,
                    'fecha',           OLD.fecha,
                    'motivo_consulta', OLD.motivo_consulta,
                    'antecedentes',    OLD.antecedentes,
                    'examen_fisico',   OLD.examen_fisico,
                    'observaciones',   OLD.observaciones,
                    'activo',          OLD.activo,
                    'created_at',      OLD.created_at,
                    'updated_at',      OLD.updated_at
                ),
                USER(), @usuario_app, NOW()
            )
        ");

        // 3. Trigger: si alguien borra una historia, queda la copia completa.
        DB::unprepared("
            CREATE TRIGGER trg_auditoria_hc_delete
            AFTER DELETE ON historia_clinica
            FOR EACH ROW
            INSERT INTO auditoria_clinica
                (tabla, id_registro, accion, valores_antes, usuario_bd, usuario_app, fecha)
            VALUES (
                'historia_clinica', OLD.id, 'DELETE',
                JSON_OBJECT(
                    'empresa_id',      OLD.empresa_id,
                    'cedula_paciente', OLD.cedula_paciente,
                    'cedula_medico',   OLD.cedula_medico,
                    'id_cita',         OLD.id_cita,
                    'fecha',           OLD.fecha,
                    'motivo_consulta', OLD.motivo_consulta,
                    'antecedentes',    OLD.antecedentes,
                    'examen_fisico',   OLD.examen_fisico,
                    'observaciones',   OLD.observaciones,
                    'activo',          OLD.activo,
                    'created_at',      OLD.created_at,
                    'updated_at',      OLD.updated_at
                ),
                USER(), @usuario_app, NOW()
            )
        ");

        // 4. Proteger la auditoría: nadie puede modificar ni borrar sus registros.
        //    SIGNAL SQLSTATE '45000' lanza un error propio y cancela la operación.
        DB::unprepared("
            CREATE TRIGGER trg_auditoria_clinica_no_update
            BEFORE UPDATE ON auditoria_clinica
            FOR EACH ROW
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'auditoria_clinica es de solo lectura: sus registros no se pueden modificar (Ley 2015 de 2020)'
        ");

        DB::unprepared("
            CREATE TRIGGER trg_auditoria_clinica_no_delete
            BEFORE DELETE ON auditoria_clinica
            FOR EACH ROW
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'auditoria_clinica es de solo lectura: sus registros no se pueden borrar (Ley 2015 de 2020)'
        ");
    }

    public function down(): void
    {
        // Orden inverso: primero los triggers, luego la tabla.
        DB::unprepared('DROP TRIGGER IF EXISTS trg_auditoria_clinica_no_delete');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_auditoria_clinica_no_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_auditoria_hc_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_auditoria_hc_delete');
        Schema::dropIfExists('auditoria_clinica');
    }
};